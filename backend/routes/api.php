<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Instructor\CourseController;
use App\Http\Controllers\Api\V1\Instructor\LessonController;
use App\Http\Controllers\Api\V1\Instructor\SectionController;
use App\Http\Controllers\Api\V1\Payment\CheckoutController;
use App\Http\Controllers\Api\V1\Payment\RefundController;
use App\Http\Controllers\Api\V1\Payment\WebhookController;
use App\Http\Controllers\Api\V1\CertificateVerificationController;
use App\Http\Controllers\Api\V1\Student\CertificateController;
use App\Http\Controllers\Api\V1\Student\CourseDiscoveryController;
use App\Http\Controllers\Api\V1\Student\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Version 1)
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| All routes are prefixed with /api/v1
|
*/

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'service' => 'Course Marketplace API',
        'version' => '1.0.0'
    ]);
});

// Public routes (no authentication required)
Route::prefix('v1')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [RegisterController::class, 'register']);
        Route::post('/login', [LoginController::class, 'login']);
        Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
        Route::post('/reset-password', [PasswordResetController::class, 'reset']);
    });

    // Public course discovery routes
    Route::prefix('courses')->group(function () {
        Route::get('/', [CourseDiscoveryController::class, 'index']);
        Route::get('/featured', [CourseDiscoveryController::class, 'featured']);
        Route::get('/search', [CourseDiscoveryController::class, 'search']);
        Route::get('/{id}', [CourseDiscoveryController::class, 'show']);
        
        // Public review routes (anyone can view reviews)
        Route::get('/{id}/reviews', [ReviewController::class, 'index']);
        Route::get('/{id}/reviews/{review}', [ReviewController::class, 'show']);
    });

    // Certificate verification (public)
    Route::get('certificates/verify/{code}', [CertificateVerificationController::class, 'verify']);
});

// Protected routes (require authentication)
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    
    // Authenticated user routes
    Route::post('auth/logout', [LoginController::class, 'logout']);
    
    // Instructor routes
    Route::prefix('instructor')->middleware([\App\Http\Middleware\EnsureInstructor::class])->group(function () {
        // Course routes
        Route::get('courses', [CourseController::class, 'index']);
        Route::post('courses', [CourseController::class, 'store']);
        Route::get('courses/{course}', [CourseController::class, 'show']);
        Route::put('courses/{course}', [CourseController::class, 'update']);
        Route::delete('courses/{course}', [CourseController::class, 'destroy']);
        Route::post('courses/{course}/publish', [CourseController::class, 'publish']);
        Route::post('courses/{course}/unpublish', [CourseController::class, 'unpublish']);

        // Section routes (nested under courses)
        Route::post('courses/{course}/sections', [SectionController::class, 'store']);
        Route::put('courses/{course}/sections/{section}', [SectionController::class, 'update']);
        Route::delete('courses/{course}/sections/{section}', [SectionController::class, 'destroy']);
        Route::post('courses/{course}/sections/reorder', [SectionController::class, 'reorder']);

        // Lesson routes (nested under courses and sections)
        Route::post('courses/{course}/sections/{section}/lessons', [LessonController::class, 'store']);
        Route::put('courses/{course}/sections/{section}/lessons/{lesson}', [LessonController::class, 'update']);
        Route::delete('courses/{course}/sections/{section}/lessons/{lesson}', [LessonController::class, 'destroy']);
        Route::post('courses/{course}/sections/{section}/lessons/{lesson}/upload-video', [LessonController::class, 'uploadVideo']);
        Route::post('courses/{course}/sections/{section}/lessons/reorder', [LessonController::class, 'reorder']);

        // Review response routes (instructor can respond to reviews)
        Route::post('courses/{course}/reviews/{review}/respond', [ReviewController::class, 'respond']);
    });
    
    // Student routes  
    Route::prefix('student')->middleware([\App\Http\Middleware\EnsureStudent::class])->group(function () {
        // Enrollment routes
        Route::get('enrollments', [\App\Http\Controllers\Api\V1\Student\EnrollmentController::class, 'index']);
        Route::post('enrollments', [\App\Http\Controllers\Api\V1\Student\EnrollmentController::class, 'store']);
        Route::get('enrollments/{enrollment}', [\App\Http\Controllers\Api\V1\Student\EnrollmentController::class, 'show']);

        // Learning routes
        Route::get('courses/{course}/learning', [\App\Http\Controllers\Api\V1\Student\LearningController::class, 'getCourseLearning']);
        Route::get('courses/{course}/lessons/{lesson}/video-url', [\App\Http\Controllers\Api\V1\Student\LearningController::class, 'getVideoUrl']);

        // Progress routes
        Route::get('enrollments/{enrollment}/progress', [\App\Http\Controllers\Api\V1\Student\ProgressController::class, 'index']);
        Route::get('enrollments/{enrollment}/lessons/{lesson}/progress', [\App\Http\Controllers\Api\V1\Student\ProgressController::class, 'show']);
        Route::put('enrollments/{enrollment}/lessons/{lesson}/progress/position', [\App\Http\Controllers\Api\V1\Student\ProgressController::class, 'updatePosition']);
        Route::post('enrollments/{enrollment}/lessons/{lesson}/progress/complete', [\App\Http\Controllers\Api\V1\Student\ProgressController::class, 'markComplete']);

        // Review routes
        Route::get('courses/{course}/reviews/my', [ReviewController::class, 'myReview']);
        Route::post('courses/{course}/reviews', [ReviewController::class, 'store']);
        Route::put('courses/{course}/reviews/{review}', [ReviewController::class, 'update']);
        Route::delete('courses/{course}/reviews/{review}', [ReviewController::class, 'destroy']);
        Route::post('courses/{course}/reviews/{review}/flag', [ReviewController::class, 'flag']);

        // Certificate routes
        Route::get('certificates', [CertificateController::class, 'index']);
        Route::get('enrollments/{enrollment}/certificate', [CertificateController::class, 'show']);
        Route::get('certificates/{certificate}/download', [CertificateController::class, 'download']);
    });
    
    // Payment routes
    Route::prefix('payment')->group(function () {
        Route::post('checkout', [CheckoutController::class, 'create']);
        Route::post('verify', [CheckoutController::class, 'verify']);
        Route::get('transactions/{transaction}/refund/eligibility', [RefundController::class, 'eligibility']);
        Route::post('transactions/{transaction}/refund', [RefundController::class, 'request']);
    });
});

// Webhook routes (for external services like Stripe)
// Note: Webhooks should not use CSRF protection
Route::post('webhooks/stripe', [WebhookController::class, 'handle'])
    ->middleware(\Illuminate\Http\Middleware\ValidatePostSize::class);

