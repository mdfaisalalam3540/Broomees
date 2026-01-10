<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReputationService
{
    public function calculateReputation(User $user): float
    {
        return DB::transaction(function () use ($user) {

            /**
             * 1️⃣ Unique friends
             */
            $uniqueFriends = $user->allFriends()
                ->unique('id')
                ->count();

            /**
             * 2️⃣ Shared hobbies (unique)
             */
            $userHobbyIds = $user->hobbies()
                ->pluck('hobby_id')
                ->unique()
                ->toArray();

            $sharedHobbyIds = collect();

            foreach ($user->allFriends() as $friend) {
                $friendHobbies = $friend->hobbies()
                    ->pluck('hobby_id');
                $sharedHobbyIds = $sharedHobbyIds->merge(
                    $friendHobbies->intersect($userHobbyIds)
                );
            }

            $sharedHobbiesCount = $sharedHobbyIds->unique()->count();
            $sharedHobbiesScore = $sharedHobbiesCount * 0.5;

            /**
             * 3️⃣ Account age score (days ÷ 30, max 3)
             */
            $daysOld = $user->created_at
                ? $user->created_at->diffInDays(Carbon::now())
                : 0;

            $accountAgeScore = min(
                floor($daysOld / 30),
                3
            );

            /**
             * 4️⃣ Blocked relationships (currently none)
             */
            $blockedScore = 0; // placeholder for future logic

            /**
             * 5️⃣ Final reputation score
             */
            $reputationScore =
                $uniqueFriends
                + $sharedHobbiesScore
                + $accountAgeScore
                - $blockedScore;

            $user->update([
                'reputation_score' => $reputationScore,
            ]);

            return (float) $reputationScore;
        });
    }

    public function recalculateAllReputations(): void
    {
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                $this->calculateReputation($user);
            }
        });
    }
}
