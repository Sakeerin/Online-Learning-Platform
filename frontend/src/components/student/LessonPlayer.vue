<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useVideoPlayer } from '@/composables/useVideoPlayer'
import VideoPlayer from '@/components/common/VideoPlayer.vue'
import Card from '@/components/common/Card.vue'
import Button from '@/components/common/Button.vue'
import type { Lesson, Section } from '@/types/Course'
import enrollmentService from '@/services/enrollmentService'

interface Props {
  enrollmentId: string
  courseId: string
  lesson: Lesson
  section: Section
  nextLesson?: Lesson
  previousLesson?: Lesson
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'lesson-complete': []
  'next-lesson': []
}>()

const videoUrl = ref<string | null>(null)
const isLoading = ref(false)
const error = ref<string | null>(null)
const currentPosition = ref(0)

const { handleTimeUpdate } = useVideoPlayer(props.enrollmentId, props.lesson.id, props.lesson.duration)

onMounted(async () => {
  await loadVideo()
})

const loadVideo = async () => {
  isLoading.value = true
  error.value = null

  try {
    const response = await enrollmentService.getVideoUrl(props.courseId, props.lesson.id)
    videoUrl.value = response.video_url
    
    // Load saved position
    const progress = await enrollmentService.getProgress(props.enrollmentId, props.lesson.id)
    if (progress?.video_position) {
      currentPosition.value = progress.video_position
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load video'
  } finally {
    isLoading.value = false
  }
}

const onTimeUpdate = (time: number) => {
  currentPosition.value = time
  handleTimeUpdate()
}

const onVideoEnded = () => {
  // T161: Auto-mark complete and trigger next lesson
  emit('lesson-complete')
}

const handleNext = () => {
  emit('next-lesson')
}
</script>

<template>
  <div class="lesson-player">
    <Card>
      <div class="lesson-header">
        <div>
          <h2>{{ lesson.title }}</h2>
          <p class="section-name">{{ section.title }}</p>
        </div>
      </div>

      <div v-if="isLoading" class="loading">Loading video...</div>
      <div v-else-if="error" class="error">{{ error }}</div>
      <div v-else-if="videoUrl" class="video-container">
        <VideoPlayer
          :src="videoUrl"
          :duration="lesson.duration"
          :current-position="currentPosition"
          @timeupdate="onTimeUpdate"
          @ended="onVideoEnded"
        />
      </div>

      <div v-if="nextLesson" class="lesson-actions">
        <Button v-if="nextLesson" @click="handleNext">Next Lesson: {{ nextLesson.title }}</Button>
      </div>
    </Card>
  </div>
</template>

<style scoped>
.lesson-player {
  width: 100%;
}

.lesson-header {
  margin-bottom: 1.5rem;
}

.lesson-header h2 {
  margin: 0 0 0.5rem 0;
  font-size: 1.5rem;
}

.section-name {
  color: #6b7280;
  margin: 0;
}

.loading,
.error {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}

.error {
  color: var(--error);
}

.video-container {
  margin-bottom: 1.5rem;
}

.lesson-actions {
  margin-top: 1.5rem;
  display: flex;
  justify-content: flex-end;
}
</style>

