import { ref, computed } from 'vue'
import courseService from '@/services/courseService'
import type { Course, PaginatedResponse } from '@/types/Course'

// T119: Simple debounce function
function debounce<T extends (...args: any[]) => any>(func: T, wait: number): (...args: Parameters<T>) => void {
  let timeout: ReturnType<typeof setTimeout> | null = null
  return function executedFunction(...args: Parameters<T>) {
    const later = () => {
      timeout = null
      func(...args)
    }
    if (timeout) clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

export interface CourseFilters {
  category?: string
  subcategory?: string
  difficulty_level?: string
  min_price?: number
  max_price?: number
  free_only?: boolean
  min_rating?: number
  sort_by?: 'relevance' | 'price_asc' | 'price_desc' | 'rating' | 'enrollments' | 'newest'
}

export function useCourseDiscovery() {
  const courses = ref<Course[]>([])
  const featuredCourses = ref<Course[]>([])
  const currentCourse = ref<Course | null>(null)
  const searchQuery = ref('')
  const filters = ref<CourseFilters>({})
  const isLoading = ref(false)
  const isSearching = ref(false)
  const error = ref<string | null>(null)
  const pagination = ref<{
    current_page: number
    last_page: number
    per_page: number
    total: number
  } | null>(null)

  // T119: Debounced search function
  const performSearch = async (query: string) => {
    if (!query || query.length < 2) {
      courses.value = []
      pagination.value = null
      return
    }

    isSearching.value = true
    error.value = null

    try {
      const response = await courseService.searchCourses(query, filters.value)
      courses.value = response.data
      pagination.value = response.meta
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Search failed'
      courses.value = []
    } finally {
      isSearching.value = false
    }
  }

  const debouncedSearch = debounce(performSearch, 500)

  async function browseCourses(params?: CourseFilters & { page?: number; per_page?: number }) {
    isLoading.value = true
    error.value = null

    try {
      const response = await courseService.browseCourses(params)
      courses.value = response.data
      pagination.value = response.meta
      return response
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch courses'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function searchCourses(query: string, params?: CourseFilters & { page?: number; per_page?: number }) {
    searchQuery.value = query
    filters.value = { ...filters.value, ...params }
    await debouncedSearch(query)
  }

  async function fetchFeaturedCourses() {
    isLoading.value = true
    error.value = null

    try {
      featuredCourses.value = await courseService.getFeaturedCourses()
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch featured courses'
    } finally {
      isLoading.value = false
    }
  }

  async function fetchCourse(courseId: string) {
    isLoading.value = true
    error.value = null

    try {
      currentCourse.value = await courseService.getPublicCourse(courseId)
      return currentCourse.value
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch course'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  function updateFilters(newFilters: CourseFilters) {
    filters.value = { ...filters.value, ...newFilters }
  }

  function clearFilters() {
    filters.value = {}
  }

  function clearSearch() {
    searchQuery.value = ''
    courses.value = []
    pagination.value = null
  }

  return {
    courses,
    featuredCourses,
    currentCourse,
    searchQuery,
    filters,
    isLoading,
    isSearching,
    error,
    pagination,
    browseCourses,
    searchCourses,
    fetchFeaturedCourses,
    fetchCourse,
    updateFilters,
    clearFilters,
    clearSearch,
  }
}

