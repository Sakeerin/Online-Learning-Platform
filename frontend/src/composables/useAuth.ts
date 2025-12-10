import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import type { LoginCredentials, RegisterData } from '@/types/User'

export function useAuth() {
  const authStore = useAuthStore()
  const router = useRouter()

  const login = async (credentials: LoginCredentials) => {
    try {
      await authStore.login(credentials)
      // Redirect based on user role
      if (authStore.isInstructor) {
        router.push('/instructor/dashboard')
      } else {
        router.push('/student/my-learning')
      }
    } catch (error) {
      throw error
    }
  }

  const register = async (data: RegisterData) => {
    try {
      await authStore.register(data)
      // Redirect based on user role
      if (authStore.isInstructor) {
        router.push('/instructor/dashboard')
      } else {
        router.push('/student/my-learning')
      }
    } catch (error) {
      throw error
    }
  }

  const logout = async () => {
    await authStore.logout()
    router.push('/login')
  }

  const requireAuth = () => {
    if (!authStore.isAuthenticated) {
      router.push('/login')
      return false
    }
    return true
  }

  const requireInstructor = () => {
    if (!authStore.isAuthenticated || !authStore.isInstructor) {
      router.push('/login')
      return false
    }
    return true
  }

  const requireStudent = () => {
    if (!authStore.isAuthenticated || !authStore.isStudent) {
      router.push('/login')
      return false
    }
    return true
  }

  return {
    user: computed(() => authStore.user),
    isAuthenticated: computed(() => authStore.isAuthenticated),
    isInstructor: computed(() => authStore.isInstructor),
    isStudent: computed(() => authStore.isStudent),
    isLoading: computed(() => authStore.isLoading),
    error: computed(() => authStore.error),
    login,
    register,
    logout,
    requireAuth,
    requireInstructor,
    requireStudent,
  }
}

