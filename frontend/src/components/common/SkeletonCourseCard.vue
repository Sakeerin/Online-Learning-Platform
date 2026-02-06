<script setup lang="ts">
import Skeleton from './Skeleton.vue'
</script>

<template>
  <div class="skeleton-card" aria-hidden="true">
    <Skeleton height="180px" border-radius="0.5rem 0.5rem 0 0" />
    <div class="skeleton-card__body">
      <Skeleton variant="text" height="1.25rem" width="90%" />
      <Skeleton variant="text" height="0.875rem" width="60%" />
      <div class="skeleton-card__meta">
        <Skeleton variant="text" height="0.75rem" width="40%" />
        <Skeleton variant="text" height="0.75rem" width="25%" />
      </div>
      <Skeleton variant="text" height="1.5rem" width="30%" />
    </div>
  </div>
</template>

<style scoped>
.skeleton-card {
  background: #fff;
  border-radius: 0.5rem;
  overflow: hidden;
  border: 1px solid var(--border-color, #e5e7eb);
}

.skeleton-card__body {
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.skeleton-card__meta {
  display: flex;
  justify-content: space-between;
  margin-top: 0.25rem;
}
</style>
