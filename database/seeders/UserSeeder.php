<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
            $admin = User::firstOrCreate(
                ['email' => 'admin@admin.com'],
                [
                    'name' => 'Admin User',
                    'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
                ]
        );
        $admin->assignRole('admin');

        $moderator = User::firstOrCreate(
            ['email' => 'moderator@moderator.com'],
            [
                'name' => 'Moderator User',
                'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
            ]
        );
        $moderator->assignRole('moderator');

        $blogger = User::firstOrCreate(
            ['email' => 'blogger@blogger.com'],
            [
                'name' => 'Blogger User',
                'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
            ]
        );
        $blogger->assignRole('blogger');
    }
}
