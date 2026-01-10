<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hobby;
use App\Services\ReputationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HobbyController extends Controller
{
    protected $reputationService;

    public function __construct(ReputationService $reputationService)
    {
        $this->reputationService = $reputationService;
    }

    public function store(Request $request, string $id)
    {
        $request->validate([
            'hobby_id' => 'required|uuid|exists:hobbies,id',
        ]);

        $user = User::findOrFail($id);
        $hobby = Hobby::findOrFail($request->hobby_id);

        DB::transaction(function () use ($user, $hobby) {
            // Attach hobby if not already attached
            $user->hobbies()->syncWithoutDetaching([$hobby->id]);

            // Recalculate for user
        $this->reputationService->calculateReputation($user);

        // 🔥 Recalculate for all friends
        foreach ($user->allFriends() as $friend) {
            $this->reputationService->calculateReputation($friend);
        }
        });

        return response()->json([
            'message' => 'Hobby added successfully',
            'user_id' => $user->id,
            'hobby_id' => $hobby->id
        ], 201);
    }

    public function destroy(Request $request, string $id)
    {
        $request->validate([
            'hobby_id' => 'required|uuid|exists:hobbies,id',
        ]);

        $user = User::findOrFail($id);
        $hobby = Hobby::findOrFail($request->hobby_id);

        DB::transaction(function () use ($user, $hobby) {
            // Detach hobby
            $user->hobbies()->detach($hobby->id);

            /// Recalculate for user
        $this->reputationService->calculateReputation($user);

        // 🔥 Recalculate for all friends
        foreach ($user->allFriends() as $friend) {
            $this->reputationService->calculateReputation($friend);
        }
        });

        return response()->json([
            'message' => 'Hobby removed successfully'
        ], 200);
    }
}