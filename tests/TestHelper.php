<?php

namespace Tests;

use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestHelper
{
    public static function createUserWithToken($username = 'testuser')
    {
        $user = User::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'username' => $username,
            'password' => 'password123',
            'age' => 25,
            'reputation_score' => 0,
            'version' => 0,
        ]);

        $plainToken = 'test_token_' . $username;
        ApiToken::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'token' => Hash::make($plainToken),
            'name' => 'Test Token',
            'abilities' => ['*'],
            'expires_at' => now()->addDay(),
        ]);

        return [
            'user' => $user,
            'token' => $plainToken,
            'headers' => ['Authorization' => 'Bearer ' . $plainToken]
        ];
    }
}