export interface User {
  id: string
  name: string
  email: string
  role: 'student' | 'instructor' | 'admin'
  profile_photo?: string
  bio?: string
  email_verified_at?: string
  created_at: string
  updated_at: string
}

export interface AuthResponse {
  user: User
  token: string
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterData {
  name: string
  email: string
  password: string
  password_confirmation: string
  role: 'student' | 'instructor'
}

