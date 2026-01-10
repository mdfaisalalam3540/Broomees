<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ApiToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OptimisticLockingTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate(User $user): array
    {
        $plainToken = Str::random(64);

        ApiToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $plainToken),
            'name' => 'Test Token',
            'abilities' => ['*'],
            'expires_at' => now()->addHour(),
        ]);

        return [
            'Authorization' => 'Bearer ' . $plainToken,
            'Accept' => 'application/json',
        ];
    }

    /** @test */
    public function optimistic_locking_conflict()
    {
        $user = User::create([
            'username' => 'conflict_user',
            'age' => 25,
            'version' => 1,
        ]);

        $headers = $this->authenticate($user);

        $response = $this->put(
            "/api/users/{$user->id}",
            [
                'username' => 'updated_name',
                'version' => 0, // ❌ OLD VERSION
            ],
            $headers
        );

        $response->assertStatus(409);
        $this->assertStringContainsString(
            'modified',
            $response->json('error')
        );
    }

    /** @test */
    public function optimistic_locking_success_with_correct_version()
    {
        $user = User::create([
            'username' => 'success_user',
            'age' => 30,
            'version' => 0,
        ]);

        $headers = $this->authenticate($user);

        $response = $this->put(
            "/api/users/{$user->id}",
            [
                'username' => 'updated_successfully',
                'version' => 0, // ✅ CORRECT VERSION
            ],
            $headers
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'username' => 'updated_successfully',
            'version' => 1, // incremented by middleware
        ]);
    }
}
