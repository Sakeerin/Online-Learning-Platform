<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CourseSeeder::class,
            EnrollmentSeeder::class,
            TransactionSeeder::class,
            ReviewSeeder::class,
            QuizSeeder::class,
            DiscussionSeeder::class,
            CertificateSeeder::class,
        ]);
    }
}
