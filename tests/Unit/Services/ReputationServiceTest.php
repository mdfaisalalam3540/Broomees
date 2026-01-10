<?php
namespace Tests\Unit\Services;

use App\Models\Hobby;
use App\Models\Relationship;
use App\Models\User;
use App\Services\ReputationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReputationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReputationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReputationService();
    }

    /** @test */
    public function reputation_score_calculation()
    {
        // Create users
        $user = User::factory()->create([
            'created_at' => Carbon::now()->subDays(60), // 60 days old → age score = 2
        ]);

        $friend1 = User::factory()->create();
        $friend2 = User::factory()->create();

        // Create friendships
        Relationship::create([
            'user_id' => $user->id,
            'friend_id' => $friend1->id,
        ]);

        Relationship::create([
            'user_id' => $user->id,
            'friend_id' => $friend2->id,
        ]);

        // Create hobbies
        $hobbyA = Hobby::create(['name' => 'Chess']);
        $hobbyB = Hobby::create(['name' => 'Music']);
        $hobbyC = Hobby::create(['name' => 'Coding']); // not shared

        // Attach hobbies to user
        $user->hobbies()->attach([
            $hobbyA->id,
            $hobbyB->id,
            $hobbyC->id,
        ]);

        // Attach shared hobbies to friends
        $friend1->hobbies()->attach([$hobbyA->id]);
        $friend2->hobbies()->attach([$hobbyB->id]);

        /*
         Expected calculation:
         - Unique friends = 2
         - Shared hobbies = 2 (Chess, Music)
         - Shared hobbies score = 2 × 0.5 = 1
         - Account age score = floor(60 / 30) = 2
         - Blocked score = 0

         TOTAL = 2 + 1 + 2 = 5
        */

        $score = $this->service->calculateReputation($user);

        $this->assertEquals(5.0, $score);
        $this->assertEquals(5.0, $user->fresh()->reputation_score);
    }

    /** @test */
    public function account_age_score_is_capped_at_3()
    {
        $user = User::factory()->create([
            'created_at' => Carbon::now()->subDays(365), // ~12 months
        ]);

        $score = $this->service->calculateReputation($user);

        /*
         - No friends
         - No hobbies
         - Account age score = min(floor(365 / 30), 3) = 3
        */

        $this->assertEquals(3.0, $score);
    }
}
