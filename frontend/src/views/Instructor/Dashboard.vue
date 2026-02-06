<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { useCourses } from '@/composables/useCourses'
import Card from '@/components/common/Card.vue'
import Button from '@/components/common/Button.vue'
import Skeleton from '@/components/common/Skeleton.vue'

const router = useRouter()
const { user } = useAuth()
const { fetchCourses, courses, isLoading } = useCourses()

onMounted(async () => {
  await fetchCourses()
})
</script>

<template>
  <div class="instructor-dashboard">
    <div class="container">
      <header class="dashboard-header">
        <div>
          <h1>Instructor Dashboard</h1>
          <p>Welcome back, {{ user?.name }}!</p>
        </div>
        <Button @click="router.push({ name: 'create-course' })">Create New Course</Button>
      </header>

      <div class="dashboard-content">
        <Card title="My Courses" subtitle="Manage your courses">
          <div v-if="isLoading" class="loading-skeleton">
            <div v-for="i in 3" :key="i" class="skeleton-course-item">
              <Skeleton height="200px" border-radius="8px 8px 0 0" />
              <div class="skeleton-course-body">
                <Skeleton variant="text" height="1.25rem" width="80%" />
                <Skeleton variant="text" height="0.875rem" width="60%" />
                <div class="skeleton-course-footer">
                  <Skeleton variant="text" height="0.75rem" width="70px" />
                  <Skeleton variant="text" height="0.75rem" width="50px" />
                </div>
              </div>
            </div>
          </div>
          <div v-else-if="courses.length === 0" class="empty-state">
            <p>You haven't created any courses yet.</p>
            <Button @click="router.push({ name: 'create-course' })">Create Your First Course</Button>
          </div>
          <div v-else class="courses-list">
            <div
              v-for="course in courses"
              :key="course.id"
              class="course-card"
              @click="router.push({ name: 'edit-course', params: { id: course.id } })"
            >
              <div class="course-thumbnail" v-if="course.thumbnail">
                <img :src="course.thumbnail" :alt="course.title" />
              </div>
              <div class="course-info">
                <h3>{{ course.title }}</h3>
                <p class="course-subtitle">{{ course.subtitle }}</p>
                <div class="course-meta">
                  <span class="course-status" :class="course.status">{{ course.status }}</span>
                  <span class="course-price">{{ course.price }} {{ course.currency }}</span>
                </div>
              </div>
            </div>
          </div>
        </Card>

        <Card title="Analytics" subtitle="Track your performance">
          <p>Analytics dashboard coming soon.</p>
        </Card>
      </div>
    </div>
  </div>
</template>

<style scoped>
.instructor-dashboard {
  min-height: 100vh;
  padding: 2rem 0;
}

.dashboard-header {
  margin-bottom: 2rem;
}

.dashboard-header h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
}

.dashboard-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.loading-skeleton {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-top: 1rem;
}

.skeleton-course-item {
  border: 2px solid var(--border-color);
  border-radius: 8px;
  overflow: hidden;
}

.skeleton-course-body {
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.skeleton-course-footer {
  display: flex;
  justify-content: space-between;
  margin-top: 0.5rem;
}

.empty-state {
  text-align: center;
  padding: 2rem;
}

.courses-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-top: 1rem;
}

.course-card {
  cursor: pointer;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  overflow: hidden;
  transition: all 0.2s;
}

.course-card:hover {
  border-color: var(--primary-color);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.course-thumbnail {
  width: 100%;
  height: 200px;
  overflow: hidden;
  background: #f3f4f6;
}

.course-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.course-info {
  padding: 1rem;
}

.course-info h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.25rem;
}

.course-subtitle {
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 1rem;
}

.course-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.course-status {
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: capitalize;
}

.course-status.draft {
  background: #fef3c7;
  color: #d97706;
}

.course-status.published {
  background: #d1fae5;
  color: #059669;
}

.course-status.unpublished {
  background: #f3f4f6;
  color: #6b7280;
}

.course-price {
  font-weight: 600;
  color: var(--primary-color);
}
</style>

