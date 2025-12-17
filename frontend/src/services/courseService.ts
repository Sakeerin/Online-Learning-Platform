import api from './api'
import type {
  Course,
  CreateCourseData,
  UpdateCourseData,
  CreateSectionData,
  UpdateSectionData,
  CreateLessonData,
  UpdateLessonData,
  PaginatedResponse,
  Section,
  Lesson,
} from '@/types/Course'

const courseService = {
  // Course endpoints
  async getCourses(params?: { status?: string; page?: number; per_page?: number }): Promise<PaginatedResponse<Course>> {
    const response = await api.get<{ data: Course[]; meta: any }>('/instructor/courses', { params })
    return {
      data: response.data.data,
      meta: response.data.meta,
    }
  },

  async getCourse(courseId: string): Promise<Course> {
    const response = await api.get<{ data: Course }>(`/instructor/courses/${courseId}`)
    return response.data.data
  },

  async createCourse(data: CreateCourseData): Promise<Course> {
    const response = await api.post<{ data: Course }>('/instructor/courses', data)
    return response.data.data
  },

  async updateCourse(courseId: string, data: UpdateCourseData): Promise<Course> {
    const response = await api.put<{ data: Course }>(`/instructor/courses/${courseId}`, data)
    return response.data.data
  },

  async deleteCourse(courseId: string): Promise<void> {
    await api.delete(`/instructor/courses/${courseId}`)
  },

  async publishCourse(courseId: string): Promise<Course> {
    const response = await api.post<{ data: Course }>(`/instructor/courses/${courseId}/publish`)
    return response.data.data
  },

  async unpublishCourse(courseId: string): Promise<Course> {
    const response = await api.post<{ data: Course }>(`/instructor/courses/${courseId}/unpublish`)
    return response.data.data
  },

  // Section endpoints
  async createSection(courseId: string, data: CreateSectionData): Promise<Section> {
    const response = await api.post<{ data: Section }>(`/instructor/courses/${courseId}/sections`, data)
    return response.data.data
  },

  async updateSection(courseId: string, sectionId: string, data: UpdateSectionData): Promise<Section> {
    const response = await api.put<{ data: Section }>(`/instructor/courses/${courseId}/sections/${sectionId}`, data)
    return response.data.data
  },

  async deleteSection(courseId: string, sectionId: string): Promise<void> {
    await api.delete(`/instructor/courses/${courseId}/sections/${sectionId}`)
  },

  async reorderSections(courseId: string, sections: Array<{ id: string; order: number }>): Promise<void> {
    await api.post(`/instructor/courses/${courseId}/sections/reorder`, { sections })
  },

  // Lesson endpoints
  async createLesson(courseId: string, sectionId: string, data: CreateLessonData): Promise<Lesson> {
    const response = await api.post<{ data: Lesson }>(
      `/instructor/courses/${courseId}/sections/${sectionId}/lessons`,
      data
    )
    return response.data.data
  },

  async updateLesson(courseId: string, sectionId: string, lessonId: string, data: UpdateLessonData): Promise<Lesson> {
    const response = await api.put<{ data: Lesson }>(
      `/instructor/courses/${courseId}/sections/${sectionId}/lessons/${lessonId}`,
      data
    )
    return response.data.data
  },

  async deleteLesson(courseId: string, sectionId: string, lessonId: string): Promise<void> {
    await api.delete(`/instructor/courses/${courseId}/sections/${sectionId}/lessons/${lessonId}`)
  },

  async uploadVideo(
    courseId: string,
    sectionId: string,
    lessonId: string,
    videoFile: File,
    filename?: string
  ): Promise<Lesson> {
    const formData = new FormData()
    formData.append('video', videoFile)
    if (filename) {
      formData.append('filename', filename)
    }

    const response = await api.post<{ data: Lesson }>(
      `/instructor/courses/${courseId}/sections/${sectionId}/lessons/${lessonId}/upload-video`,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    )
    return response.data.data
  },

  async reorderLessons(courseId: string, sectionId: string, lessons: Array<{ id: string; order: number }>): Promise<void> {
    await api.post(`/instructor/courses/${courseId}/sections/${sectionId}/lessons/reorder`, { lessons })
  },
}

export default courseService

