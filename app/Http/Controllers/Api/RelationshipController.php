<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\RelationshipService;
use App\Services\ReputationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RelationshipController extends Controller
{
    protected $relationshipService;
    protected $reputationService;

    public function __construct(
        RelationshipService $relationshipService,
        ReputationService $reputationService
    ) {
        $this->relationshipService = $relationshipService;
        $this->reputationService = $reputationService;
    }

    public function store(Request $request, string $id)
    {
        $request->validate([
            'friend_id' => 'required|uuid|exists:users,id'
        ]);

        $user = User::findOrFail($id);
        $friend = User::findOrFail($request->friend_id);

        DB::transaction(function () use ($user, $friend) {
            $this->relationshipService->createRelationship($user, $friend);

            // Recalculate reputation for both users
            $this->reputationService->calculateReputation($user);
            $this->reputationService->calculateReputation($friend);
        });

        return response()->json([
            'message' => 'Relationship created successfully',
            'user_id' => $user->id,
            'friend_id' => $friend->id
        ], 201);
    }

    public function destroy(Request $request, string $id)
    {
        $request->validate([
            'friend_id' => 'required|uuid|exists:users,id'
        ]);

        $user = User::findOrFail($id);
        $friend = User::findOrFail($request->friend_id);

        DB::transaction(function () use ($user, $friend) {
            $this->relationshipService->deleteRelationship($user, $friend);

            // Recalculate reputation for both users
            $this->reputationService->calculateReputation($user);
            $this->reputationService->calculateReputation($friend);
        });

        return response()->json([
            'message' => 'Relationship deleted successfully'
        ], 200);
    }
}