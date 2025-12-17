<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useCourses } from '@/composables/useCourses'
import CourseForm from '@/components/instructor/CourseForm.vue'
import type { CreateCourseData } from '@/types/Course'

const router = useRouter()
const { createCourse, isLoading, error } = useCourses()

const handleSubmit = async (data: CreateCourseData) => {
  try {
    const course = await createCourse(data)
    router.push({ name: 'edit-course', params: { id: course.id } })
  } catch (err) {
    console.error('Failed to create course:', err)
  }
}
</script>

<template>
  <div class="create-course">
    <div class="container">
      <header class="page-header">
        <h1>Create New Course</h1>
        <p>Fill in the details to create your course</p>
      </header>

      <div v-if="error" class="error-message">{{ error }}</div>

      <CourseForm :loading="isLoading" @submit="handleSubmit" />
    </div>
  </div>
</template>

<style scoped>
.create-course {
  min-height: 100vh;
  padding: 2rem 0;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.error-message {
  padding: 1rem;
  background: #fee2e2;
  color: var(--error);
  border-radius: 8px;
  margin-bottom: 1.5rem;
}
</style>

