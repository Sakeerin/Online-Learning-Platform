<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table with instructors, students, and an admin.
     */
    public function run(): void
    {
        // Create instructors
        User::factory()->instructor()->create([
            'name' => 'Sarah Johnson',
            'email' => 'instructor@example.com',
            'password' => Hash::make('password'),
            'bio' => 'Senior software engineer with 10+ years of experience in web development and cloud computing. Passionate about teaching the next generation of developers.',
        ]);

        User::factory()->instructor()->create([
            'name' => 'Michael Chen',
            'email' => 'instructor2@example.com',
            'password' => Hash::make('password'),
            'bio' => 'UX designer and creative director specializing in brand identity, user experience, and digital marketing strategies.',
        ]);

        // Create students
        $studentNames = [
            'student@example.com' => 'Alice Williams',
            'student2@example.com' => 'Bob Martinez',
            'student3@example.com' => 'Carol Davis',
            'student4@example.com' => 'David Kim',
            'student5@example.com' => 'Eva Thompson',
        ];

        foreach ($studentNames as $email => $name) {
            User::factory()->student()->create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
            ]);
        }

        // Create admin
        User::factory()->create([
            'name' => 'Platform Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}
