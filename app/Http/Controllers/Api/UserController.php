<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ReputationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected ReputationService $reputationService;

    public function __construct(ReputationService $reputationService)
    {
        $this->reputationService = $reputationService;
    }

    /**
     * List users (paginated)
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);

        $users = User::paginate($perPage);

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'per_page'     => $users->perPage(),
                'total'        => $users->total(),
                'last_page'    => $users->lastPage(),
            ],
        ]);
    }

    /**
     * Show single user
     */
    public function show(string $id)
    {
        $user = User::with(['hobbies', 'friends', 'friendOf'])->findOrFail($id);

        return response()->json([
            'id'              => $user->id,
            'username'        => $user->username,
            'age'             => $user->age,
            'reputationScore' => $user->reputation_score,
            'version'         => $user->version,
            'createdAt'       => $user->created_at->toIso8601String(),
            'updatedAt'       => $user->updated_at->toIso8601String(),
            'hobbies'         => $user->hobbies,
            'friends'         => $user->allFriends()->pluck('id'),
        ]);
    }

    /**
     * Create user profile (NO password here)
     * Auth users should be created via /api/register
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'age'      => 'required|integer|min:1|max:120',
        ]);

        $user = User::create([
            'username'         => $request->username,
            'age'              => $request->age,
            'reputation_score' => 0,
            'version'          => 0,
        ]);

        // Initial reputation calculation
        $this->reputationService->calculateReputation($user);

        return response()->json([
            'id'              => $user->id,
            'username'        => $user->username,
            'age'             => $user->age,
            'reputationScore' => $user->reputation_score,
            'createdAt'       => $user->created_at->toIso8601String(),
        ], 201);
    }

    /**
     * Update user profile (optimistic locking)
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'username' => 'sometimes|string|unique:users,username,' . $id,
            'age'      => 'sometimes|integer|min:1|max:120',
            'version'  => 'required|integer',
        ]);

        $user = User::findOrFail($id);

        // Optimistic lock check
        if ($user->version !== $request->version) {
            return response()->json([
                'error'           => 'Conflict: Resource has been modified',
                'current_version' => $user->version,
            ], 409);
        }

        $user->update([
            ...$request->only(['username', 'age']),
            'version' => $user->version + 1,
        ]);

        return response()->json([
            'id'              => $user->id,
            'username'        => $user->username,
            'age'             => $user->age,
            'reputationScore' => $user->reputation_score,
            'version'         => $user->version,
            'updatedAt'       => $user->updated_at->toIso8601String(),
        ]);
    }

    /**
     * Delete user safely
     */
    public function destroy(string $id)
    {
        $user = User::with(['friends', 'friendOf'])->findOrFail($id);

        if ($user->allFriends()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete user with active relationships',
            ], 409);
        }

        $threshold = config('reputation.delete_threshold', 10.0);

        if ($user->reputation_score > $threshold) {
            return response()->json([
                'error'          => 'Cannot delete user with reputation above threshold',
                'threshold'     => $threshold,
                'current_score' => $user->reputation_score,
            ], 409);
        }

        DB::transaction(function () use ($user) {
            $user->apiTokens()->delete();
            $user->hobbies()->detach();
            $user->delete();
        });

        return response()->json(null, 204);
    }
}
