<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('admin123'),
            // 'avatar' => null,
            'user_type' => 'admin',
            'status' => 'active',
            'contact' => '9999999999',
            'created_by' => null,
        ]);

        $roles = ['faculty', 'hostel_staff', 'general', 'student', 'parent'];

        foreach ($roles as $role) {
            User::factory()->count(5)->create([
                'user_type' => $role,
            ]);
        }
    }
}