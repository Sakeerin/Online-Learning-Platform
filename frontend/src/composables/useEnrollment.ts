import { ref, computed } from 'vue'
import { useEnrollmentStore } from '@/stores/enrollment'
import type { Enrollment } from '@/types/Enrollment'

export function useEnrollment() {
  const enrollmentStore = useEnrollmentStore()
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const enrollments = computed(() => enrollmentStore.enrollments)
  const currentEnrollment = computed(() => enrollmentStore.currentEnrollment)
  const activeEnrollments = computed(() => enrollmentStore.activeEnrollments)
  const completedEnrollments = computed(() => enrollmentStore.completedEnrollments)

  async function fetchEnrollments(params?: { is_completed?: boolean; page?: number; per_page?: number }) {
    isLoading.value = true
    error.value = null

    try {
      const response = await enrollmentStore.fetchEnrollments(params)
      return response
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch enrollments'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function fetchEnrollment(enrollmentId: string) {
    isLoading.value = true
    error.value = null

    try {
      const enrollment = await enrollmentStore.fetchEnrollment(enrollmentId)
      return enrollment
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch enrollment'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function enroll(courseId: string) {
    isLoading.value = true
    error.value = null

    try {
      const enrollment = await enrollmentStore.enroll(courseId)
      return enrollment
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to enroll in course'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  return {
    enrollments,
    currentEnrollment,
    activeEnrollments,
    completedEnrollments,
    isLoading,
    error,
    fetchEnrollments,
    fetchEnrollment,
    enroll,
  }
}

