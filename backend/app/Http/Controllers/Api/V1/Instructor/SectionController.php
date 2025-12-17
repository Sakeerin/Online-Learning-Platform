<?php

namespace App\Http\Controllers\Api\V1\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Resources\SectionResource;
use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    /**
     * Store a newly created section.
     */
    public function store(Request $request, Course $course): JsonResponse
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:1'],
        ]);

        $section = DB::transaction(function () use ($course, $validated) {
            // If no order specified, add to end
            if (!isset($validated['order'])) {
                $maxOrder = $course->sections()->max('order') ?? 0;
                $validated['order'] = $maxOrder + 1;
            }

            return $course->sections()->create($validated);
        });

        return response()->json([
            'message' => 'Section created successfully',
            'data' => new SectionResource($section->load('lessons')),
        ], 201);
    }

    /**
     * Update the specified section.
     */
    public function update(Request $request, Course $course, Section $section): JsonResponse
    {
        $this->authorize('update', $course);

        // Ensure section belongs to course
        if ($section->course_id !== $course->id) {
            return response()->json([
                'message' => 'Section not found in this course',
            ], 404);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order' => ['sometimes', 'integer', 'min:1'],
        ]);

        $section->update($validated);

        return response()->json([
            'message' => 'Section updated successfully',
            'data' => new SectionResource($section->load('lessons')),
        ]);
    }

    /**
     * Remove the specified section.
     */
    public function destroy(Course $course, Section $section): JsonResponse
    {
        $this->authorize('update', $course);

        // Ensure section belongs to course
        if ($section->course_id !== $course->id) {
            return response()->json([
                'message' => 'Section not found in this course',
            ], 404);
        }

        $section->delete();

        return response()->json([
            'message' => 'Section deleted successfully',
        ]);
    }

    /**
     * Reorder sections.
     */
    public function reorder(Request $request, Course $course): JsonResponse
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'sections' => ['required', 'array'],
            'sections.*.id' => ['required', 'uuid', 'exists:sections,id'],
            'sections.*.order' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($course, $validated) {
            foreach ($validated['sections'] as $sectionData) {
                $section = Section::find($sectionData['id']);
                if ($section && $section->course_id === $course->id) {
                    $section->update(['order' => $sectionData['order']]);
                }
            }
        });

        return response()->json([
            'message' => 'Sections reordered successfully',
        ]);
    }
}

