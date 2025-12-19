import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Progress } from '@/types/Enrollment'
import enrollmentService from '@/services/enrollmentService'

export const useProgressStore = defineStore('progress', () => {
  const progress = ref<Map<string, Progress>>(new Map())

  // Actions
  async function fetchProgress(enrollmentId: string, lessonId: string): Promise<Progress | null> {
    const progressData = await enrollmentService.getProgress(enrollmentId, lessonId)
    if (progressData) {
      progress.value.set(`${enrollmentId}-${lessonId}`, progressData)
    }
    return progressData
  }

  async function fetchAllProgress(enrollmentId: string): Promise<Progress[]> {
    const progressList = await enrollmentService.getAllProgress(enrollmentId)
    progressList.forEach((p) => {
      progress.value.set(`${enrollmentId}-${p.lesson_id}`, p)
    })
    return progressList
  }

  async function updatePosition(enrollmentId: string, lessonId: string, position: number): Promise<Progress> {
    const progressData = await enrollmentService.updatePosition(enrollmentId, lessonId, { position })
    progress.value.set(`${enrollmentId}-${lessonId}`, progressData)
    return progressData
  }

  async function markComplete(enrollmentId: string, lessonId: string): Promise<Progress> {
    const progressData = await enrollmentService.markComplete(enrollmentId, lessonId)
    progress.value.set(`${enrollmentId}-${lessonId}`, progressData)
    return progressData
  }

  function getProgress(enrollmentId: string, lessonId: string): Progress | undefined {
    return progress.value.get(`${enrollmentId}-${lessonId}`)
  }

  function clearProgress() {
    progress.value.clear()
  }

  return {
    progress,
    fetchProgress,
    fetchAllProgress,
    updatePosition,
    markComplete,
    getProgress,
    clearProgress,
  }
})

