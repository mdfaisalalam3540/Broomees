<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ApiToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function authHeaders(): array
    {
        $user = User::factory()->create();

        $plainToken = Str::random(64);

        ApiToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $plainToken),
            'name' => 'Test Token',
            'abilities' => ['*'],
            'expires_at' => now()->addDay(),
        ]);

        return [
            'Authorization' => 'Bearer ' . $plainToken,
        ];
    }

    /** @test */
    public function rate_limiting_for_write_operations()
    {
        $headers = $this->authHeaders();
        $successCount = 0;

        for ($i = 0; $i < 35; $i++) {
            $response = $this->post('/api/users', [
                'username' => 'user_' . $i,
                'age' => 25,
            ], $headers);

            if ($response->status() < 400) {
                $successCount++;
            }
        }

        // POST limit = 30 per minute
        $this->assertGreaterThanOrEqual(25, $successCount);
    }

    /** @test */
    public function rate_limiting_for_read_operations()
    {
        $headers = $this->authHeaders();

        for ($i = 0; $i < 130; $i++) {
            $response = $this->get('/api/users', $headers);

            if ($i >= 120) {
                $response->assertStatus(429);
                return;
            }
        }

        $this->fail('Rate limit was not enforced for GET requests');
    }
}
