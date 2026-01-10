<?php
namespace App\Services;

use App\Models\User;
use App\Models\Relationship;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class RelationshipService
{
    public function createRelationship(User $user, User $friend): void
    {
        DB::transaction(function () use ($user, $friend) {
            // Prevent self-relationship
            if ($user->id === $friend->id) {
                throw new \InvalidArgumentException('Cannot create relationship with self');
            }

            // Check if relationship already exists
            $exists = Relationship::where(function ($query) use ($user, $friend) {
                $query->where('user_id', $user->id)
                      ->where('friend_id', $friend->id);
            })->orWhere(function ($query) use ($user, $friend) {
                $query->where('user_id', $friend->id)
                      ->where('friend_id', $user->id);
            })->exists();

            if ($exists) {
                throw new \InvalidArgumentException('Relationship already exists');
            }

            // Create mutual relationships
            Relationship::create([
                'user_id' => $user->id,
                'friend_id' => $friend->id
            ]);

            // Create reverse relationship (mutual)
            Relationship::create([
                'user_id' => $friend->id,
                'friend_id' => $user->id
            ]);
        });
    }

    public function deleteRelationship(User $user, User $friend): void
    {
        DB::transaction(function () use ($user, $friend) {
            // Delete both directions
            Relationship::where('user_id', $user->id)
                ->where('friend_id', $friend->id)
                ->delete();

            Relationship::where('user_id', $friend->id)
                ->where('friend_id', $user->id)
                ->delete();
        });
    }
}