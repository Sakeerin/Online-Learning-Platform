<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
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
    Route::prefix('instructor')->middleware(['role:instructor'])->group(function () {
        // Will be implemented in User Story 1: Course Creation
        // Route::resource('courses', CourseController::class);
        // Route::resource('sections', SectionController::class);
        // Route::resource('lessons', LessonController::class);
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

