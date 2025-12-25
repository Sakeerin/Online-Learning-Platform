import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'home',
    component: () => import('../views/Home.vue')
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/Auth/Login.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('../views/Auth/Register.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: () => import('../views/Auth/ForgotPassword.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/instructor',
    meta: { requiresAuth: true, requiresInstructor: true },
    children: [
      {
        path: 'dashboard',
        name: 'instructor-dashboard',
        component: () => import('../views/Instructor/Dashboard.vue')
      },
      {
        path: 'courses/create',
        name: 'create-course',
        component: () => import('../views/Instructor/CreateCourse.vue')
      },
      {
        path: 'courses/:id/edit',
        name: 'edit-course',
        component: () => import('../views/Instructor/EditCourse.vue')
      }
    ]
  },
  {
    path: '/student',
    meta: { requiresAuth: true, requiresStudent: true },
    children: [
      {
        path: 'my-learning',
        name: 'my-learning',
        component: () => import('../views/Student/MyLearning.vue')
      },
      {
        path: 'courses/:courseId/learn',
        name: 'course-player',
        component: () => import('../views/Student/CoursePlayer.vue')
      },
      {
        path: 'certificates',
        name: 'certificates',
        component: () => import('../views/Student/Certificates.vue')
      }
    ]
  },
  {
    path: '/courses',
    children: [
      {
        path: 'browse',
        name: 'browse-courses',
        component: () => import('../views/Courses/Browse.vue')
      },
      {
        path: 'search',
        name: 'search-courses',
        component: () => import('../views/Courses/Search.vue')
      },
      {
        path: ':id',
        name: 'course-detail',
        component: () => import('../views/Courses/Detail.vue')
      }
    ]
  },
  {
    path: '/certificates/verify',
    name: 'certificate-verification',
    component: () => import('../views/CertificateVerification.vue')
  },
  {
    path: '/payment',
    meta: { requiresAuth: true, requiresStudent: true },
    children: [
      {
        path: 'checkout',
        name: 'checkout',
        component: () => import('../views/Payment/Checkout.vue')
      },
      {
        path: 'success',
        name: 'payment-success',
        component: () => import('../views/Payment/Success.vue')
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// Navigation guards for authentication
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  // Initialize auth from localStorage on first load
  if (!authStore.user && authStore.token) {
    authStore.initAuth()
  }

  // Check if route requires authentication
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login', query: { redirect: to.fullPath } })
    return
  }

  // Check if route requires guest (redirect if already logged in)
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    // Redirect based on role
    if (authStore.isInstructor) {
      next({ name: 'instructor-dashboard' })
    } else {
      next({ name: 'my-learning' })
    }
    return
  }

  // Check if route requires instructor role
  if (to.meta.requiresInstructor && !authStore.isInstructor) {
    next({ name: 'home' })
    return
  }

  // Check if route requires student role
  if (to.meta.requiresStudent && !authStore.isStudent) {
    next({ name: 'home' })
    return
  }

  next()
})

export default router

