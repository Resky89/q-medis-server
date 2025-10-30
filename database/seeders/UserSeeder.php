<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'avatar' => 'https://www.gravatar.com/avatar/'.md5('admin@example.com').'?d=identicon',
            ]
        );

        // Petugas users
        User::updateOrCreate(
            ['email' => 'petugas1@example.com'],
            [
                'name' => 'Petugas 1',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'avatar' => 'https://www.gravatar.com/avatar/'.md5('petugas1@example.com').'?d=identicon',
            ]
        );

        User::updateOrCreate(
            ['email' => 'petugas2@example.com'],
            [
                'name' => 'Petugas 2',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'avatar' => 'https://www.gravatar.com/avatar/'.md5('petugas2@example.com').'?d=identicon',
            ]
        );
    }
}
