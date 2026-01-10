<?php

namespace Tests\Unit\Concurrency;

use App\Models\User;
use App\Models\Relationship;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class RelationshipConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function relationship_uniqueness_under_concurrency()
    {
        $userA = User::create([
            'username' => 'user_a',
            'age' => 25,
        ]);

        $userB = User::create([
            'username' => 'user_b',
            'age' => 30,
        ]);

        // First insert (simulates request 1)
        Relationship::create([
            'user_id' => $userA->id,
            'friend_id' => $userB->id,
        ]);

        // Second insert (simulates concurrent request)
        try {
            Relationship::create([
                'user_id' => $userA->id,
                'friend_id' => $userB->id,
            ]);

            $this->fail('Duplicate relationship was allowed');
        } catch (QueryException $e) {
            $this->assertTrue(true); // Unique constraint worked
        }

        $this->assertEquals(1, Relationship::count());
    }
}
