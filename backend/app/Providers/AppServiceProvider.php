<?php

namespace App\Providers;

use App\Events\CoursePublished;
use App\Events\LessonCompleted;
use App\Events\PaymentProcessed;
use App\Events\StudentEnrolled;
use App\Jobs\SendTransactionReceipt;
use App\Listeners\SendCoursePublishedNotification;
use App\Listeners\SendEnrollmentConfirmation;
use App\Listeners\UpdateCourseProgress;
use App\Listeners\UpdateInstructorRevenue;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use App\Policies\CoursePolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Course::class => CoursePolicy::class,
        Enrollment::class => EnrollmentPolicy::class,
        Review::class => ReviewPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Register event listeners
        Event::listen(
            CoursePublished::class,
            SendCoursePublishedNotification::class
        );

        Event::listen(
            StudentEnrolled::class,
            SendEnrollmentConfirmation::class
        );

        Event::listen(
            LessonCompleted::class,
            UpdateCourseProgress::class
        );

        Event::listen(
            PaymentProcessed::class,
            UpdateInstructorRevenue::class
        );

        Event::listen(
            PaymentProcessed::class,
            function (PaymentProcessed $event) {
                SendTransactionReceipt::dispatch($event->transaction);
            }
        );
    }
}
