import type { User } from './User'
import type { Course } from './Course'

export interface Review {
  id: string
  user_id: string
  course_id: string
  rating: number
  review_text?: string
  helpful_votes: number
  instructor_response?: string
  responded_at?: string
  is_flagged: boolean
  user?: User
  course?: Course
  created_at: string
  updated_at: string
}

export interface CreateReviewData {
  rating: number
  review_text?: string
}

export interface UpdateReviewData {
  rating?: number
  review_text?: string
}

export interface ReviewResponse {
  response: string
}

