import api from './api'
import type { User, LoginCredentials, RegisterData, AuthResponse } from '@/types/User'

const authService = {
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/auth/login', credentials)
    return response.data
  },

  async register(data: RegisterData): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/auth/register', data)
    return response.data
  },

  async logout(): Promise<void> {
    await api.post('/auth/logout')
  },

  async getUser(): Promise<User> {
    const response = await api.get<{ data: User }>('/auth/user')
    return response.data.data
  },

  async forgotPassword(email: string): Promise<void> {
    await api.post('/auth/forgot-password', { email })
  },

  async resetPassword(token: string, email: string, password: string, passwordConfirmation: string): Promise<void> {
    await api.post('/auth/reset-password', {
      token,
      email,
      password,
      password_confirmation: passwordConfirmation,
    })
  },
}

export default authService

