<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ApiToken;
use App\Models\Hobby;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_user_relationship_flow()
    {
        // Create users
        $user1 = User::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'username' => 'user1',
            'password' => bcrypt('password123'),
            'age' => 25,
            'reputation_score' => 0,
            'version' => 0,
        ]);

        $user2 = User::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'username' => 'user2',
            'password' => bcrypt('password123'),
            'age' => 30,
            'reputation_score' => 0,
            'version' => 0,
        ]);

        // Create token for user1
        $plainToken = 'test_token_flow_' . time();
        ApiToken::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'user_id' => $user1->id,
            'token' => Hash::make($plainToken),
            'name' => 'Test Token',
            'abilities' => ['*'],
            'expires_at' => now()->addDay(),
        ]);

        $headers = ['Authorization' => 'Bearer ' . $plainToken];

        // 1. Create relationship
        $response = $this->post("/api/users/{$user1->id}/relationships", [
            'friend_id' => $user2->id
        ], $headers);

        $response->assertStatus(201);

        // 2. Check user1 has friend
        $response = $this->get("/api/users/{$user1->id}", $headers);
        $response->assertStatus(200);
        $friends = $response->json()['friends'];
        $this->assertContains($user2->id, $friends);

        // 3. Create hobby
        $hobby = Hobby::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'name' => 'Test Hobby'
        ]);

        // 4. Add hobby to user1
        $response = $this->post("/api/users/{$user1->id}/hobbies", [
            'hobby_id' => $hobby->id
        ], $headers);

        $response->assertStatus(201);

        // 5. Check reputation increased
        $response = $this->get("/api/users/{$user1->id}", $headers);
        $reputation = $response->json()['reputationScore'];
        $this->assertGreaterThan(0, $reputation);

        // 6. Delete relationship
        $response = $this->delete("/api/users/{$user1->id}/relationships", [
            'friend_id' => $user2->id
        ], $headers);

        $response->assertStatus(200);

        // 7. Check reputation decreased
        $response = $this->get("/api/users/{$user1->id}", $headers);
        $newReputation = $response->json()['reputationScore'];
        $this->assertLessThan($reputation, $newReputation);
    }
}