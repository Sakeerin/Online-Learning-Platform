<script setup lang="ts">
import { ref } from 'vue'
import authService from '@/services/authService'
import Button from '@/components/common/Button.vue'
import Input from '@/components/common/Input.vue'

const email = ref('')
const isLoading = ref(false)
const error = ref<string | null>(null)
const success = ref(false)

const handleSubmit = async () => {
  if (!email.value) return
  
  isLoading.value = true
  error.value = null
  
  try {
    await authService.forgotPassword(email.value)
    success.value = true
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to send reset link. Please try again.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="forgot-password-page">
    <div class="forgot-password-container">
      <h1>Reset Password</h1>
      <p class="subtitle">Enter your email to receive a password reset link</p>

      <form v-if="!success" @submit.prevent="handleSubmit" class="forgot-password-form">
        <div v-if="error" class="error-message" role="alert">
          {{ error }}
        </div>

        <Input
          v-model="email"
          type="email"
          label="Email"
          placeholder="your@email.com"
          required
          autocomplete="email"
        />

        <Button
          type="submit"
          :disabled="isLoading || !email"
          class="submit-button"
        >
          {{ isLoading ? 'Sending...' : 'Send Reset Link' }}
        </Button>

        <p class="login-link">
          Remember your password?
          <router-link to="/login">Sign in</router-link>
        </p>
      </form>

      <div v-else class="success-message">
        <p>✅ Password reset link sent!</p>
        <p>Check your email for instructions.</p>
        <router-link to="/login">
          <Button>Back to Login</Button>
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.forgot-password-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem;
}

.forgot-password-container {
  background: white;
  border-radius: 12px;
  padding: 3rem;
  width: 100%;
  max-width: 450px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

h1 {
  font-size: 2rem;
  margin-bottom: 0.5rem;
  color: #333;
}

.subtitle {
  color: #666;
  margin-bottom: 2rem;
}

.forgot-password-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.error-message {
  background: #fee;
  color: #c33;
  padding: 0.75rem;
  border-radius: 6px;
  border: 1px solid #fcc;
}

.success-message {
  text-align: center;
  padding: 2rem 0;
}

.success-message p {
  margin-bottom: 1rem;
  color: #333;
}

.submit-button {
  width: 100%;
  padding: 0.875rem;
  font-size: 1rem;
  margin-top: 0.5rem;
}

.login-link {
  text-align: center;
  color: #666;
  margin-top: 1.5rem;
}

.login-link a {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.login-link a:hover {
  text-decoration: underline;
}
</style>

