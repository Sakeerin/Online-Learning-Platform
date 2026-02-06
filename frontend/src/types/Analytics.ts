export interface InstructorAnalytics {
  total_courses: number
  total_enrollments: number
  total_revenue: number
  total_platform_fees: number
  net_revenue: number
  enrollment_trends: TrendData[]
  revenue_trends: TrendData[]
  top_courses: TopCourse[]
  completion_rate: number
}

export interface CourseAnalytics {
  course_id: string
  course_title: string
  total_enrollments: number
  total_revenue: number
  completion_rate: number
  average_rating: number
  total_reviews: number
  lesson_analytics: LessonAnalytics[]
  enrollment_trends: TrendData[]
  student_demographics: StudentDemographics
}

export interface LessonAnalytics {
  lesson_id: string
  lesson_title: string
  lesson_type: 'video' | 'quiz' | 'article'
  section_title: string
  total_views: number
  completion_count: number
  completion_rate: number
  average_watch_time: number
  lesson_duration: number
}

export interface TrendData {
  date: string
  count?: number
  revenue?: number
}

export interface TopCourse {
  course_id: string
  course_title: string
  enrollments: number
  revenue: number
}

export interface RevenueBreakdown {
  course_id?: string
  course_title?: string
  date?: string
  month?: string
  total_revenue: number
  total_amount: number
  platform_fees: number
  transaction_count: number
}

export interface StudentDemographics {
  total_students: number
  by_country: Record<string, number>
  by_enrollment_date: Record<string, number>
}

export interface AnalyticsFilters {
  date_from?: string
  date_to?: string
  group_by?: 'course' | 'day' | 'month'
}

