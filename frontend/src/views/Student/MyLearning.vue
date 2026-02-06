<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useEnrollment } from '@/composables/useEnrollment'
import CourseCard from '@/components/course/CourseCard.vue'
import ProgressBar from '@/components/student/ProgressBar.vue'
import Card from '@/components/common/Card.vue'
import Button from '@/components/common/Button.vue'
import SkeletonList from '@/components/common/SkeletonList.vue'

const { enrollments, activeEnrollments, completedEnrollments, fetchEnrollments, isLoading } = useEnrollment()
const activeTab = ref<'active' | 'completed'>('active')

onMounted(async () => {
  await fetchEnrollments()
})
</script>

<template>
  <div class="my-learning">
    <div class="container">
      <header class="page-header">
        <h1>My Learning</h1>
        <p>Continue your learning journey</p>
      </header>

      <div class="tabs" role="tablist" aria-label="Course categories">
        <button
          role="tab"
          :aria-selected="activeTab === 'active'"
          aria-controls="active-panel"
          :class="['tab', { active: activeTab === 'active' }]"
          @click="activeTab = 'active'"
        >
          Active Courses ({{ activeEnrollments.length }})
        </button>
        <button
          role="tab"
          :aria-selected="activeTab === 'completed'"
          aria-controls="completed-panel"
          :class="['tab', { active: activeTab === 'completed' }]"
          @click="activeTab = 'completed'"
        >
          Completed ({{ completedEnrollments.length }})
        </button>
      </div>

      <SkeletonList v-if="isLoading" :count="6" />
      <div v-else-if="activeTab === 'active' && activeEnrollments.length === 0" class="empty-state">
        <p>You haven't enrolled in any courses yet.</p>
        <Button @click="$router.push({ name: 'browse-courses' })">Browse Courses</Button>
      </div>
      <div v-else-if="activeTab === 'completed' && completedEnrollments.length === 0" class="empty-state">
        <p>No completed courses yet.</p>
      </div>
      <div v-else class="enrollments-grid">
        <Card
          v-for="enrollment in (activeTab === 'active' ? activeEnrollments : completedEnrollments)"
          :key="enrollment.id"
          class="enrollment-card"
        >
          <div v-if="enrollment.course" class="enrollment-content">
            <div class="course-info">
              <h3>{{ enrollment.course.title }}</h3>
              <p v-if="enrollment.course.subtitle">{{ enrollment.course.subtitle }}</p>
            </div>
            <div class="progress-section">
              <div class="progress-header">
                <span class="progress-label">Progress</span>
                <span class="progress-percentage">{{ Math.round(enrollment.progress_percentage) }}%</span>
              </div>
              <ProgressBar :progress="enrollment.progress_percentage" />
            </div>
            <div class="enrollment-actions">
              <Button
                @click="$router.push({ name: 'course-player', params: { courseId: enrollment.course_id } })"
              >
                {{ enrollment.is_completed ? 'Review Course' : 'Continue Learning' }}
              </Button>
            </div>
          </div>
        </Card>
      </div>
    </div>
  </div>
</template>

<style scoped>
.my-learning {
  min-height: 100vh;
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.tabs {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  border-bottom: 2px solid var(--border-color);
}

.tab {
  padding: 1rem 1.5rem;
  background: none;
  border: none;
  border-bottom: 2px solid transparent;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
  color: #6b7280;
  transition: all 0.2s;
}

.tab:hover {
  color: var(--primary-color);
}

.tab.active {
  color: var(--primary-color);
  border-bottom-color: var(--primary-color);
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #6b7280;
}

.enrollments-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.enrollment-card {
  height: 100%;
}

.enrollment-content {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.course-info h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.25rem;
}

.course-info p {
  margin: 0;
  color: #6b7280;
  font-size: 0.875rem;
}

.progress-section {
  margin: 1rem 0;
}

.progress-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.progress-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #6b7280;
}

.progress-percentage {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--primary-color);
}

.enrollment-actions {
  margin-top: auto;
}
</style>

