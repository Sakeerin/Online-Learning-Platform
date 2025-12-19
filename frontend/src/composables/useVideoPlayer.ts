import { ref, onMounted, onUnmounted } from 'vue'
import { useProgressStore } from '@/stores/progress'

// T158: Debounce function for position saving
function debounce<T extends (...args: any[]) => any>(func: T, wait: number): (...args: Parameters<T>) => void {
  let timeout: ReturnType<typeof setTimeout> | null = null
  return function executedFunction(...args: Parameters<T>) {
    const later = () => {
      timeout = null
      func(...args)
    }
    if (timeout) clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

export function useVideoPlayer(enrollmentId: string, lessonId: string, lessonDuration?: number) {
  const progressStore = useProgressStore()
  const videoRef = ref<HTMLVideoElement | null>(null)
  const isPlaying = ref(false)
  const currentTime = ref(0)
  const volume = ref(1)
  const playbackRate = ref(1)
  const isFullscreen = ref(false)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  // T159: Resume from saved position
  const savedPosition = ref(0)

  // Load saved position
  onMounted(async () => {
    try {
      const progress = await progressStore.fetchProgress(enrollmentId, lessonId)
      if (progress?.video_position) {
        savedPosition.value = progress.video_position
        if (videoRef.value) {
          videoRef.value.currentTime = progress.video_position
        }
      }
    } catch (err) {
      console.error('Failed to load saved position:', err)
    }
  })

  // T158: Debounced position save (every 10 seconds)
  const savePosition = debounce(async (position: number) => {
    if (!enrollmentId || !lessonId) return

    try {
      await progressStore.updatePosition(enrollmentId, lessonId, Math.floor(position))
    } catch (err) {
      console.error('Failed to save position:', err)
    }
  }, 10000) // 10 seconds

  // T160: Auto-mark complete when video reaches 95%
  const checkCompletion = () => {
    if (!videoRef.value || !lessonDuration) return

    const progress = (currentTime.value / lessonDuration) * 100
    if (progress >= 95 && !progressStore.getProgress(enrollmentId, lessonId)?.is_completed) {
      progressStore.markComplete(enrollmentId, lessonId)
    }
  }

  const handleTimeUpdate = () => {
    if (!videoRef.value) return
    currentTime.value = videoRef.value.currentTime
    savePosition(videoRef.value.currentTime)
    checkCompletion()
  }

  const togglePlay = () => {
    if (!videoRef.value) return
    if (isPlaying.value) {
      videoRef.value.pause()
    } else {
      videoRef.value.play()
    }
    isPlaying.value = !isPlaying.value
  }

  const setVolume = (value: number) => {
    if (!videoRef.value) return
    volume.value = Math.max(0, Math.min(1, value))
    videoRef.value.volume = volume.value
  }

  const setPlaybackRate = (rate: number) => {
    if (!videoRef.value) return
    playbackRate.value = rate
    videoRef.value.playbackRate = rate
  }

  const seek = (time: number) => {
    if (!videoRef.value) return
    videoRef.value.currentTime = time
    currentTime.value = time
  }

  const toggleFullscreen = () => {
    if (!videoRef.value) return

    if (!isFullscreen.value) {
      if (videoRef.value.requestFullscreen) {
        videoRef.value.requestFullscreen()
      }
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen()
      }
    }
    isFullscreen.value = !isFullscreen.value
  }

  // T157: Keyboard shortcuts
  const handleKeyPress = (event: KeyboardEvent) => {
    if (event.code === 'Space') {
      event.preventDefault()
      togglePlay()
    }
  }

  onMounted(() => {
    document.addEventListener('keydown', handleKeyPress)
  })

  onUnmounted(() => {
    document.removeEventListener('keydown', handleKeyPress)
  })

  return {
    videoRef,
    isPlaying,
    currentTime,
    volume,
    playbackRate,
    isFullscreen,
    isLoading,
    error,
    savedPosition,
    togglePlay,
    setVolume,
    setPlaybackRate,
    seek,
    toggleFullscreen,
    handleTimeUpdate,
  }
}

