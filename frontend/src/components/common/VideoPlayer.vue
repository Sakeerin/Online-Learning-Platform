<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import Button from './Button.vue'

interface Props {
  src: string
  duration?: number
  currentPosition?: number
  autoplay?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  autoplay: false,
})

const emit = defineEmits<{
  'timeupdate': [time: number]
  'play': []
  'pause': []
  'ended': []
}>()

const videoRef = ref<HTMLVideoElement | null>(null)
const isPlaying = ref(false)
const currentTime = ref(0)
const volume = ref(1)
const playbackRate = ref(1)
const isFullscreen = ref(false)
const isMuted = ref(false)
const showControls = ref(true)
const controlsTimeout = ref<ReturnType<typeof setTimeout> | null>(null)

const duration = computed(() => props.duration || (videoRef.value?.duration || 0))
const progress = computed(() => {
  if (!duration.value) return 0
  return (currentTime.value / duration.value) * 100
})

const formattedTime = (seconds: number): string => {
  const h = Math.floor(seconds / 3600)
  const m = Math.floor((seconds % 3600) / 60)
  const s = Math.floor(seconds % 60)
  if (h > 0) {
    return `${h}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`
  }
  return `${m}:${s.toString().padStart(2, '0')}`
}

// T156: Play/pause controls
const togglePlay = () => {
  if (!videoRef.value) return
  if (isPlaying.value) {
    videoRef.value.pause()
  } else {
    videoRef.value.play()
  }
}

// T156: Volume controls
const toggleMute = () => {
  if (!videoRef.value) return
  isMuted.value = !isMuted.value
  videoRef.value.muted = isMuted.value
}

const setVolume = (value: number) => {
  if (!videoRef.value) return
  volume.value = Math.max(0, Math.min(1, value))
  videoRef.value.volume = volume.value
  if (volume.value > 0) {
    isMuted.value = false
    videoRef.value.muted = false
  }
}

// T156: Playback speed selector
const setPlaybackRate = (rate: number) => {
  if (!videoRef.value) return
  playbackRate.value = rate
  videoRef.value.playbackRate = rate
}

const seek = (e: MouseEvent) => {
  if (!videoRef.value || !duration.value) return
  const rect = (e.currentTarget as HTMLElement).getBoundingClientRect()
  const x = e.clientX - rect.left
  const percent = x / rect.width
  const newTime = percent * duration.value
  videoRef.value.currentTime = newTime
  currentTime.value = newTime
}

// T157: Fullscreen mode
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
}

// T157: Keyboard shortcuts (Space for play/pause)
const handleKeyPress = (event: KeyboardEvent) => {
  if (event.code === 'Space' && document.activeElement?.tagName !== 'INPUT') {
    event.preventDefault()
    togglePlay()
  }
}

const handleTimeUpdate = () => {
  if (!videoRef.value) return
  currentTime.value = videoRef.value.currentTime
  emit('timeupdate', videoRef.value.currentTime)
}

const handlePlay = () => {
  isPlaying.value = true
  emit('play')
  hideControls()
}

const handlePause = () => {
  isPlaying.value = false
  emit('pause')
  showControls()
}

const handleEnded = () => {
  isPlaying.value = false
  emit('ended')
}

const showControls = () => {
  showControls.value = true
  if (controlsTimeout.value) {
    clearTimeout(controlsTimeout.value)
  }
  if (isPlaying.value) {
    controlsTimeout.value = setTimeout(() => {
      showControls.value = false
    }, 3000)
  }
}

const hideControls = () => {
  if (controlsTimeout.value) {
    clearTimeout(controlsTimeout.value)
  }
  if (isPlaying.value) {
    controlsTimeout.value = setTimeout(() => {
      showControls.value = false
    }, 3000)
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleKeyPress)
  document.addEventListener('fullscreenchange', () => {
    isFullscreen.value = !!document.fullscreenElement
  })

  if (props.currentPosition && videoRef.value) {
    videoRef.value.currentTime = props.currentPosition
  }
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeyPress)
  if (controlsTimeout.value) {
    clearTimeout(controlsTimeout.value)
  }
})

watch(() => props.currentPosition, (newPos) => {
  if (newPos && videoRef.value) {
    videoRef.value.currentTime = newPos
  }
})
</script>

<template>
  <div class="video-player" @mousemove="showControls" @mouseleave="hideControls">
    <video
      ref="videoRef"
      :src="src"
      class="video-element"
      @timeupdate="handleTimeUpdate"
      @play="handlePlay"
      @pause="handlePause"
      @ended="handleEnded"
      @click="togglePlay"
    />

    <div v-if="showControls" class="video-controls">
      <!-- Progress bar -->
      <div class="progress-bar-container" @click="seek">
        <div class="progress-bar" :style="{ width: `${progress}%` }"></div>
      </div>

      <!-- Controls -->
      <div class="controls-row">
        <div class="controls-left">
          <Button variant="outline" size="sm" @click="togglePlay">
            {{ isPlaying ? '⏸' : '▶' }}
          </Button>
          <div class="volume-control">
            <Button variant="outline" size="sm" @click="toggleMute">
              {{ isMuted ? '🔇' : '🔊' }}
            </Button>
            <input
              type="range"
              min="0"
              max="1"
              step="0.1"
              :value="volume"
              @input="setVolume(parseFloat(($event.target as HTMLInputElement).value))"
              class="volume-slider"
            />
          </div>
          <span class="time-display">
            {{ formattedTime(currentTime) }} / {{ formattedTime(duration) }}
          </span>
        </div>

        <div class="controls-right">
          <select :value="playbackRate" @change="setPlaybackRate(parseFloat(($event.target as HTMLSelectElement).value))" class="speed-select">
            <option :value="0.5">0.5x</option>
            <option :value="0.75">0.75x</option>
            <option :value="1">1x</option>
            <option :value="1.25">1.25x</option>
            <option :value="1.5">1.5x</option>
            <option :value="2">2x</option>
          </select>
          <Button variant="outline" size="sm" @click="toggleFullscreen">
            {{ isFullscreen ? '⤓' : '⤢' }}
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.video-player {
  position: relative;
  width: 100%;
  background: #000;
  border-radius: 8px;
  overflow: hidden;
}

.video-element {
  width: 100%;
  display: block;
}

.video-controls {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
  padding: 1rem;
  color: white;
}

.progress-bar-container {
  width: 100%;
  height: 6px;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 3px;
  cursor: pointer;
  margin-bottom: 1rem;
}

.progress-bar {
  height: 100%;
  background: var(--primary-color);
  border-radius: 3px;
  transition: width 0.1s;
}

.controls-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
}

.controls-left,
.controls-right {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.volume-control {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.volume-slider {
  width: 80px;
}

.time-display {
  font-size: 0.875rem;
  font-weight: 500;
}

.speed-select {
  padding: 0.5rem;
  border-radius: 4px;
  background: rgba(255, 255, 255, 0.1);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.2);
  cursor: pointer;
}
</style>

