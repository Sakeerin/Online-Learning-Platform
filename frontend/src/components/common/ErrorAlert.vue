<script setup lang="ts">
defineProps<{
  message: string
  retryable?: boolean
}>()

const emit = defineEmits<{
  retry: []
}>()
</script>

<template>
  <div class="error-alert" role="alert">
    <p class="error-alert__message">{{ message }}</p>
    <button
      v-if="retryable"
      class="error-alert__retry"
      @click="emit('retry')"
    >
      Try Again
    </button>
  </div>
</template>

<style scoped>
.error-alert {
  padding: 1rem;
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.error-alert__message {
  color: #991b1b;
  font-size: 0.875rem;
  margin: 0;
}

.error-alert__retry {
  padding: 0.375rem 0.75rem;
  background-color: #991b1b;
  color: #fff;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
  font-size: 0.75rem;
  white-space: nowrap;
}

.error-alert__retry:hover {
  background-color: #7f1d1d;
}
</style>
