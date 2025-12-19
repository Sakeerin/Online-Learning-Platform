<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useEnrollment } from '@/composables/useEnrollment'
import Button from '@/components/common/Button.vue'

interface Props {
  courseId: string
  price: number
  currency?: string
  isEnrolled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  currency: 'THB',
  isEnrolled: false,
})

const emit = defineEmits<{
  enrolled: [enrollment: any]
}>()

const router = useRouter()
const { enroll, isLoading, error } = useEnrollment()
const enrollmentError = ref<string | null>(null)

const formattedPrice = computed(() => {
  if (props.price === 0) {
    return 'Free'
  }
  return `${props.price.toFixed(2)} ${props.currency}`
})

const handleEnroll = async () => {
  enrollmentError.value = null

  try {
    const enrollment = await enroll(props.courseId)
    emit('enrolled', enrollment)
    
    // Redirect to course player
    router.push({ name: 'course-player', params: { courseId: props.courseId } })
  } catch (err: any) {
    enrollmentError.value = err.response?.data?.message || 'Failed to enroll in course'
  }
}

const handleContinue = () => {
  router.push({ name: 'course-player', params: { courseId: props.courseId } })
}
</script>

<template>
  <div class="enrollment-button">
    <div v-if="enrollmentError" class="error-message">
      {{ enrollmentError }}
    </div>
    <Button
      v-if="!isEnrolled"
      size="lg"
      :loading="isLoading"
      @click="handleEnroll"
      class="enroll-btn"
    >
      {{ price === 0 ? 'Enroll for Free' : `Enroll Now - ${formattedPrice}` }}
    </Button>
    <Button
      v-else
      size="lg"
      @click="handleContinue"
      class="continue-btn"
    >
      Continue Learning
    </Button>
  </div>
</template>

<style scoped>
.enrollment-button {
  width: 100%;
}

.error-message {
  color: var(--error);
  font-size: 0.875rem;
  margin-bottom: 0.5rem;
}

.enroll-btn,
.continue-btn {
  width: 100%;
}
</style>

