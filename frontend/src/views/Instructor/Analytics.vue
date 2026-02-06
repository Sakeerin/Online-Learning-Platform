<script setup lang="ts">
import AnalyticsDashboard from '@/components/instructor/AnalyticsDashboard.vue'
</script>

<template>
  <div class="analytics-page">
    <div class="container">
      <AnalyticsDashboard />
    </div>
  </div>
</template>

<style scoped>
.analytics-page {
  min-height: 100vh;
  padding: 2rem 0;
  background: var(--bg-color, #f9fafb);
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1.5rem;
}
</style>

