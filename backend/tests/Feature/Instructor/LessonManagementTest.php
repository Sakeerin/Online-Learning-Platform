<?php

namespace Tests\Feature\Instructor;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LessonManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a lesson
     */
    public function test_can_create_lesson(): void
    {
        $instructor = $this->createInstructor();
        $token = $this->authenticateUser($instructor);

        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $section = Section::factory()->create(['course_id' => $course->id]);

        $lessonData = [
            'title' => 'Introduction to HTML',
            'type' => 'video',
            'content' => [
                'video_url' => 'https://example.com/video.mp4',
            ],
            'duration' => 600,
            'is_preview' => false,
            'order' => 1,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/instructor/courses/{$course->id}/sections/{$section->id}/lessons", $lessonData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'title',
                    'type',
                    'content',
                ],
            ]);

        $this->assertDatabaseHas('lessons', [
            'section_id' => $section->id,
            'title' => 'Introduction to HTML',
            'type' => 'video',
        ]);
    }

    /**
     * Test updating a lesson
     */
    public function test_can_update_lesson(): void
    {
        $instructor = $this->createInstructor();
        $token = $this->authenticateUser($instructor);

        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $section = Section::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->create(['section_id' => $section->id]);

        $updateData = [
            'title' => 'Updated Lesson Title',
            'duration' => 900,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/v1/instructor/courses/{$course->id}/sections/{$section->id}/lessons/{$lesson->id}", $updateData);

        $response->assertStatus(200);

        $lesson->refresh();
        $this->assertEquals('Updated Lesson Title', $lesson->title);
        $this->assertEquals(900, $lesson->duration);
    }

    /**
     * Test deleting a lesson
     */
    public function test_can_delete_lesson(): void
    {
        Storage::fake('public');

        $instructor = $this->createInstructor();
        $token = $this->authenticateUser($instructor);

        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $section = Section::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->create([
            'section_id' => $section->id,
            'type' => 'video',
            'content' => ['video_path' => 'videos/test.mp4'],
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/v1/instructor/courses/{$course->id}/sections/{$section->id}/lessons/{$lesson->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
    }

    /**
     * Test uploading video to lesson
     */
    public function test_can_upload_video_to_lesson(): void
    {
        Storage::fake('public');

        $instructor = $this->createInstructor();
        $token = $this->authenticateUser($instructor);

        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $section = Section::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->create([
            'section_id' => $section->id,
            'type' => 'video',
        ]);

        $videoFile = UploadedFile::fake()->create('lesson-video.mp4', 1024);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(
                "/api/v1/instructor/courses/{$course->id}/sections/{$section->id}/lessons/{$lesson->id}/upload-video",
                [
                    'video' => $videoFile,
                    'duration' => 1200,
                ]
            );

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'content',
                ],
            ]);

        $lesson->refresh();
        $this->assertArrayHasKey('video_path', $lesson->content);
        $this->assertArrayHasKey('video_url', $lesson->content);
        $this->assertEquals(1200, $lesson->duration);
    }

    /**
     * Test that video upload fails for non-video lessons
     */
    public function test_video_upload_fails_for_non_video_lessons(): void
    {
        Storage::fake('public');

        $instructor = $this->createInstructor();
        $token = $this->authenticateUser($instructor);

        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $section = Section::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->create([
            'section_id' => $section->id,
            'type' => 'article', // Not a video
        ]);

        $videoFile = UploadedFile::fake()->create('lesson-video.mp4', 1024);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(
                "/api/v1/instructor/courses/{$course->id}/sections/{$section->id}/lessons/{$lesson->id}/upload-video",
                [
                    'video' => $videoFile,
                ]
            );

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Lesson must be of type video',
            ]);
    }

    /**
     * Test reordering lessons
     */
    public function test_can_reorder_lessons(): void
    {
        $instructor = $this->createInstructor();
        $token = $this->authenticateUser($instructor);

        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $section = Section::factory()->create(['course_id' => $course->id]);
        $lesson1 = Lesson::factory()->create(['section_id' => $section->id, 'order' => 1]);
        $lesson2 = Lesson::factory()->create(['section_id' => $section->id, 'order' => 2]);
        $lesson3 = Lesson::factory()->create(['section_id' => $section->id, 'order' => 3]);

        $reorderData = [
            'lessons' => [
                ['id' => $lesson3->id, 'order' => 1],
                ['id' => $lesson1->id, 'order' => 2],
                ['id' => $lesson2->id, 'order' => 3],
            ],
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/instructor/courses/{$course->id}/sections/{$section->id}/lessons/reorder", $reorderData);

        $response->assertStatus(200);

        $lesson3->refresh();
        $lesson1->refresh();
        $lesson2->refresh();

        $this->assertEquals(1, $lesson3->order);
        $this->assertEquals(2, $lesson1->order);
        $this->assertEquals(3, $lesson2->order);
    }
}

