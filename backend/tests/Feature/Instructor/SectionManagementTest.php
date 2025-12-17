<?php

namespace Tests\Feature\Instructor;

use App\Models\Course;
use App\Models\Section;
use Tests\TestCase;

class SectionManagementTest extends TestCase
{
    /**
     * Test creating a section
     */
    public function test_can_create_section(): void
    {
        $instructor = $this->createInstructor();
        $token = $this->authenticateUser($instructor);

        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        $sectionData = [
            'title' => 'Introduction Section',
            'description' => 'This is an introduction',
            'order' => 1,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/instructor/courses/{$course->id}/sections", $sectionData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'order',
                ],
            ]);

        $this->assertDatabaseHas('sections', [
            'course_id' => $course->id,
            'title' => 'Introduction Section',
        ]);
    }

    /**
     * Test updating a section
     */
    public function test_can_update_section(): void
    {
        $instructor = $this->createInstructor();
        $token = $this->authenticateUser($instructor);

        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $section = Section::factory()->create(['course_id' => $course->id]);

        $updateData = [
            'title' => 'Updated Section Title',
            'description' => 'Updated description',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/v1/instructor/courses/{$course->id}/sections/{$section->id}", $updateData);

        $response->assertStatus(200);

        $section->refresh();
        $this->assertEquals('Updated Section Title', $section->title);
    }

    /**
     * Test deleting a section
     */
    public function test_can_delete_section(): void
    {
        $instructor = $this->createInstructor();
        $token = $this->authenticateUser($instructor);

        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $section = Section::factory()->create(['course_id' => $course->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/v1/instructor/courses/{$course->id}/sections/{$section->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('sections', ['id' => $section->id]);
    }

    /**
     * Test reordering sections
     */
    public function test_can_reorder_sections(): void
    {
        $instructor = $this->createInstructor();
        $token = $this->authenticateUser($instructor);

        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $section1 = Section::factory()->create(['course_id' => $course->id, 'order' => 1]);
        $section2 = Section::factory()->create(['course_id' => $course->id, 'order' => 2]);
        $section3 = Section::factory()->create(['course_id' => $course->id, 'order' => 3]);

        $reorderData = [
            'sections' => [
                ['id' => $section3->id, 'order' => 1],
                ['id' => $section1->id, 'order' => 2],
                ['id' => $section2->id, 'order' => 3],
            ],
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/instructor/courses/{$course->id}/sections/reorder", $reorderData);

        $response->assertStatus(200);

        $section3->refresh();
        $section1->refresh();
        $section2->refresh();

        $this->assertEquals(1, $section3->order);
        $this->assertEquals(2, $section1->order);
        $this->assertEquals(3, $section2->order);
    }
}

