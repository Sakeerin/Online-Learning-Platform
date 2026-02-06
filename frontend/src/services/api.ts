import axios, { type AxiosInstance, type AxiosError, type InternalAxiosRequestConfig } from 'axios'
import type { ApiErrorResponse } from '@/types/Api'

// Create axios instance with base configuration
const api: AxiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  withCredentials: true
})

// Request interceptor for adding auth token
api.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    const token = localStorage.getItem('auth_token')
    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error: AxiosError) => {
    return Promise.reject(error)
  }
)

// Lazy-load toast to avoid circular dependency issues
let toastInstance: ReturnType<typeof import('vue-toastification')['useToast']> | null = null
async function getToast() {
  if (!toastInstance) {
    const { useToast } = await import('vue-toastification')
    toastInstance = useToast()
  }
  return toastInstance
}

// Response interceptor for handling errors
api.interceptors.response.use(
  (response) => response,
  async (error: AxiosError<ApiErrorResponse>) => {
    const status = error.response?.status
    const message = error.response?.data?.message || 'An unexpected error occurred'

    if (status === 401) {
      // Handle unauthorized - redirect to login
      localStorage.removeItem('auth_token')
      window.location.href = '/login'
    } else if (status === 403) {
      const toast = await getToast()
      toast.error(message || 'You do not have permission to perform this action')
    } else if (status === 429) {
      const toast = await getToast()
      toast.warning('Too many requests. Please wait a moment.')
    } else if (status && status >= 500) {
      const toast = await getToast()
      toast.error('A server error occurred. Please try again later.')
    }

    return Promise.reject(error)
  }
)

export default api
