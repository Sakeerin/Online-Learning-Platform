<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    /**
     * Determine if the user can view any enrollments.
     */
    public function viewAny(User $user): bool
    {
        return $user->isStudent();
    }

    /**
     * Determine if the user can view the enrollment.
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        return $user->isStudent() && $enrollment->user_id === $user->id;
    }

    /**
     * Determine if the user can create enrollments.
     */
    public function create(User $user): bool
    {
        return $user->isStudent();
    }

    /**
     * Determine if the user can access the enrollment content.
     */
    public function access(User $user, Enrollment $enrollment): bool
    {
        return $user->isStudent() && $enrollment->user_id === $user->id;
    }
}

