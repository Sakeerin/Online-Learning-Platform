import type { User } from './User'
import type { Course } from './Course'
import type { Lesson } from './Course'

export interface Discussion {
  id: string
  course_id: string
  lesson_id?: string
  user_id: string
  question: string
  upvotes: number
  is_answered: boolean
  user?: User
  course?: Course
  lesson?: Lesson
  replies?: Reply[]
  replies_count?: number
  created_at: string
  updated_at: string
}

export interface Reply {
  id: string
  discussion_id: string
  user_id: string
  reply_text: string
  upvotes: number
  is_instructor_reply: boolean
  user?: User
  discussion?: Discussion
  created_at: string
  updated_at: string
}

export interface CreateDiscussionData {
  question: string
  lesson_id?: string
}

export interface CreateReplyData {
  reply_text: string
}

export interface DiscussionFilters {
  lesson_id?: string
  is_answered?: boolean
  search?: string
  sort_by?: 'created_at' | 'upvotes'
  sort_order?: 'asc' | 'desc'
  per_page?: number
}

export interface ReplyFilters {
  instructor_only?: boolean
  sort_by?: 'created_at' | 'upvotes'
  sort_order?: 'asc' | 'desc'
  per_page?: number
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

