import type { Course } from './Course'

export interface Enrollment {
  id: string
  user_id: string
  course_id: string
  enrolled_at: string
  last_accessed_at?: string
  progress_percentage: number
  is_completed: boolean
  completed_at?: string
  course?: Course
  progress?: Progress[]
  created_at: string
  updated_at: string
}

export interface Progress {
  id: string
  enrollment_id: string
  lesson_id: string
  is_completed: boolean
  video_position?: number
  completed_at?: string
  lesson?: {
    id: string
    title: string
    type: string
    duration?: number
  }
  created_at: string
  updated_at: string
}

export interface CreateEnrollmentData {
  course_id: string
}

export interface UpdateProgressData {
  position: number
}

