export interface Course {
  id: string
  title: string
  subtitle?: string
  description: string
  learning_objectives?: string[]
  category: string
  subcategory?: string
  difficulty_level: 'beginner' | 'intermediate' | 'advanced'
  thumbnail?: string
  price: number
  currency: string
  status: 'draft' | 'published' | 'unpublished'
  published_at?: string
  average_rating?: number
  total_enrollments: number
  total_reviews: number
  instructor?: {
    id: string
    name: string
    email: string
    role: string
    profile_photo?: string
    bio?: string
  }
  sections?: Section[]
  created_at: string
  updated_at: string
}

export interface Section {
  id: string
  course_id: string
  title: string
  description?: string
  order: number
  lessons?: Lesson[]
  created_at: string
  updated_at: string
}

export interface Lesson {
  id: string
  section_id: string
  title: string
  type: 'video' | 'quiz' | 'article'
  content: {
    video_url?: string
    video_path?: string
    thumbnail?: string
    filename?: string
    instructions?: string
    content?: string
    [key: string]: any
  }
  duration?: number
  is_preview: boolean
  order: number
  created_at: string
  updated_at: string
}

export interface CreateCourseData {
  title: string
  subtitle?: string
  description: string
  learning_objectives?: string[]
  category: string
  subcategory?: string
  difficulty_level: 'beginner' | 'intermediate' | 'advanced'
  thumbnail?: string
  price: number
  currency?: string
}

export interface UpdateCourseData extends Partial<CreateCourseData> {}

export interface CreateSectionData {
  title: string
  description?: string
  order?: number
}

export interface UpdateSectionData extends Partial<CreateSectionData> {}

export interface CreateLessonData {
  title: string
  type: 'video' | 'quiz' | 'article'
  content: Record<string, any>
  duration?: number
  is_preview?: boolean
  order?: number
}

export interface UpdateLessonData extends Partial<CreateLessonData> {}

export interface PaginatedResponse<T> {
  data: T[]
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

