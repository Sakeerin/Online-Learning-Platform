<script setup lang="ts">
import { ref, onErrorCaptured } from 'vue'

defineProps<{
  fallbackMessage?: string
}>()

const hasError = ref(false)
const errorMessage = ref('')

onErrorCaptured((err: Error) => {
  hasError.value = true
  errorMessage.value = err.message || 'An unexpected error occurred'
  console.error('ErrorBoundary caught:', err)
  return false
})

function retry() {
  hasError.value = false
  errorMessage.value = ''
}
</script>

<template>
  <div v-if="hasError" class="error-boundary">
    <div class="error-boundary__content">
      <h2 class="error-boundary__title">Something went wrong</h2>
      <p class="error-boundary__message">{{ fallbackMessage || errorMessage }}</p>
      <button class="error-boundary__button" @click="retry">Try Again</button>
    </div>
  </div>
  <slot v-else />
</template>

<style scoped>
.error-boundary {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 200px;
  padding: 2rem;
}

.error-boundary__content {
  text-align: center;
  max-width: 480px;
}

.error-boundary__title {
  font-size: 1.5rem;
  color: var(--text-color, #1a1a1a);
  margin-bottom: 0.5rem;
}

.error-boundary__message {
  color: var(--text-light, #555);
  margin-bottom: 1.5rem;
}

.error-boundary__button {
  padding: 0.5rem 1.5rem;
  background-color: var(--primary-color, #4f46e5);
  color: #fff;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
  font-size: 0.875rem;
}

.error-boundary__button:hover {
  opacity: 0.9;
}
</style>
