<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8',
            'age'      => 'required|integer|min:1',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'age'      => $request->age,
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user_id' => $user->id,
        ], 201);
    }

    /**
     * Issue API token (LOGIN)
     */
    public function issueToken(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        // ✅ PROPER credential validation
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 401);
        }

        $plainTextToken = Str::random(64);

        ApiToken::create([
            'user_id'    => $user->id,
            'token'      => hash('sha256', $plainTextToken),
            'name'       => 'API Token',
            'abilities'  => ['*'],
            'expires_at' => Carbon::now()->addDays(30),
        ]);

        return response()->json([
            'token'       => $plainTextToken,
            'token_type'  => 'Bearer',
            'expires_at'  => now()->addDays(30)->toIso8601String(),
        ]);
    }

    /**
     * Revoke API token
     */
    public function revokeToken(Request $request)
    {
        $token = $request->bearerToken();

        if ($token) {
            ApiToken::where('token', hash('sha256', $token))->delete();
        }

        return response()->json([
            'message' => 'Token revoked'
        ]);
    }
}
