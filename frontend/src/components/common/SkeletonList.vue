<script setup lang="ts">
import SkeletonCourseCard from './SkeletonCourseCard.vue'

defineProps<{
  count?: number
}>()
</script>

<template>
  <div class="skeleton-list" aria-label="Loading content" role="status">
    <SkeletonCourseCard v-for="i in (count || 6)" :key="i" />
    <span class="sr-only">Loading...</span>
  </div>
</template>

<style scoped>
.skeleton-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
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
