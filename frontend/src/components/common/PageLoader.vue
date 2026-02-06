<script setup lang="ts">
</script>

<template>
  <div class="page-loader" role="status" aria-label="Loading page">
    <div class="page-loader__spinner"></div>
    <span class="sr-only">Loading...</span>
  </div>
</template>

<style scoped>
.page-loader {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 50vh;
}

.page-loader__spinner {
  width: 40px;
  height: 40px;
  border: 3px solid var(--border-color, #e5e7eb);
  border-top-color: var(--primary-color, #4f46e5);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border-width: 0;
}
</style>
