<script setup lang="ts">
import { ref, computed } from 'vue'
import Button from '@/components/common/Button.vue'
import type { Review, CreateReviewData, UpdateReviewData, ReviewResponse } from '@/types/Review'
import reviewService from '@/services/reviewService'

interface Props {
  courseId: string
  review?: Review | null
  isInstructor?: boolean
  instructorCanRespond?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  isInstructor: false,
  instructorCanRespond: false,
})

const emit = defineEmits<{
  saved: [review: Review]
  cancelled: []
}>()

const rating = ref(props.review?.rating || 0)
const reviewText = ref(props.review?.review_text || '')
const instructorResponse = ref(props.review?.instructor_response || '')
const isLoading = ref(false)
const error = ref<string | null>(null)
const isEditing = computed(() => !!props.review)

const stars = [1, 2, 3, 4, 5]

const selectRating = (value: number) => {
  if (!isEditing.value && !props.isInstructor) {
    rating.value = value
  }
}

const handleSubmit = async () => {
  if (!props.isInstructor && rating.value === 0) {
    error.value = 'Please select a rating'
    return
  }

  error.value = null
  isLoading.value = true

  try {
    let updatedReview: Review

    if (props.isInstructor && props.instructorCanRespond && instructorResponse.value) {
      // Instructor responding to review
      updatedReview = await reviewService.addInstructorResponse(
        props.courseId,
        props.review!.id,
        { response: instructorResponse.value }
      )
    } else if (isEditing.value) {
      // Update existing review
      const data: UpdateReviewData = {
        rating: rating.value,
        review_text: reviewText.value || undefined,
      }
      updatedReview = await reviewService.updateReview(props.courseId, props.review!.id, data)
    } else {
      // Create new review
      const data: CreateReviewData = {
        rating: rating.value,
        review_text: reviewText.value || undefined,
      }
      updatedReview = await reviewService.createReview(props.courseId, data)
    }

    emit('saved', updatedReview)
    // Reset form if creating new review
    if (!isEditing.value) {
      rating.value = 0
      reviewText.value = ''
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to save review'
  } finally {
    isLoading.value = false
  }
}

const handleCancel = () => {
  emit('cancelled')
}
</script>

<template>
  <div class="review-form">
    <h3 v-if="!isInstructor" class="form-title">{{ isEditing ? 'Edit Your Review' : 'Write a Review' }}</h3>
    <h3 v-else class="form-title">Respond to Review</h3>

    <div v-if="error" class="error-message" role="alert">{{ error }}</div>

    <div v-if="!isInstructor" class="rating-section">
      <label class="rating-label">Rating <span class="required">*</span></label>
      <div class="stars">
        <button
          v-for="star in stars"
          :key="star"
          type="button"
          :class="['star-button', { active: star <= rating, disabled: isEditing }]"
          :disabled="isEditing"
          :aria-label="`Rate ${star} star${star !== 1 ? 's' : ''}`"
          @click="selectRating(star)"
        >
          ⭐
        </button>
      </div>
    </div>

    <div v-if="!isInstructor" class="form-group">
      <label for="review-text" class="form-label">Review (optional)</label>
      <textarea
        id="review-text"
        v-model="reviewText"
        class="textarea"
        placeholder="Share your thoughts about this course..."
        rows="5"
        maxlength="1000"
      ></textarea>
      <div class="char-count">{{ reviewText.length }} / 1000 characters</div>
    </div>

    <div v-if="isInstructor && instructorCanRespond" class="form-group">
      <label for="instructor-response" class="form-label">Your Response</label>
      <textarea
        id="instructor-response"
        v-model="instructorResponse"
        class="textarea"
        placeholder="Thank the student for their feedback..."
        rows="4"
        maxlength="1000"
      ></textarea>
      <div class="char-count">{{ instructorResponse.length }} / 1000 characters</div>
    </div>

    <div class="form-actions">
      <Button type="submit" :loading="isLoading" @click="handleSubmit">
        {{ isInstructor ? 'Post Response' : isEditing ? 'Update Review' : 'Submit Review' }}
      </Button>
      <Button v-if="isEditing || isInstructor" variant="outline" @click="handleCancel">Cancel</Button>
    </div>
  </div>
</template>

<style scoped>
.review-form {
  padding: 1.5rem;
  background: #fff;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

.form-title {
  margin: 0 0 1.5rem 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
}

.error-message {
  padding: 0.75rem;
  margin-bottom: 1rem;
  background: #fee2e2;
  color: #dc2626;
  border-radius: 4px;
  font-size: 0.875rem;
}

.rating-section {
  margin-bottom: 1.5rem;
}

.rating-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.required {
  color: #dc2626;
}

.stars {
  display: flex;
  gap: 0.5rem;
}

.star-button {
  background: none;
  border: none;
  font-size: 2rem;
  cursor: pointer;
  padding: 0;
  transition: transform 0.2s;
  filter: grayscale(100%);
}

.star-button:hover:not(.disabled) {
  transform: scale(1.1);
}

.star-button.active {
  filter: grayscale(0%);
}

.star-button.disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-family: inherit;
  font-size: 0.875rem;
  resize: vertical;
  transition: border-color 0.2s;
}

.textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.char-count {
  margin-top: 0.25rem;
  font-size: 0.75rem;
  color: #6b7280;
  text-align: right;
}

.form-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
}
</style>

