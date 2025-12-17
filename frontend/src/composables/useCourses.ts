import { ref, computed } from 'vue'
import { useCourseStore } from '@/stores/course'
import type { Course, CreateCourseData, UpdateCourseData, PaginatedResponse } from '@/types/Course'

export function useCourses() {
  const courseStore = useCourseStore()
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const courses = computed(() => courseStore.courses)
  const currentCourse = computed(() => courseStore.currentCourse)

  async function fetchCourses(params?: { status?: string; page?: number; per_page?: number }) {
    isLoading.value = true
    error.value = null

    try {
      const response = await courseStore.fetchCourses(params)
      return response
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch courses'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function fetchCourse(courseId: string) {
    isLoading.value = true
    error.value = null

    try {
      const course = await courseStore.fetchCourse(courseId)
      return course
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch course'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function createCourse(data: CreateCourseData) {
    isLoading.value = true
    error.value = null

    try {
      const course = await courseStore.createCourse(data)
      return course
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create course'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function updateCourse(courseId: string, data: UpdateCourseData) {
    isLoading.value = true
    error.value = null

    try {
      const course = await courseStore.updateCourse(courseId, data)
      return course
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update course'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function deleteCourse(courseId: string) {
    isLoading.value = true
    error.value = null

    try {
      await courseStore.deleteCourse(courseId)
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete course'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function publishCourse(courseId: string) {
    isLoading.value = true
    error.value = null

    try {
      const course = await courseStore.publishCourse(courseId)
      return course
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to publish course'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function unpublishCourse(courseId: string) {
    isLoading.value = true
    error.value = null

    try {
      const course = await courseStore.unpublishCourse(courseId)
      return course
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to unpublish course'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  return {
    courses,
    currentCourse,
    isLoading,
    error,
    fetchCourses,
    fetchCourse,
    createCourse,
    updateCourse,
    deleteCourse,
    publishCourse,
    unpublishCourse,
  }
}

