<script setup lang="ts">
import { ref } from 'vue'
import { useAuth } from '@/composables/useAuth'
import Button from '@/components/common/Button.vue'
import Input from '@/components/common/Input.vue'

const { register, isLoading, error } = useAuth()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'student' as 'student' | 'instructor',
})

const handleSubmit = async () => {
  try {
    await register({
      name: form.value.name,
      email: form.value.email,
      password: form.value.password,
      password_confirmation: form.value.password_confirmation,
      role: form.value.role,
    })
  } catch (err) {
    // Error handled by store
  }
}
</script>

<template>
  <div class="register-page">
    <div class="register-container">
      <h1>Create Account</h1>
      <p class="subtitle">Join our learning community</p>

      <form @submit.prevent="handleSubmit" class="register-form">
        <div v-if="error" class="error-message" role="alert">
          {{ error }}
        </div>

        <Input
          v-model="form.name"
          type="text"
          label="Full Name"
          placeholder="John Doe"
          required
          autocomplete="name"
        />

        <Input
          v-model="form.email"
          type="email"
          label="Email"
          placeholder="your@email.com"
          required
          autocomplete="email"
        />

        <div class="role-selection">
          <label class="role-label">I want to:</label>
          <div class="role-options">
            <label class="role-option">
              <input
                v-model="form.role"
                type="radio"
                value="student"
                required
              />
              <span>Learn courses</span>
            </label>
            <label class="role-option">
              <input
                v-model="form.role"
                type="radio"
                value="instructor"
                required
              />
              <span>Teach courses</span>
            </label>
          </div>
        </div>

        <Input
          v-model="form.password"
          type="password"
          label="Password"
          placeholder="At least 8 characters"
          required
          autocomplete="new-password"
        />

        <Input
          v-model="form.password_confirmation"
          type="password"
          label="Confirm Password"
          placeholder="Re-enter your password"
          required
          autocomplete="new-password"
        />

        <Button
          type="submit"
          :disabled="isLoading"
          class="submit-button"
        >
          {{ isLoading ? 'Creating account...' : 'Create Account' }}
        </Button>

        <p class="login-link">
          Already have an account?
          <router-link to="/login">Sign in</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<style scoped>
.register-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem;
}

.register-container {
  background: white;
  border-radius: 12px;
  padding: 3rem;
  width: 100%;
  max-width: 500px;
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

.register-form {
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

.role-selection {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.role-label {
  font-weight: 500;
  color: #333;
}

.role-options {
  display: flex;
  gap: 1rem;
}

.role-option {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  padding: 0.75rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  flex: 1;
  transition: all 0.2s;
}

.role-option:hover {
  border-color: #667eea;
  background: #f8f9ff;
}

.role-option input[type="radio"] {
  margin: 0;
}

.role-option input[type="radio"]:checked + span {
  font-weight: 600;
  color: #667eea;
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

