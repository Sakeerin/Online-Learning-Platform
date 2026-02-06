import api from './api'
import type {
  InstructorAnalytics,
  CourseAnalytics,
  RevenueBreakdown,
  LessonAnalytics,
  AnalyticsFilters,
} from '@/types/Analytics'

const analyticsService = {
  /**
   * Get overall analytics for the instructor
   */
  async getInstructorAnalytics(filters?: AnalyticsFilters): Promise<InstructorAnalytics> {
    const params = new URLSearchParams()
    if (filters?.date_from) params.append('date_from', filters.date_from)
    if (filters?.date_to) params.append('date_to', filters.date_to)

    const response = await api.get<{ data: InstructorAnalytics }>(
      `/instructor/analytics?${params.toString()}`
    )
    return response.data.data
  },

  /**
   * Get analytics for a specific course
   */
  async getCourseAnalytics(courseId: string, filters?: AnalyticsFilters): Promise<CourseAnalytics> {
    const params = new URLSearchParams()
    if (filters?.date_from) params.append('date_from', filters.date_from)
    if (filters?.date_to) params.append('date_to', filters.date_to)

    const response = await api.get<{ data: CourseAnalytics }>(
      `/instructor/courses/${courseId}/analytics?${params.toString()}`
    )
    return response.data.data
  },

  /**
   * Get revenue breakdown
   */
  async getRevenueBreakdown(filters?: AnalyticsFilters): Promise<RevenueBreakdown[]> {
    const params = new URLSearchParams()
    if (filters?.date_from) params.append('date_from', filters.date_from)
    if (filters?.date_to) params.append('date_to', filters.date_to)
    if (filters?.group_by) params.append('group_by', filters.group_by)

    const response = await api.get<{ data: RevenueBreakdown[] }>(
      `/instructor/analytics/revenue?${params.toString()}`
    )
    return response.data.data
  },

  /**
   * Get lesson analytics for a course
   */
  async getLessonAnalytics(courseId: string): Promise<LessonAnalytics[]> {
    const response = await api.get<{ data: LessonAnalytics[] }>(
      `/instructor/courses/${courseId}/analytics/lessons`
    )
    return response.data.data
  },
}

export default analyticsService

