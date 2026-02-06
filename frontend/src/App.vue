<script setup lang="ts">
import { RouterView } from 'vue-router'
import ErrorBoundary from '@/components/common/ErrorBoundary.vue'
</script>

<template>
  <a href="#main-content" class="skip-to-content">Skip to main content</a>
  <div id="app">
    <ErrorBoundary>
      <main id="main-content">
        <RouterView />
      </main>
    </ErrorBoundary>
  </div>
</template>

<style scoped>
#app {
  min-height: 100vh;
}

.skip-to-content {
  position: absolute;
  left: -9999px;
  top: 0;
  z-index: 9999;
  padding: 0.5rem 1rem;
  background-color: var(--primary-color, #4f46e5);
  color: #fff;
  text-decoration: none;
  font-size: 0.875rem;
}

.skip-to-content:focus {
  left: 0;
}
</style>
