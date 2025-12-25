export interface Certificate {
  id: string
  enrollment_id: string
  user_id: string
  course_id: string
  verification_code: string
  certificate_url: string | null
  issued_at: string
  user?: {
    id: string
    name: string
    email: string
  }
  course?: {
    id: string
    title: string
    subtitle?: string
  }
  enrollment?: {
    id: string
    progress_percentage: number
    is_completed: boolean
  }
  created_at: string
  updated_at: string
}

export interface CertificateVerification {
  valid: boolean
  data?: Certificate
  message?: string
}

