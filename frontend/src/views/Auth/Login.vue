<script setup lang="ts">
import { ref } from 'vue'
import { useAuth } from '@/composables/useAuth'
import Button from '@/components/common/Button.vue'
import Input from '@/components/common/Input.vue'

const { login, isLoading, error } = useAuth()

const form = ref({
  email: '',
  password: '',
})

const handleSubmit = async () => {
  try {
    await login({
      email: form.value.email,
      password: form.value.password,
    })
  } catch (err) {
    // Error handled by store
  }
}
</script>

<template>
  <div class="login-page">
    <div class="login-container">
      <h1>Welcome Back</h1>
      <p class="subtitle">Sign in to continue learning</p>

      <form @submit.prevent="handleSubmit" class="login-form">
        <div v-if="error" class="error-message" role="alert">
          {{ error }}
        </div>

        <Input
          v-model="form.email"
          type="email"
          label="Email"
          placeholder="your@email.com"
          required
          autocomplete="email"
        />

        <Input
          v-model="form.password"
          type="password"
          label="Password"
          placeholder="Enter your password"
          required
          autocomplete="current-password"
        />

        <div class="form-footer">
          <router-link to="/forgot-password" class="forgot-link">
            Forgot password?
          </router-link>
        </div>

        <Button
          type="submit"
          :disabled="isLoading"
          class="submit-button"
        >
          {{ isLoading ? 'Signing in...' : 'Sign In' }}
        </Button>

        <p class="signup-link">
          Don't have an account?
          <router-link to="/register">Sign up</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem;
}

.login-container {
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

.login-form {
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

.form-footer {
  display: flex;
  justify-content: flex-end;
}

.forgot-link {
  color: #667eea;
  text-decoration: none;
  font-size: 0.9rem;
}

.forgot-link:hover {
  text-decoration: underline;
}

.submit-button {
  width: 100%;
  padding: 0.875rem;
  font-size: 1rem;
  margin-top: 0.5rem;
}

.signup-link {
  text-align: center;
  color: #666;
  margin-top: 1.5rem;
}

.signup-link a {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.signup-link a:hover {
  text-decoration: underline;
}
</style>

