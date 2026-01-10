<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Hobby;
use App\Models\ApiToken;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        // Create test user with API token
        $user = User::create([
            'id' => Str::uuid(),
            'username' => 'testuser',
            'password' => Hash::make('password123'),
            'age' => 25,
            'reputation_score' => 0,
            'version' => 0,
        ]);

        // Create API token
        ApiToken::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'token' => Hash::make('test_token'),
            'name' => 'Test Token',
            'abilities' => ['*'],
            'expires_at' => now()->addDays(30),
        ]);

        // Create hobbies
        $hobbies = ['Reading', 'Gaming', 'Cooking', 'Hiking'];
        foreach ($hobbies as $hobbyName) {
            Hobby::create([
                'id' => Str::uuid(),
                'name' => $hobbyName
            ]);
        }
    }
}