<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Creates the application.
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        return $app;
    }

    /**
     * Creates an authenticated instructor user for testing.
     */
    protected function createInstructor(array $attributes = []): \App\Models\User
    {
        return \App\Models\User::factory()->create(array_merge([
            'role' => 'instructor',
        ], $attributes));
    }

    /**
     * Creates an authenticated student user for testing.
     */
    protected function createStudent(array $attributes = []): \App\Models\User
    {
        return \App\Models\User::factory()->create(array_merge([
            'role' => 'student',
        ], $attributes));
    }

    /**
     * Authenticates a user and returns the token.
     */
    protected function authenticateUser(\App\Models\User $user): string
    {
        return $user->createToken('test-token')->plainTextToken;
    }
}
