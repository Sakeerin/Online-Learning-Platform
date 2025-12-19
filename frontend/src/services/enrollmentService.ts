import api from './api'
import type { Enrollment, Progress, CreateEnrollmentData, UpdateProgressData } from '@/types/Enrollment'

const enrollmentService = {
  async getEnrollments(params?: { is_completed?: boolean; page?: number; per_page?: number }) {
    const response = await api.get<{ data: Enrollment[]; meta: any }>('/student/enrollments', { params })
    return {
      data: response.data.data,
      meta: response.data.meta,
    }
  },

  async getEnrollment(enrollmentId: string): Promise<Enrollment> {
    const response = await api.get<{ data: Enrollment }>(`/student/enrollments/${enrollmentId}`)
    return response.data.data
  },

  async enroll(data: CreateEnrollmentData): Promise<Enrollment> {
    const response = await api.post<{ data: Enrollment }>('/student/enrollments', data)
    return response.data.data
  },

  async getCourseLearning(courseId: string) {
    const response = await api.get(`/student/courses/${courseId}/learning`)
    return response.data.data
  },

  async getVideoUrl(courseId: string, lessonId: string) {
    const response = await api.get<{
      video_url: string
      expires_at: string
      lesson: {
        id: string
        title: string
        duration?: number
      }
    }>(`/student/courses/${courseId}/lessons/${lessonId}/video-url`)
    return response.data
  },

  async getProgress(enrollmentId: string, lessonId: string): Promise<Progress | null> {
    const response = await api.get<{ data: Progress | null }>(
      `/student/enrollments/${enrollmentId}/lessons/${lessonId}/progress`
    )
    return response.data.data
  },

  async getAllProgress(enrollmentId: string): Promise<Progress[]> {
    const response = await api.get<{ data: Progress[] }>(`/student/enrollments/${enrollmentId}/progress`)
    return response.data.data
  },

  async updatePosition(enrollmentId: string, lessonId: string, data: UpdateProgressData): Promise<Progress> {
    const response = await api.put<{ data: Progress }>(
      `/student/enrollments/${enrollmentId}/lessons/${lessonId}/progress/position`,
      data
    )
    return response.data.data
  },

  async markComplete(enrollmentId: string, lessonId: string): Promise<Progress> {
    const response = await api.post<{ data: Progress }>(
      `/student/enrollments/${enrollmentId}/lessons/${lessonId}/progress/complete`
    )
    return response.data.data
  },
}

export default enrollmentService

