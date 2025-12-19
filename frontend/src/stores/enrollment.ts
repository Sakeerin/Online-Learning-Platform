import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Enrollment, Progress } from '@/types/Enrollment'
import enrollmentService from '@/services/enrollmentService'

export const useEnrollmentStore = defineStore('enrollment', () => {
  const enrollments = ref<Enrollment[]>([])
  const currentEnrollment = ref<Enrollment | null>(null)
  const pagination = ref<{
    current_page: number
    last_page: number
    per_page: number
    total: number
  } | null>(null)

  // Computed
  const activeEnrollments = computed(() => enrollments.value.filter((e) => !e.is_completed))
  const completedEnrollments = computed(() => enrollments.value.filter((e) => e.is_completed))

  // Actions
  async function fetchEnrollments(params?: { is_completed?: boolean; page?: number; per_page?: number }) {
    const response = await enrollmentService.getEnrollments(params)
    enrollments.value = response.data
    pagination.value = response.meta
    return response
  }

  async function fetchEnrollment(enrollmentId: string): Promise<Enrollment> {
    const enrollment = await enrollmentService.getEnrollment(enrollmentId)
    currentEnrollment.value = enrollment
    return enrollment
  }

  async function enroll(courseId: string): Promise<Enrollment> {
    const enrollment = await enrollmentService.enroll({ course_id: courseId })
    enrollments.value.unshift(enrollment)
    return enrollment
  }

  function clearCurrentEnrollment() {
    currentEnrollment.value = null
  }

  return {
    enrollments,
    currentEnrollment,
    pagination,
    activeEnrollments,
    completedEnrollments,
    fetchEnrollments,
    fetchEnrollment,
    enroll,
    clearCurrentEnrollment,
  }
})

