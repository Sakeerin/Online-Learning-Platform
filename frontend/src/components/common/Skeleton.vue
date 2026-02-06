<script setup lang="ts">
defineProps<{
  width?: string
  height?: string
  borderRadius?: string
  variant?: 'text' | 'circular' | 'rectangular'
}>()
</script>

<template>
  <div
    class="skeleton"
    :class="`skeleton--${variant || 'rectangular'}`"
    :style="{
      width: width || '100%',
      height: height || (variant === 'text' ? '1rem' : '100%'),
      borderRadius: borderRadius || (variant === 'circular' ? '50%' : '0.25rem'),
    }"
    aria-hidden="true"
  />
</template>

<style scoped>
.skeleton {
  background: linear-gradient(90deg, #e5e7eb 25%, #f3f4f6 50%, #e5e7eb 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

.skeleton--text {
  display: inline-block;
}

.skeleton--circular {
  flex-shrink: 0;
}

@keyframes shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}
</style>
