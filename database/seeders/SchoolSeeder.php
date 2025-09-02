<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        // Create a test school
        $school = School::firstOrCreate(
            ['code' => 'TS001'],
            [
                'name' => 'Test School',
                'contact_email' => 'school@test.com',
                'contact_phone' => '123456789',
            ]
        );

        // Create admin user for this school
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'school_id' => $school->id,
                'name' => 'Admin User',
                'password' => Hash::make('12345678'),
            ]
        );

        // Assign role if you’re using Spatie Permissions
        if (method_exists($admin, 'assignRole')) {
            $admin->assignRole('admin');
        }
    }
}
