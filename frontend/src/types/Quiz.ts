export interface Quiz {
  id: string
  lesson_id: string
  title: string
  passing_score: number
  allow_retake: boolean
  questions?: Question[]
  created_at: string
  updated_at: string
}

export interface Question {
  id: string
  quiz_id: string
  question_text: string
  question_type: 'multiple_choice' | 'true_false'
  options: Record<string, string>
  correct_answer?: string // Only available for instructors or after submission
  explanation?: string // Only available after submission
  order: number
  created_at: string
  updated_at: string
}

export interface QuizAttempt {
  id: string
  enrollment_id: string
  quiz_id: string
  attempt_number: number
  answers: Record<string, string>
  score: number
  is_passed: boolean
  submitted_at: string
  created_at: string
  updated_at: string
}

export interface QuizResult {
  question_id: string
  question_text: string
  question_type: 'multiple_choice' | 'true_false'
  options: Record<string, string>
  student_answer: string
  correct_answer: string
  is_correct: boolean
  explanation?: string
}

export interface QuizSubmissionResponse {
  attempt: QuizAttempt
  score: number
  is_passed: boolean
  passing_score: number
  results: QuizResult[]
}

export interface CreateQuizData {
  title: string
  passing_score?: number
  allow_retake?: boolean
  questions: CreateQuestionData[]
}

export interface CreateQuestionData {
  question_text: string
  question_type: 'multiple_choice' | 'true_false'
  options: Record<string, string>
  correct_answer: string
  explanation?: string
  order?: number
}

