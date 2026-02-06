<?php

namespace App\Providers;

use App\Events\CourseCompleted;
use App\Events\CoursePublished;
use App\Events\LessonCompleted;
use App\Events\PaymentProcessed;
use App\Events\StudentEnrolled;
use App\Jobs\SendTransactionReceipt;
use App\Listeners\IssueCertificate;
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
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;

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
        $this->configureRateLimiting();

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

        Event::listen(
            CourseCompleted::class,
            IssueCertificate::class
        );
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $user = $request->user();
            return Limit::perMinute(60)->by($user ? $user->id : $request->ip());
        });

        RateLimiter::for('public', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('webhooks', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
