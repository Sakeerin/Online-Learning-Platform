<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useEnrollment } from '@/composables/useEnrollment'
import enrollmentService from '@/services/enrollmentService'
import certificateService from '@/services/certificateService'
import LessonPlayer from '@/components/student/LessonPlayer.vue'
import CourseProgress from '@/components/course/CourseProgress.vue'
import Card from '@/components/common/Card.vue'
import Button from '@/components/common/Button.vue'
import type { Section, Lesson } from '@/types/Course'
import type { Certificate } from '@/types/Certificate'

const route = useRoute()
const router = useRouter()
const courseId = route.params.courseId as string
const lessonId = route.query.lesson as string | undefined

const { currentEnrollment, fetchEnrollment } = useEnrollment()
const courseData = ref<any>(null)
const currentLesson = ref<Lesson | null>(null)
const currentSection = ref<Section | null>(null)
const isLoading = ref(false)
const error = ref<string | null>(null)
const certificate = ref<Certificate | null>(null)
const isDownloadingCertificate = ref(false)

const sections = computed(() => courseData.value?.sections || [])
const enrollment = computed(() => courseData.value?.enrollment)

// Find next and previous lessons
const nextLesson = computed(() => {
  if (!currentLesson.value || !sections.value.length) return null

  const allLessons: Array<{ lesson: Lesson; section: Section }> = []
  sections.value.forEach((section: Section) => {
    section.lessons?.forEach((lesson: Lesson) => {
      allLessons.push({ lesson, section })
    })
  })

  const currentIndex = allLessons.findIndex((item) => item.lesson.id === currentLesson.value?.id)
  if (currentIndex < allLessons.length - 1) {
    return allLessons[currentIndex + 1].lesson
  }
  return null
})

const previousLesson = computed(() => {
  if (!currentLesson.value || !sections.value.length) return null

  const allLessons: Array<{ lesson: Lesson; section: Section }> = []
  sections.value.forEach((section: Section) => {
    section.lessons?.forEach((lesson: Lesson) => {
      allLessons.push({ lesson, section })
    })
  })

  const currentIndex = allLessons.findIndex((item) => item.lesson.id === currentLesson.value?.id)
  if (currentIndex > 0) {
    return allLessons[currentIndex - 1].lesson
  }
  return null
})

onMounted(async () => {
  await loadCourseData()
  
  if (lessonId) {
    selectLesson(lessonId)
  } else {
    // Select first lesson
    const firstSection = sections.value[0]
    if (firstSection?.lessons?.[0]) {
      selectLesson(firstSection.lessons[0].id)
    }
  }
})

const loadCourseData = async () => {
  isLoading.value = true
  error.value = null

  try {
    const data = await enrollmentService.getCourseLearning(courseId)
    courseData.value = data

    // Update enrollment in store
    if (data.enrollment) {
      await fetchEnrollment(data.enrollment.id)
      
      // Load certificate if course is completed
      if (data.enrollment.is_completed) {
        try {
          certificate.value = await certificateService.getEnrollmentCertificate(data.enrollment.id)
        } catch (err: any) {
          // Certificate might not exist yet (still generating)
          if (err.response?.status !== 404) {
            console.error('Failed to load certificate:', err)
          }
        }
      }
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load course'
    if (err.response?.status === 403) {
      router.push({ name: 'course-detail', params: { id: courseId } })
    }
  } finally {
    isLoading.value = false
  }
}

const selectLesson = (lessonId: string) => {
  for (const section of sections.value) {
    const lesson = section.lessons?.find((l: Lesson) => l.id === lessonId)
    if (lesson) {
      currentLesson.value = lesson
      currentSection.value = section
      router.replace({
        query: { ...route.query, lesson: lessonId },
      })
      return
    }
  }
}

const handleLessonComplete = () => {
  // T161: Auto-load next lesson on completion
  if (nextLesson.value) {
    selectLesson(nextLesson.value.id)
  }
}

const handleNextLesson = () => {
  if (nextLesson.value) {
    selectLesson(nextLesson.value.id)
  }
}

const handleDownloadCertificate = async () => {
  if (!certificate.value) return
  
  isDownloadingCertificate.value = true
  try {
    await certificateService.downloadCertificate(certificate.value.id)
  } catch (error) {
    console.error('Failed to download certificate:', error)
    alert('Failed to download certificate. Please try again.')
  } finally {
    isDownloadingCertificate.value = false
  }
}
</script>

<template>
  <div class="course-player">
    <div v-if="isLoading" class="loading">Loading course...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else-if="courseData && enrollment" class="player-container">
      <div class="player-main">
        <LessonPlayer
          v-if="currentLesson && currentSection"
          :enrollment-id="enrollment.id"
          :course-id="courseId"
          :lesson="currentLesson"
          :section="currentSection"
          :next-lesson="nextLesson || undefined"
          @lesson-complete="handleLessonComplete"
          @next-lesson="handleNextLesson"
        />
      </div>

      <div class="player-sidebar">
        <CourseProgress v-if="enrollment" :enrollment="enrollment" />

        <!-- Completion Badge and Certificate -->
        <Card v-if="enrollment?.is_completed" class="completion-card">
          <div class="completion-badge">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
            <h3>Course Completed!</h3>
            <p>Congratulations on completing this course</p>
          </div>
          <Button
            v-if="certificate?.certificate_url"
            @click="handleDownloadCertificate"
            :disabled="isDownloadingCertificate"
            variant="primary"
            class="certificate-button"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            {{ isDownloadingCertificate ? 'Downloading...' : 'Download Certificate' }}
          </Button>
          <div v-else-if="certificate" class="certificate-generating">
            <p>Certificate is being generated. Please check back later.</p>
          </div>
          <div v-else class="certificate-generating">
            <p>Certificate will be available shortly.</p>
          </div>
        </Card>

        <Card title="Course Content" class="curriculum-card">
          <div class="sections-list">
            <div
              v-for="section in sections"
              :key="section.id"
              class="section-item"
              :class="{ active: section.id === currentSection?.id }"
            >
              <h4 class="section-title">{{ section.title }}</h4>
              <div class="lessons-list">
                <button
                  v-for="lesson in section.lessons"
                  :key="lesson.id"
                  :class="['lesson-item', { active: lesson.id === currentLesson?.id }]"
                  @click="selectLesson(lesson.id)"
                >
                  <span class="lesson-type">{{ lesson.type }}</span>
                  <span class="lesson-title">{{ lesson.title }}</span>
                  <span v-if="lesson.progress?.is_completed" class="completed-badge">✓</span>
                </button>
              </div>
            </div>
          </div>
        </Card>
      </div>
    </div>
  </div>
</template>

<style scoped>
.course-player {
  min-height: 100vh;
  padding: 2rem 0;
  background: #f9fafb;
}

.loading,
.error {
  text-align: center;
  padding: 4rem 2rem;
  font-size: 1.25rem;
}

.error {
  color: var(--error);
}

.player-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1.5rem;
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 2rem;
}

.player-main {
  min-width: 0;
}

.player-sidebar {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  position: sticky;
  top: 2rem;
  height: fit-content;
}

.curriculum-card {
  max-height: 600px;
  overflow-y: auto;
}

.sections-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.section-item {
  border-left: 3px solid transparent;
  padding-left: 1rem;
}

.section-item.active {
  border-left-color: var(--primary-color);
}

.section-title {
  font-size: 1rem;
  font-weight: 600;
  margin: 0 0 0.75rem 0;
}

.lessons-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.lesson-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  background: none;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  text-align: left;
  transition: background 0.2s;
}

.lesson-item:hover {
  background: #f3f4f6;
}

.lesson-item.active {
  background: #e0e7ff;
  color: var(--primary-color);
  font-weight: 600;
}

.lesson-type {
  padding: 0.25rem 0.5rem;
  background: #e0e7ff;
  color: #6366f1;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.lesson-title {
  flex: 1;
  font-size: 0.875rem;
}

.completed-badge {
  color: #10b981;
  font-weight: 700;
}

.completion-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.completion-badge {
  text-align: center;
  padding: 1rem 0;
}

.completion-badge svg {
  width: 48px;
  height: 48px;
  margin: 0 auto 1rem;
  display: block;
}

.completion-badge h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.25rem;
  color: white;
}

.completion-badge p {
  margin: 0;
  font-size: 0.875rem;
  opacity: 0.9;
}

.certificate-button {
  width: 100%;
  margin-top: 1rem;
  background: white;
  color: #667eea;
}

.certificate-button:hover {
  background: #f3f4f6;
}

.certificate-button .icon {
  width: 16px;
  height: 16px;
  margin-right: 8px;
}

.certificate-generating {
  text-align: center;
  padding: 1rem 0;
  font-size: 0.875rem;
  opacity: 0.9;
}

@media (max-width: 1024px) {
  .player-container {
    grid-template-columns: 1fr;
  }

  .player-sidebar {
    position: static;
  }
}
</style>

