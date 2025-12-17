<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCourses } from '@/composables/useCourses'
import { useCourseStore } from '@/stores/course'
import CourseForm from '@/components/instructor/CourseForm.vue'
import SectionEditor from '@/components/instructor/SectionEditor.vue'
import LessonUploader from '@/components/instructor/LessonUploader.vue'
import Button from '@/components/common/Button.vue'
import Card from '@/components/common/Card.vue'
import type { CreateCourseData, CreateSectionData, CreateLessonData } from '@/types/Course'

const route = useRoute()
const router = useRouter()
const courseId = route.params.id as string

const { fetchCourse, updateCourse, publishCourse, unpublishCourse, isLoading, error } = useCourses()
const courseStore = useCourseStore()

const course = ref(courseStore.currentCourse)
const showSectionEditor = ref(false)
const showLessonUploader = ref(false)
const selectedSectionId = ref<string | null>(null)

onMounted(async () => {
  if (!course.value || course.value.id !== courseId) {
    await fetchCourse(courseId)
    course.value = courseStore.currentCourse
  }
})

const handleCourseUpdate = async (data: CreateCourseData) => {
  try {
    await updateCourse(courseId, data)
    await fetchCourse(courseId)
    course.value = courseStore.currentCourse
  } catch (err) {
    console.error('Failed to update course:', err)
  }
}

const handlePublish = async () => {
  try {
    await publishCourse(courseId)
    await fetchCourse(courseId)
    course.value = courseStore.currentCourse
  } catch (err) {
    console.error('Failed to publish course:', err)
  }
}

const handleUnpublish = async () => {
  try {
    await unpublishCourse(courseId)
    await fetchCourse(courseId)
    course.value = courseStore.currentCourse
  } catch (err) {
    console.error('Failed to unpublish course:', err)
  }
}

const handleSectionSave = async (data: CreateSectionData) => {
  try {
    await courseStore.createSection(courseId, data)
    await fetchCourse(courseId)
    course.value = courseStore.currentCourse
    showSectionEditor.value = false
  } catch (err) {
    console.error('Failed to create section:', err)
  }
}

const handleLessonSave = async (data: CreateLessonData) => {
  if (!selectedSectionId.value) return

  try {
    await courseStore.createLesson(courseId, selectedSectionId.value, data)
    await fetchCourse(courseId)
    course.value = courseStore.currentCourse
    showLessonUploader.value = false
    selectedSectionId.value = null
  } catch (err) {
    console.error('Failed to create lesson:', err)
  }
}

const openLessonUploader = (sectionId: string) => {
  selectedSectionId.value = sectionId
  showLessonUploader.value = true
}
</script>

<template>
  <div class="edit-course">
    <div class="container">
      <header class="page-header">
        <div>
          <h1>{{ course?.title || 'Edit Course' }}</h1>
          <p class="course-status">Status: <span :class="course?.status">{{ course?.status }}</span></p>
        </div>
        <div class="header-actions">
          <Button v-if="course?.status === 'draft'" @click="handlePublish" :loading="isLoading">
            Publish Course
          </Button>
          <Button v-else-if="course?.status === 'published'" variant="outline" @click="handleUnpublish" :loading="isLoading">
            Unpublish
          </Button>
        </div>
      </header>

      <div v-if="error" class="error-message">{{ error }}</div>

      <div class="course-content">
        <Card title="Course Details">
          <CourseForm v-if="course" :course="course" :loading="isLoading" @submit="handleCourseUpdate" />
        </Card>

        <Card title="Course Curriculum">
          <div class="curriculum-actions">
            <Button @click="showSectionEditor = true">Add Section</Button>
          </div>

          <div v-if="showSectionEditor" class="editor-container">
            <SectionEditor @save="handleSectionSave" @cancel="showSectionEditor = false" />
          </div>

          <div v-if="course?.sections && course.sections.length > 0" class="sections-list">
            <div v-for="section in course.sections" :key="section.id" class="section-item">
              <div class="section-header">
                <h3>{{ section.title }}</h3>
                <Button size="sm" @click="openLessonUploader(section.id)">Add Lesson</Button>
              </div>
              <p v-if="section.description" class="section-description">{{ section.description }}</p>
              
              <div v-if="section.lessons && section.lessons.length > 0" class="lessons-list">
                <div v-for="lesson in section.lessons" :key="lesson.id" class="lesson-item">
                  <span class="lesson-type">{{ lesson.type }}</span>
                  <span>{{ lesson.title }}</span>
                  <span v-if="lesson.duration" class="lesson-duration">{{ Math.floor(lesson.duration / 60) }}m</span>
                </div>
              </div>
            </div>
          </div>

          <div v-if="showLessonUploader" class="editor-container">
            <LessonUploader @save="handleLessonSave" @cancel="showLessonUploader = false" />
          </div>
        </Card>
      </div>
    </div>
  </div>
</template>

<style scoped>
.edit-course {
  min-height: 100vh;
  padding: 2rem 0;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.course-status {
  color: #6b7280;
}

.course-status span {
  text-transform: capitalize;
  font-weight: 600;
}

.course-status span.draft {
  color: #f59e0b;
}

.course-status span.published {
  color: #10b981;
}

.course-status span.unpublished {
  color: #6b7280;
}

.error-message {
  padding: 1rem;
  background: #fee2e2;
  color: var(--error);
  border-radius: 8px;
  margin-bottom: 1.5rem;
}

.course-content {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.curriculum-actions {
  margin-bottom: 1.5rem;
}

.editor-container {
  margin: 1.5rem 0;
}

.sections-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  margin-top: 1.5rem;
}

.section-item {
  padding: 1.5rem;
  background: #f9fafb;
  border-radius: 8px;
  border: 1px solid var(--border-color);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.section-header h3 {
  margin: 0;
  font-size: 1.25rem;
}

.section-description {
  color: #6b7280;
  margin-bottom: 1rem;
}

.lessons-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-top: 1rem;
}

.lesson-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  background: white;
  border-radius: 6px;
  border: 1px solid var(--border-color);
}

.lesson-type {
  padding: 0.25rem 0.5rem;
  background: #e0e7ff;
  color: #6366f1;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.lesson-duration {
  margin-left: auto;
  color: #6b7280;
  font-size: 0.875rem;
}
</style>

