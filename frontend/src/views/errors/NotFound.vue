<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()
</script>

<template>
  <div class="not-found">
    <h1 class="not-found__code">404</h1>
    <h2 class="not-found__title">Page Not Found</h2>
    <p class="not-found__message">The page you're looking for doesn't exist or has been moved.</p>
    <div class="not-found__actions">
      <button class="not-found__button" @click="router.push('/')">Go Home</button>
      <button class="not-found__button not-found__button--secondary" @click="router.back()">Go Back</button>
    </div>
  </div>
</template>

<style scoped>
.not-found {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 60vh;
  text-align: center;
  padding: 2rem;
}

.not-found__code {
  font-size: 6rem;
  font-weight: 700;
  color: var(--primary-color, #4f46e5);
  line-height: 1;
  margin-bottom: 0.5rem;
}

.not-found__title {
  font-size: 1.5rem;
  color: var(--text-color, #1a1a1a);
  margin-bottom: 0.5rem;
}

.not-found__message {
  color: var(--text-light, #555);
  margin-bottom: 2rem;
}

.not-found__actions {
  display: flex;
  gap: 1rem;
}

.not-found__button {
  padding: 0.625rem 1.5rem;
  background-color: var(--primary-color, #4f46e5);
  color: #fff;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
  font-size: 0.875rem;
}

.not-found__button:hover {
  opacity: 0.9;
}

.not-found__button--secondary {
  background-color: transparent;
  color: var(--text-color, #1a1a1a);
  border: 1px solid var(--border-color, #e5e7eb);
}

.not-found__button--secondary:hover {
  background-color: var(--bg-secondary, #f9fafb);
}
</style>
