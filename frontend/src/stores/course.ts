import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type {
  Course,
  CreateCourseData,
  UpdateCourseData,
  PaginatedResponse,
  Section,
  Lesson,
  CreateSectionData,
  UpdateSectionData,
  CreateLessonData,
  UpdateLessonData,
} from '@/types/Course'
import courseService from '@/services/courseService'

export const useCourseStore = defineStore('course', () => {
  const courses = ref<Course[]>([])
  const currentCourse = ref<Course | null>(null)
  const pagination = ref<{
    current_page: number
    last_page: number
    per_page: number
    total: number
  } | null>(null)

  // Computed
  const publishedCourses = computed(() => courses.value.filter((c) => c.status === 'published'))
  const draftCourses = computed(() => courses.value.filter((c) => c.status === 'draft'))

  // Actions
  async function fetchCourses(params?: { status?: string; page?: number; per_page?: number }): Promise<PaginatedResponse<Course>> {
    const response = await courseService.getCourses(params)
    courses.value = response.data
    pagination.value = response.meta
    return response
  }

  async function fetchCourse(courseId: string): Promise<Course> {
    const course = await courseService.getCourse(courseId)
    currentCourse.value = course
    return course
  }

  async function createCourse(data: CreateCourseData): Promise<Course> {
    const course = await courseService.createCourse(data)
    courses.value.unshift(course)
    return course
  }

  async function updateCourse(courseId: string, data: UpdateCourseData): Promise<Course> {
    const course = await courseService.updateCourse(courseId, data)
    const index = courses.value.findIndex((c) => c.id === courseId)
    if (index !== -1) {
      courses.value[index] = course
    }
    if (currentCourse.value?.id === courseId) {
      currentCourse.value = course
    }
    return course
  }

  async function deleteCourse(courseId: string): Promise<void> {
    await courseService.deleteCourse(courseId)
    courses.value = courses.value.filter((c) => c.id !== courseId)
    if (currentCourse.value?.id === courseId) {
      currentCourse.value = null
    }
  }

  async function publishCourse(courseId: string): Promise<Course> {
    const course = await courseService.publishCourse(courseId)
    const index = courses.value.findIndex((c) => c.id === courseId)
    if (index !== -1) {
      courses.value[index] = course
    }
    if (currentCourse.value?.id === courseId) {
      currentCourse.value = course
    }
    return course
  }

  async function unpublishCourse(courseId: string): Promise<Course> {
    const course = await courseService.unpublishCourse(courseId)
    const index = courses.value.findIndex((c) => c.id === courseId)
    if (index !== -1) {
      courses.value[index] = course
    }
    if (currentCourse.value?.id === courseId) {
      currentCourse.value = course
    }
    return course
  }

  // Section actions
  async function createSection(courseId: string, data: CreateSectionData): Promise<Section> {
    const section = await courseService.createSection(courseId, data)
    if (currentCourse.value?.id === courseId) {
      if (!currentCourse.value.sections) {
        currentCourse.value.sections = []
      }
      currentCourse.value.sections.push(section)
    }
    return section
  }

  async function updateSection(courseId: string, sectionId: string, data: UpdateSectionData): Promise<Section> {
    const section = await courseService.updateSection(courseId, sectionId, data)
    if (currentCourse.value?.id === courseId && currentCourse.value.sections) {
      const index = currentCourse.value.sections.findIndex((s) => s.id === sectionId)
      if (index !== -1) {
        currentCourse.value.sections[index] = section
      }
    }
    return section
  }

  async function deleteSection(courseId: string, sectionId: string): Promise<void> {
    await courseService.deleteSection(courseId, sectionId)
    if (currentCourse.value?.id === courseId && currentCourse.value.sections) {
      currentCourse.value.sections = currentCourse.value.sections.filter((s) => s.id !== sectionId)
    }
  }

  // Lesson actions
  async function createLesson(courseId: string, sectionId: string, data: CreateLessonData): Promise<Lesson> {
    const lesson = await courseService.createLesson(courseId, sectionId, data)
    if (currentCourse.value?.id === courseId && currentCourse.value.sections) {
      const section = currentCourse.value.sections.find((s) => s.id === sectionId)
      if (section) {
        if (!section.lessons) {
          section.lessons = []
        }
        section.lessons.push(lesson)
      }
    }
    return lesson
  }

  async function updateLesson(
    courseId: string,
    sectionId: string,
    lessonId: string,
    data: UpdateLessonData
  ): Promise<Lesson> {
    const lesson = await courseService.updateLesson(courseId, sectionId, lessonId, data)
    if (currentCourse.value?.id === courseId && currentCourse.value.sections) {
      const section = currentCourse.value.sections.find((s) => s.id === sectionId)
      if (section?.lessons) {
        const index = section.lessons.findIndex((l) => l.id === lessonId)
        if (index !== -1) {
          section.lessons[index] = lesson
        }
      }
    }
    return lesson
  }

  async function deleteLesson(courseId: string, sectionId: string, lessonId: string): Promise<void> {
    await courseService.deleteLesson(courseId, sectionId, lessonId)
    if (currentCourse.value?.id === courseId && currentCourse.value.sections) {
      const section = currentCourse.value.sections.find((s) => s.id === sectionId)
      if (section?.lessons) {
        section.lessons = section.lessons.filter((l) => l.id !== lessonId)
      }
    }
  }

  async function uploadVideo(courseId: string, sectionId: string, lessonId: string, videoFile: File, filename?: string): Promise<Lesson> {
    const lesson = await courseService.uploadVideo(courseId, sectionId, lessonId, videoFile, filename)
    if (currentCourse.value?.id === courseId && currentCourse.value.sections) {
      const section = currentCourse.value.sections.find((s) => s.id === sectionId)
      if (section?.lessons) {
        const index = section.lessons.findIndex((l) => l.id === lessonId)
        if (index !== -1) {
          section.lessons[index] = lesson
        }
      }
    }
    return lesson
  }

  function clearCurrentCourse() {
    currentCourse.value = null
  }

  return {
    courses,
    currentCourse,
    pagination,
    publishedCourses,
    draftCourses,
    fetchCourses,
    fetchCourse,
    createCourse,
    updateCourse,
    deleteCourse,
    publishCourse,
    unpublishCourse,
    createSection,
    updateSection,
    deleteSection,
    createLesson,
    updateLesson,
    deleteLesson,
    uploadVideo,
    clearCurrentCourse,
  }
})

