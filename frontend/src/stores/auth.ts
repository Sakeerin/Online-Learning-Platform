import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User, LoginCredentials, RegisterData, AuthResponse } from '@/types/User'
import authService from '@/services/authService'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const isAuthenticated = computed(() => !!user.value && !!token.value)
  const isInstructor = computed(() => user.value?.role === 'instructor')
  const isStudent = computed(() => user.value?.role === 'student')
  const isAdmin = computed(() => user.value?.role === 'admin')

  // Initialize auth state from localStorage
  function initAuth() {
    const storedToken = localStorage.getItem('auth_token')
    const storedUser = localStorage.getItem('auth_user')
    
    if (storedToken && storedUser) {
      token.value = storedToken
      user.value = JSON.parse(storedUser)
    }
  }

  async function login(credentials: LoginCredentials) {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await authService.login(credentials)
      token.value = response.token
      user.value = response.user
      
      // Store in localStorage
      localStorage.setItem('auth_token', response.token)
      localStorage.setItem('auth_user', JSON.stringify(response.user))
      
      return response
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Login failed. Please try again.'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function register(data: RegisterData) {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await authService.register(data)
      token.value = response.token
      user.value = response.user
      
      // Store in localStorage
      localStorage.setItem('auth_token', response.token)
      localStorage.setItem('auth_user', JSON.stringify(response.user))
      
      return response
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Registration failed. Please try again.'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function logout() {
    isLoading.value = true
    
    try {
      await authService.logout()
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      // Clear state regardless of API call success
      user.value = null
      token.value = null
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
      isLoading.value = false
    }
  }

  async function fetchUser() {
    if (!token.value) return
    
    try {
      const userData = await authService.getUser()
      user.value = userData
      localStorage.setItem('auth_user', JSON.stringify(userData))
    } catch (err) {
      // Token might be invalid, clear auth
      logout()
    }
  }

  return {
    user,
    token,
    isLoading,
    error,
    isAuthenticated,
    isInstructor,
    isStudent,
    isAdmin,
    initAuth,
    login,
    register,
    logout,
    fetchUser,
  }
})

