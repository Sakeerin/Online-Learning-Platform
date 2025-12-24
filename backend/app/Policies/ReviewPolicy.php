<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Determine if the user can view any reviews.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view reviews
    }

    /**
     * Determine if the user can view the review.
     */
    public function view(User $user, Review $review): bool
    {
        return true; // Anyone can view reviews (unless flagged)
    }

    /**
     * Determine if the user can create reviews.
     */
    public function create(User $user): bool
    {
        return $user->isStudent();
    }

    /**
     * Determine if the user can update the review.
     */
    public function update(User $user, Review $review): bool
    {
        return $user->isStudent() && $review->user_id === $user->id;
    }

    /**
     * Determine if the user can delete the review.
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->isStudent() && $review->user_id === $user->id;
    }

    /**
     * Determine if the user can flag the review.
     */
    public function flag(User $user, Review $review): bool
    {
        return $user->isStudent(); // Any student can flag inappropriate reviews
    }

    /**
     * Determine if the user can respond to the review (instructor only).
     */
    public function respond(User $user, Review $review): bool
    {
        return $user->isInstructor() && $review->course->instructor_id === $user->id;
    }
}

