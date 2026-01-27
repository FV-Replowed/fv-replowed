<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserMeta;
use App\Models\UserAvatar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $testUid = 1111111111;

        User::create([
            'uid' => $testUid,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('TestPassword'),
        ]);

        UserMeta::create([
            'uid' => $testUid,
            'firstName' => 'Test',
            'lastName' => 'User',
        ]);

        $userAvatar = UserAvatar::create([
            'uid' => $testUid,
            'value' => null
        ]);
    }
}
