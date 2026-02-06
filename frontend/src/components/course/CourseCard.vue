<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import type { Course } from '@/types/Course'

interface Props {
  course: Course
}

const props = defineProps<Props>()
const router = useRouter()

const formattedPrice = computed(() => {
  if (props.course.price === 0) {
    return 'Free'
  }
  return `${props.course.price.toFixed(2)} ${props.course.currency}`
})

const ratingDisplay = computed(() => {
  if (!props.course.average_rating) {
    return 'No ratings yet'
  }
  return `${props.course.average_rating.toFixed(1)} ⭐ (${props.course.total_reviews} reviews)`
})

const handleClick = () => {
  router.push({ name: 'course-detail', params: { id: props.course.id } })
}
</script>

<template>
  <div
    class="course-card"
    tabindex="0"
    role="link"
    :aria-label="`${course.title} - ${formattedPrice}`"
    @click="handleClick"
    @keydown.enter="handleClick"
  >
    <div class="course-thumbnail">
      <img v-if="course.thumbnail" :src="course.thumbnail" :alt="course.title" />
      <div v-else class="thumbnail-placeholder">
        <span>No Image</span>
      </div>
    </div>
    <div class="course-content">
      <div class="course-header">
        <h3 class="course-title">{{ course.title }}</h3>
        <p v-if="course.subtitle" class="course-subtitle">{{ course.subtitle }}</p>
      </div>
      <div class="course-meta">
        <span class="course-instructor">{{ course.instructor?.name || 'Unknown Instructor' }}</span>
        <span class="course-category">{{ course.category }}</span>
      </div>
      <div class="course-stats">
        <span class="course-rating">{{ ratingDisplay }}</span>
        <span class="course-enrollments">{{ course.total_enrollments }} students</span>
      </div>
      <div class="course-footer">
        <span class="course-price">{{ formattedPrice }}</span>
        <span class="course-difficulty" :class="course.difficulty_level">{{ course.difficulty_level }}</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
.course-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.course-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.course-thumbnail {
  width: 100%;
  height: 200px;
  overflow: hidden;
  background: #f3f4f6;
  position: relative;
}

.course-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.thumbnail-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  font-size: 0.875rem;
}

.course-content {
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  flex: 1;
  gap: 0.75rem;
}

.course-header {
  flex: 1;
}

.course-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.5rem 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.course-subtitle {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.course-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.875rem;
  color: #6b7280;
}

.course-instructor {
  font-weight: 500;
}

.course-category {
  padding: 0.25rem 0.5rem;
  background: #e0e7ff;
  color: #6366f1;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
}

.course-stats {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.875rem;
  color: #6b7280;
}

.course-rating {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.course-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 0.75rem;
  border-top: 1px solid #e5e7eb;
}

.course-price {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary-color);
}

.course-difficulty {
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: capitalize;
}

.course-difficulty.beginner {
  background: #d1fae5;
  color: #059669;
}

.course-difficulty.intermediate {
  background: #fef3c7;
  color: #d97706;
}

.course-difficulty.advanced {
  background: #fee2e2;
  color: #dc2626;
}
</style>

