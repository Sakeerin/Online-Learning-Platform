<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Instructor\CourseController;
use App\Http\Controllers\Api\V1\Instructor\LessonController;
use App\Http\Controllers\Api\V1\Instructor\SectionController;
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
        // Will be implemented in User Story 2: Course Discovery
        // Route::get('/', [CourseDiscoveryController::class, 'index']);
        // Route::get('/{id}', [CourseDiscoveryController::class, 'show']);
    });

    // Certificate verification (public)
    Route::get('certificates/verify/{code}', function () {
        // Will be implemented in User Story 6: Certificates
        return response()->json(['message' => 'Certificate verification endpoint']);
    });
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
    });
    
    // Student routes  
    Route::prefix('student')->middleware(['role:student'])->group(function () {
        // Will be implemented in User Story 3: Enrollment & Learning
        // Route::resource('enrollments', EnrollmentController::class);
        // Route::post('enrollments/{enrollment}/lessons/{lesson}/progress', [ProgressController::class, 'update']);
    });
    
    // Payment routes
    Route::prefix('payment')->group(function () {
        // Will be implemented in User Story 4: Payments
        // Route::post('checkout', [CheckoutController::class, 'create']);
    });
});

// Webhook routes (for external services like Stripe)
Route::post('webhooks/stripe', function () {
    // Will be implemented in User Story 4: Payments
    return response()->json(['message' => 'Stripe webhook endpoint']);
});

