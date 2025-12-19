<script setup lang="ts">
import { computed } from 'vue'
import type { Enrollment } from '@/types/Enrollment'
import ProgressBar from '@/components/student/ProgressBar.vue'

interface Props {
  enrollment: Enrollment
}

const props = defineProps<Props>()

const progressPercentage = computed(() => props.enrollment.progress_percentage)
</script>

<template>
  <div class="course-progress">
    <div class="progress-header">
      <h3>Your Progress</h3>
      <span class="progress-percentage">{{ Math.round(progressPercentage) }}%</span>
    </div>
    <ProgressBar :progress="progressPercentage" :show-label="false" />
    <div class="progress-stats">
      <span v-if="enrollment.is_completed" class="completed-badge">✓ Completed</span>
      <span v-else class="in-progress">In Progress</span>
    </div>
  </div>
</template>

<style scoped>
.course-progress {
  padding: 1.5rem;
  background: #f9fafb;
  border-radius: 8px;
}

.progress-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.progress-header h3 {
  margin: 0;
  font-size: 1.125rem;
  font-weight: 600;
}

.progress-percentage {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--primary-color);
}

.progress-stats {
  margin-top: 0.75rem;
}

.completed-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  background: #d1fae5;
  color: #059669;
  border-radius: 4px;
  font-size: 0.875rem;
  font-weight: 600;
}

.in-progress {
  color: #6b7280;
  font-size: 0.875rem;
}
</style>

