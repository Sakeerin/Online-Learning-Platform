<script setup lang="ts">
import { onMounted, computed, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useCourseDiscovery } from '@/composables/useCourseDiscovery'
import { useEnrollment } from '@/composables/useEnrollment'
import CourseCurriculum from '@/components/course/CourseCurriculum.vue'
import EnrollmentButton from '@/components/student/EnrollmentButton.vue'
import ReviewList from '@/components/student/ReviewList.vue'
import Card from '@/components/common/Card.vue'
import Skeleton from '@/components/common/Skeleton.vue'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const courseId = route.params.id as string

const { currentCourse, fetchCourse, isLoading, error } = useCourseDiscovery()
const { enrollments, fetchEnrollments } = useEnrollment()
const authStore = useAuthStore()
const isEnrolled = ref(false)

onMounted(async () => {
  await fetchCourse(courseId)
  await fetchEnrollments()
  
  // Check if user is enrolled
  isEnrolled.value = enrollments.value.some((e) => e.course_id === courseId)
})

const formattedPrice = computed(() => {
  if (!currentCourse.value) return 'Free'
  if (currentCourse.value.price === 0) {
    return 'Free'
  }
  return `${currentCourse.value.price.toFixed(2)} ${currentCourse.value.currency}`
})

const ratingDisplay = computed(() => {
  if (!currentCourse.value?.average_rating) {
    return 'No ratings yet'
  }
  return `${currentCourse.value.average_rating.toFixed(1)} ⭐ (${currentCourse.value.total_reviews} reviews)`
})
</script>

<template>
  <div class="course-detail-page">
    <div v-if="isLoading" class="loading-skeleton">
      <div class="skeleton-hero">
        <div class="skeleton-hero-content">
          <Skeleton variant="text" height="0.875rem" width="100px" />
          <Skeleton variant="text" height="2.5rem" width="80%" />
          <Skeleton variant="text" height="1.25rem" width="60%" />
          <div class="skeleton-meta-group">
            <Skeleton variant="text" height="1rem" width="200px" />
            <div class="skeleton-stats">
              <Skeleton variant="text" height="0.875rem" width="120px" />
              <Skeleton variant="text" height="0.875rem" width="150px" />
              <Skeleton variant="text" height="0.875rem" width="80px" />
            </div>
          </div>
        </div>
        <div class="skeleton-hero-sidebar">
          <Skeleton height="300px" border-radius="0.5rem" />
        </div>
      </div>
      <div class="skeleton-body">
        <div class="skeleton-main">
          <Skeleton height="200px" border-radius="0.5rem" />
          <Skeleton height="150px" border-radius="0.5rem" />
        </div>
        <div class="skeleton-side">
          <Skeleton height="250px" border-radius="0.5rem" />
        </div>
      </div>
    </div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else-if="currentCourse" class="container">
      <!-- Hero Section -->
      <div class="course-hero">
        <div class="hero-content">
          <div class="course-header">
            <span class="course-category">{{ currentCourse.category }}</span>
            <h1>{{ currentCourse.title }}</h1>
            <p v-if="currentCourse.subtitle" class="course-subtitle">{{ currentCourse.subtitle }}</p>
          </div>

          <div class="course-meta">
            <div class="instructor-info">
              <span>Instructor:</span>
              <strong>{{ currentCourse.instructor?.name || 'Unknown' }}</strong>
            </div>
            <div class="course-stats">
              <span>{{ ratingDisplay }}</span>
              <span>{{ currentCourse.total_enrollments }} students enrolled</span>
              <span class="difficulty" :class="currentCourse.difficulty_level">
                {{ currentCourse.difficulty_level }}
              </span>
            </div>
          </div>
        </div>

        <div class="hero-sidebar">
          <Card>
            <div class="purchase-card">
              <div class="price">{{ formattedPrice }}</div>
              <EnrollmentButton
                :course-id="courseId"
                :price="currentCourse.price"
                :currency="currentCourse.currency"
                :is-enrolled="isEnrolled"
                @enrolled="isEnrolled = true"
              />
              <ul class="course-features">
                <li>Full lifetime access</li>
                <li>Certificate of completion</li>
                <li>30-day money-back guarantee</li>
              </ul>
            </div>
          </Card>
        </div>
      </div>

      <!-- Course Content -->
      <div class="course-content">
        <main class="main-content">
          <Card title="About This Course">
            <div class="course-description">
              <p>{{ currentCourse.description }}</p>
              
              <div v-if="currentCourse.learning_objectives && currentCourse.learning_objectives.length > 0" class="learning-objectives">
                <h3>What you'll learn</h3>
                <ul>
                  <li v-for="(objective, index) in currentCourse.learning_objectives" :key="index">
                    {{ objective }}
                  </li>
                </ul>
              </div>
            </div>
          </Card>

          <CourseCurriculum :sections="currentCourse.sections" />

          <ReviewList
            :course-id="courseId"
            :is-enrolled="isEnrolled"
            :is-instructor="authStore.user?.role === 'instructor' && currentCourse.instructor_id === authStore.user?.id"
            :instructor-id="currentCourse.instructor_id"
          />
        </main>

        <aside class="sidebar">
          <Card title="Course Details">
            <dl class="course-details">
              <dt>Category</dt>
              <dd>{{ currentCourse.category }}</dd>
              
              <dt v-if="currentCourse.subcategory">Subcategory</dt>
              <dd v-if="currentCourse.subcategory">{{ currentCourse.subcategory }}</dd>
              
              <dt>Difficulty</dt>
              <dd class="difficulty" :class="currentCourse.difficulty_level">
                {{ currentCourse.difficulty_level }}
              </dd>
              
              <dt>Price</dt>
              <dd>{{ formattedPrice }}</dd>
              
              <dt>Rating</dt>
              <dd>{{ ratingDisplay }}</dd>
              
              <dt>Students</dt>
              <dd>{{ currentCourse.total_enrollments }} enrolled</dd>
            </dl>
          </Card>
        </aside>
      </div>
    </div>
  </div>
</template>

<style scoped>
.course-detail-page {
  min-height: 100vh;
  padding: 2rem 0;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.loading-skeleton {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.skeleton-hero {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 2rem;
  margin-bottom: 3rem;
  padding-bottom: 2rem;
}

.skeleton-hero-content {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.skeleton-meta-group {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-top: 0.5rem;
}

.skeleton-stats {
  display: flex;
  gap: 1.5rem;
}

.skeleton-hero-sidebar {
  position: sticky;
  top: 2rem;
  height: fit-content;
}

.skeleton-body {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 2rem;
}

.skeleton-main {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.skeleton-side {
  position: sticky;
  top: 2rem;
  height: fit-content;
}

.error {
  text-align: center;
  padding: 4rem 2rem;
  font-size: 1.25rem;
  color: var(--error);
}

@media (max-width: 1024px) {
  .skeleton-hero {
    grid-template-columns: 1fr;
  }

  .skeleton-body {
    grid-template-columns: 1fr;
  }
}

.course-hero {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 2rem;
  margin-bottom: 3rem;
  padding-bottom: 2rem;
  border-bottom: 2px solid var(--border-color);
}

.hero-content {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.course-header {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.course-category {
  display: inline-block;
  padding: 0.5rem 1rem;
  background: #e0e7ff;
  color: #6366f1;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 600;
  width: fit-content;
}

.course-header h1 {
  font-size: 2.5rem;
  margin: 0;
  line-height: 1.2;
}

.course-subtitle {
  font-size: 1.25rem;
  color: #6b7280;
  margin: 0;
}

.course-meta {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.instructor-info {
  font-size: 1rem;
  color: #374151;
}

.course-stats {
  display: flex;
  gap: 1.5rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.difficulty {
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: capitalize;
}

.difficulty.beginner {
  background: #d1fae5;
  color: #059669;
}

.difficulty.intermediate {
  background: #fef3c7;
  color: #d97706;
}

.difficulty.advanced {
  background: #fee2e2;
  color: #dc2626;
}

.hero-sidebar {
  position: sticky;
  top: 2rem;
  height: fit-content;
}

.purchase-card {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.price {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary-color);
}

.enroll-button {
  width: 100%;
}

.course-features {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.course-features li {
  padding-left: 1.5rem;
  position: relative;
  color: #374151;
}

.course-features li::before {
  content: '✓';
  position: absolute;
  left: 0;
  color: #10b981;
  font-weight: 700;
}

.course-content {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 2rem;
}

.main-content {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.course-description {
  line-height: 1.8;
  color: #374151;
}

.course-description p {
  margin-bottom: 1.5rem;
}

.learning-objectives {
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid var(--border-color);
}

.learning-objectives h3 {
  font-size: 1.25rem;
  margin-bottom: 1rem;
}

.learning-objectives ul {
  list-style: none;
  padding: 0;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.75rem;
}

.learning-objectives li {
  padding-left: 1.5rem;
  position: relative;
}

.learning-objectives li::before {
  content: '✓';
  position: absolute;
  left: 0;
  color: #10b981;
  font-weight: 700;
}

.sidebar {
  position: sticky;
  top: 2rem;
  height: fit-content;
}

.course-details {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.course-details dt {
  font-weight: 600;
  color: #6b7280;
  font-size: 0.875rem;
}

.course-details dd {
  margin: 0;
  color: #374151;
  font-size: 1rem;
}

@media (max-width: 1024px) {
  .course-hero {
    grid-template-columns: 1fr;
  }

  .course-content {
    grid-template-columns: 1fr;
  }

  .hero-sidebar,
  .sidebar {
    position: static;
  }
}
</style>

