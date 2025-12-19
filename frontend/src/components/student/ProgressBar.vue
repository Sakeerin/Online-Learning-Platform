<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  progress: number
  showLabel?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  showLabel: true,
})

const progressPercentage = computed(() => {
  return Math.min(100, Math.max(0, props.progress))
})
</script>

<template>
  <div class="progress-bar">
    <div v-if="showLabel" class="progress-label">
      <span>{{ Math.round(progressPercentage) }}% Complete</span>
    </div>
    <div class="progress-track">
      <div class="progress-fill" :style="{ width: `${progressPercentage}%` }"></div>
    </div>
  </div>
</template>

<style scoped>
.progress-bar {
  width: 100%;
}

.progress-label {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.progress-track {
  width: 100%;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: var(--primary-color);
  border-radius: 4px;
  transition: width 0.3s ease;
}
</style>

