import api from './api'
import type { Review, CreateReviewData, UpdateReviewData, ReviewResponse } from '@/types/Review'

const reviewService = {
  async getCourseReviews(
    courseId: string,
    params?: {
      rating?: number
      sort_by?: string
      sort_order?: 'asc' | 'desc'
      page?: number
      per_page?: number
    }
  ) {
    const response = await api.get<{ data: Review[]; meta: any }>(`/courses/${courseId}/reviews`, { params })
    return {
      data: response.data.data,
      meta: response.data.meta,
    }
  },

  async getReview(courseId: string, reviewId: string): Promise<Review> {
    const response = await api.get<{ data: Review }>(`/courses/${courseId}/reviews/${reviewId}`)
    return response.data.data
  },

  async getMyReview(courseId: string): Promise<Review | null> {
    const response = await api.get<{ data: Review | null }>(`/student/courses/${courseId}/reviews/my`)
    return response.data.data
  },

  async createReview(courseId: string, data: CreateReviewData): Promise<Review> {
    const response = await api.post<{ data: Review }>(`/student/courses/${courseId}/reviews`, data)
    return response.data.data
  },

  async updateReview(courseId: string, reviewId: string, data: UpdateReviewData): Promise<Review> {
    const response = await api.put<{ data: Review }>(`/student/courses/${courseId}/reviews/${reviewId}`, data)
    return response.data.data
  },

  async deleteReview(courseId: string, reviewId: string): Promise<void> {
    await api.delete(`/student/courses/${courseId}/reviews/${reviewId}`)
  },

  async flagReview(courseId: string, reviewId: string): Promise<Review> {
    const response = await api.post<{ data: Review }>(`/student/courses/${courseId}/reviews/${reviewId}/flag`)
    return response.data.data
  },

  async addInstructorResponse(courseId: string, reviewId: string, data: ReviewResponse): Promise<Review> {
    const response = await api.post<{ data: Review }>(
      `/instructor/courses/${courseId}/reviews/${reviewId}/respond`,
      data
    )
    return response.data.data
  },
}

export default reviewService

