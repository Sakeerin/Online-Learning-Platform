<script setup lang="ts">
import { computed } from 'vue'
import CourseCard from './CourseCard.vue'
import type { Course } from '@/types/Course'

interface Props {
  courses: Course[]
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  loading: false,
})

const hasCourses = computed(() => props.courses.length > 0)
</script>

<template>
  <div class="course-grid">
    <!-- T120: Loading skeleton states -->
    <div v-if="loading" class="skeleton-grid">
      <div v-for="i in 12" :key="i" class="skeleton-card">
        <div class="skeleton-thumbnail"></div>
        <div class="skeleton-content">
          <div class="skeleton-line skeleton-title"></div>
          <div class="skeleton-line skeleton-subtitle"></div>
          <div class="skeleton-line skeleton-meta"></div>
        </div>
      </div>
    </div>

    <!-- Course cards -->
    <template v-else-if="hasCourses">
      <CourseCard v-for="course in courses" :key="course.id" :course="course" />
    </template>

    <!-- Empty state -->
    <div v-else class="empty-state">
      <p>No courses found</p>
    </div>
  </div>
</template>

<style scoped>
.course-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
  padding: 1rem 0;
}

.skeleton-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.skeleton-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.skeleton-thumbnail {
  width: 100%;
  height: 200px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s ease-in-out infinite;
}

.skeleton-content {
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.skeleton-line {
  height: 1rem;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s ease-in-out infinite;
  border-radius: 4px;
}

.skeleton-title {
  width: 80%;
  height: 1.25rem;
}

.skeleton-subtitle {
  width: 60%;
}

.skeleton-meta {
  width: 40%;
}

@keyframes loading {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

.empty-state {
  grid-column: 1 / -1;
  text-align: center;
  padding: 4rem 2rem;
  color: #6b7280;
}
</style>

