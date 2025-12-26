import api from './api'
import type {
  Quiz,
  QuizAttempt,
  QuizSubmissionResponse,
  CreateQuizData,
} from '@/types/Quiz'

const quizService = {
  // Instructor endpoints
  async createQuiz(courseId: string, lessonId: string, data: CreateQuizData): Promise<Quiz> {
    const response = await api.post<{ data: Quiz }>(`/instructor/courses/${courseId}/lessons/${lessonId}/quizzes`, data)
    return response.data.data
  },

  async getQuiz(courseId: string, lessonId: string, quizId: string): Promise<Quiz> {
    const response = await api.get<{ data: Quiz }>(`/instructor/courses/${courseId}/lessons/${lessonId}/quizzes/${quizId}`)
    return response.data.data
  },

  async updateQuiz(courseId: string, lessonId: string, quizId: string, data: CreateQuizData): Promise<Quiz> {
    const response = await api.put<{ data: Quiz }>(`/instructor/courses/${courseId}/lessons/${lessonId}/quizzes/${quizId}`, data)
    return response.data.data
  },

  async deleteQuiz(courseId: string, lessonId: string, quizId: string): Promise<void> {
    await api.delete(`/instructor/courses/${courseId}/lessons/${lessonId}/quizzes/${quizId}`)
  },

  // Student endpoints
  async getQuizForStudent(courseId: string, lessonId: string): Promise<Quiz> {
    const response = await api.get<{ data: Quiz }>(`/student/courses/${courseId}/lessons/${lessonId}/quiz`)
    return response.data.data
  },

  async submitQuiz(courseId: string, lessonId: string, answers: Record<string, string>): Promise<QuizSubmissionResponse> {
    const response = await api.post<{ data: QuizSubmissionResponse }>(`/student/courses/${courseId}/lessons/${lessonId}/quiz/submit`, { answers })
    return response.data.data
  },

  async getQuizAttempts(courseId: string, lessonId: string): Promise<QuizAttempt[]> {
    const response = await api.get<{ data: QuizAttempt[] }>(`/student/courses/${courseId}/lessons/${lessonId}/quiz/attempts`)
    return response.data.data
  },

  async getQuizResults(courseId: string, lessonId: string, attemptId: string): Promise<QuizSubmissionResponse> {
    const response = await api.get<{ data: QuizSubmissionResponse }>(`/student/courses/${courseId}/lessons/${lessonId}/quiz/attempts/${attemptId}`)
    return response.data.data
  },

  async canRetake(courseId: string, lessonId: string): Promise<boolean> {
    const response = await api.get<{ can_retake: boolean }>(`/student/courses/${courseId}/lessons/${lessonId}/quiz/can-retake`)
    return response.data.can_retake
  },
}

export default quizService

