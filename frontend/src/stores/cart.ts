import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Course } from '@/types/Course'

export const useCartStore = defineStore('cart', () => {
  const items = ref<Course[]>([])

  const total = computed(() => {
    return items.value.reduce((sum, item) => sum + (item.price || 0), 0)
  })

  const itemCount = computed(() => {
    return items.value.length
  })

  const addItem = (course: Course) => {
    // Check if course already in cart
    if (!items.value.find(item => item.id === course.id)) {
      items.value.push(course)
    }
  }

  const removeItem = (courseId: string) => {
    items.value = items.value.filter(item => item.id !== courseId)
  }

  const clear = () => {
    items.value = []
  }

  const hasItem = (courseId: string) => {
    return items.value.some(item => item.id === courseId)
  }

  return {
    items,
    total,
    itemCount,
    addItem,
    removeItem,
    clear,
    hasItem,
  }
})

