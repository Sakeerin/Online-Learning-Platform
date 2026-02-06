import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/auth'
import Toast from '@/plugins/toast'
import { toastOptions } from '@/plugins/toast'
import './assets/styles/main.css'

const app = createApp(App)

const pinia = createPinia()
app.use(pinia)
app.use(router)
app.use(Toast, toastOptions)

// Global error handler
app.config.errorHandler = (err, _instance, info) => {
  console.error('Global error:', err, info)
}

// Initialize auth state from localStorage
const authStore = useAuthStore()
authStore.initAuth()

app.mount('#app')
