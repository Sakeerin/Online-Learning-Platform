<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Enrollment;

class EnsureEnrolled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $courseId = $request->route('courseId') ?? $request->route('id');
        
        if (!$courseId) {
            return response()->json([
                'message' => 'Course ID is required.',
            ], 400);
        }

        $enrollment = Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $courseId)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'message' => 'You must be enrolled in this course to access this content.',
            ], 403);
        }

        // Attach enrollment to request for use in controller
        $request->merge(['enrollment' => $enrollment]);

        return $next($request);
    }
}

