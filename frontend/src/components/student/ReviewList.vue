<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import ReviewForm from './ReviewForm.vue'
import Button from '@/components/common/Button.vue'
import Card from '@/components/common/Card.vue'
import type { Review } from '@/types/Review'
import reviewService from '@/services/reviewService'
import { useAuthStore } from '@/stores/auth'

interface Props {
  courseId: string
  isEnrolled?: boolean
  isInstructor?: boolean
  instructorId?: string
}

const props = withDefaults(defineProps<Props>(), {
  isEnrolled: false,
  isInstructor: false,
})

const authStore = useAuthStore()
const reviews = ref<Review[]>([])
const myReview = ref<Review | null>(null)
const isLoading = ref(false)
const error = ref<string | null>(null)
const showReviewForm = ref(false)
const showResponseForm = ref<string | null>(null)
const currentPage = ref(1)
const totalPages = ref(1)
const sortBy = ref<'created_at' | 'helpful'>('created_at')

const canWriteReview = computed(() => {
  return props.isEnrolled && !myReview.value && !props.isInstructor && authStore.user?.role === 'student'
})

const canRespondToReview = computed(() => {
  return props.isInstructor && authStore.user?.id === props.instructorId
})

const stars = [1, 2, 3, 4, 5]

const fetchReviews = async () => {
  isLoading.value = true
  error.value = null

  try {
    const response = await reviewService.getCourseReviews(props.courseId, {
      sort_by: sortBy.value,
      sort_order: 'desc',
      page: currentPage.value,
      per_page: 10,
    })

    reviews.value = response.data
    totalPages.value = response.meta.last_page
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load reviews'
  } finally {
    isLoading.value = false
  }
}

const fetchMyReview = async () => {
  if (!authStore.user || props.isInstructor) return

  try {
    myReview.value = await reviewService.getMyReview(props.courseId)
  } catch (err) {
    // User might not have a review yet, which is fine
    myReview.value = null
  }
}

const handleReviewSaved = (review: Review) => {
  myReview.value = review
  showReviewForm.value = false
  fetchReviews()
}

const handleFlagReview = async (reviewId: string) => {
  if (!confirm('Flag this review as inappropriate?')) return

  try {
    await reviewService.flagReview(props.courseId, reviewId)
    await fetchReviews()
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to flag review')
  }
}

const handleResponseSaved = () => {
  showResponseForm.value = null
  fetchReviews()
}

onMounted(async () => {
  await Promise.all([fetchReviews(), fetchMyReview()])
})
</script>

<template>
  <div class="review-list">
    <div class="reviews-header">
      <h2 class="section-title">Reviews</h2>
      <div class="sort-controls">
        <label for="sort-select">Sort by:</label>
        <select id="sort-select" v-model="sortBy" class="select" @change="fetchReviews">
          <option value="created_at">Most Recent</option>
          <option value="helpful">Most Helpful</option>
        </select>
      </div>
    </div>

    <!-- My Review Section -->
    <div v-if="canWriteReview && !showReviewForm" class="my-review-section">
      <Card>
        <p>Share your experience with this course!</p>
        <Button @click="showReviewForm = true">Write a Review</Button>
      </Card>
    </div>

    <!-- Review Form -->
    <div v-if="showReviewForm || (myReview && !props.isInstructor)" class="review-form-section">
      <Card>
        <ReviewForm
          :course-id="courseId"
          :review="myReview"
          @saved="handleReviewSaved"
          @cancelled="showReviewForm = false"
        />
      </Card>
    </div>

    <!-- Error State -->
    <div v-if="error" class="error-message" role="alert">{{ error }}</div>

    <!-- Loading State -->
    <div v-if="isLoading && reviews.length === 0" class="loading">Loading reviews...</div>

    <!-- Reviews List -->
    <div v-else-if="reviews.length > 0" class="reviews">
      <div v-for="review in reviews" :key="review.id" class="review-item">
        <Card>
          <div class="review-header">
            <div class="reviewer-info">
              <div class="reviewer-avatar">
                {{ review.user?.name?.charAt(0).toUpperCase() || 'U' }}
              </div>
              <div>
                <div class="reviewer-name">{{ review.user?.name || 'Anonymous' }}</div>
                <div class="review-date">{{ new Date(review.created_at).toLocaleDateString() }}</div>
              </div>
            </div>
            <div class="review-rating">
              <div class="stars-display">
                <span
                  v-for="star in stars"
                  :key="star"
                  :class="['star', { filled: star <= review.rating }]"
                >
                  ⭐
                </span>
              </div>
            </div>
          </div>

          <div v-if="review.review_text" class="review-text">{{ review.review_text }}</div>

          <!-- Instructor Response -->
          <div v-if="review.instructor_response" class="instructor-response">
            <div class="response-header">
              <strong>Instructor Response</strong>
              <span v-if="review.responded_at" class="response-date">
                {{ new Date(review.responded_at).toLocaleDateString() }}
              </span>
            </div>
            <p>{{ review.instructor_response }}</p>
          </div>

          <!-- Response Form for Instructor -->
          <div
            v-if="canRespondToReview && !review.instructor_response && showResponseForm !== review.id"
            class="response-form-section"
          >
            <Button variant="outline" size="sm" @click="showResponseForm = review.id">Respond</Button>
          </div>

          <div v-if="showResponseForm === review.id" class="response-form">
            <ReviewForm
              :course-id="courseId"
              :review="review"
              :is-instructor="true"
              :instructor-can-respond="true"
              @saved="handleResponseSaved"
              @cancelled="showResponseForm = null"
            />
          </div>

          <div class="review-actions">
            <button
              v-if="!props.isInstructor && authStore.user"
              class="flag-button"
              @click="handleFlagReview(review.id)"
            >
              Flag as inappropriate
            </button>
          </div>
        </Card>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!isLoading" class="empty-state">
      <p>No reviews yet. Be the first to review this course!</p>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="pagination">
      <Button
        variant="outline"
        :disabled="currentPage === 1"
        @click="currentPage--; fetchReviews()"
      >
        Previous
      </Button>
      <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
      <Button
        variant="outline"
        :disabled="currentPage === totalPages"
        @click="currentPage++; fetchReviews()"
      >
        Next
      </Button>
    </div>
  </div>
</template>

<style scoped>
.review-list {
  margin-top: 2rem;
}

.reviews-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-title {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
}

.sort-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.select {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
}

.my-review-section,
.review-form-section {
  margin-bottom: 1.5rem;
}

.error-message {
  padding: 1rem;
  background: #fee2e2;
  color: #dc2626;
  border-radius: 6px;
  margin-bottom: 1rem;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
}

.reviews {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.review-item {
  margin-bottom: 1rem;
}

.review-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.reviewer-info {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.reviewer-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #3b82f6;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
}

.reviewer-name {
  font-weight: 600;
  color: #111827;
}

.review-date {
  font-size: 0.875rem;
  color: #6b7280;
}

.review-rating {
  display: flex;
  align-items: center;
}

.stars-display {
  display: flex;
  gap: 0.25rem;
}

.star {
  font-size: 1.25rem;
  filter: grayscale(100%);
}

.star.filled {
  filter: grayscale(0%);
}

.review-text {
  margin: 1rem 0;
  color: #374151;
  line-height: 1.6;
}

.instructor-response {
  margin-top: 1rem;
  padding: 1rem;
  background: #f3f4f6;
  border-left: 3px solid #3b82f6;
  border-radius: 4px;
}

.response-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.response-date {
  font-size: 0.875rem;
  color: #6b7280;
}

.response-form-section {
  margin-top: 1rem;
}

.response-form {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
}

.review-actions {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
}

.flag-button {
  background: none;
  border: none;
  color: #6b7280;
  font-size: 0.875rem;
  cursor: pointer;
  text-decoration: underline;
}

.flag-button:hover {
  color: #dc2626;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
}

.page-info {
  color: #6b7280;
  font-size: 0.875rem;
}
</style>

