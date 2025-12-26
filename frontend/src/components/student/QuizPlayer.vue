<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import Button from '@/components/common/Button.vue'
import quizService from '@/services/quizService'
import type { Quiz, Question, QuizSubmissionResponse, QuizResult } from '@/types/Quiz'

interface Props {
  courseId: string
  lessonId: string
  quiz: Quiz
}

const props = defineProps<Props>()

const emit = defineEmits<{
  completed: [results: QuizSubmissionResponse]
}>()

const currentQuestionIndex = ref(0)
const answers = ref<Record<string, string>>({})
const isSubmitted = ref(false)
const results = ref<QuizSubmissionResponse | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)
const canRetake = ref(false)

const currentQuestion = computed(() => {
  if (!props.quiz.questions || props.quiz.questions.length === 0) {
    return null
  }
  return props.quiz.questions[currentQuestionIndex.value]
})

const totalQuestions = computed(() => props.quiz.questions?.length || 0)
const progress = computed(() => {
  if (totalQuestions.value === 0) return 0
  return ((currentQuestionIndex.value + 1) / totalQuestions.value) * 100
})

const canSubmit = computed(() => {
  if (!props.quiz.questions) return false
  return props.quiz.questions.every(q => answers.value[q.id] !== undefined && answers.value[q.id] !== '')
})

const score = computed(() => results.value?.score || 0)
const isPassed = computed(() => results.value?.is_passed || false)

const selectAnswer = (questionId: string, answer: string) => {
  answers.value[questionId] = answer
}

const nextQuestion = () => {
  if (currentQuestionIndex.value < totalQuestions.value - 1) {
    currentQuestionIndex.value++
  }
}

const previousQuestion = () => {
  if (currentQuestionIndex.value > 0) {
    currentQuestionIndex.value--
  }
}

const goToQuestion = (index: number) => {
  currentQuestionIndex.value = index
}

const submitQuiz = async () => {
  if (!canSubmit.value) return

  loading.value = true
  error.value = null

  try {
    const response = await quizService.submitQuiz(props.courseId, props.lessonId, answers.value)
    results.value = response
    isSubmitted.value = true
    emit('completed', response)
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to submit quiz'
  } finally {
    loading.value = false
  }
}

const checkRetake = async () => {
  try {
    canRetake.value = await quizService.canRetake(props.courseId, props.lessonId)
  } catch (err) {
    console.error('Failed to check retake status', err)
  }
}

const getQuestionResult = (questionId: string): QuizResult | null => {
  if (!results.value) return null
  return results.value.results.find(r => r.question_id === questionId) || null
}

const isCorrect = (questionId: string): boolean => {
  const result = getQuestionResult(questionId)
  return result?.is_correct || false
}

onMounted(() => {
  checkRetake()
})
</script>

<template>
  <div class="quiz-player">
    <div v-if="!isSubmitted" class="quiz-taking">
      <div class="quiz-header">
        <h2 class="quiz-title">{{ quiz.title }}</h2>
        <div class="quiz-meta">
          <span>{{ totalQuestions }} Questions</span>
          <span>Passing Score: {{ quiz.passing_score }}%</span>
        </div>
      </div>

      <div class="progress-bar">
        <div class="progress-fill" :style="{ width: `${progress}%` }"></div>
      </div>

      <div v-if="error" class="error-message">{{ error }}</div>

      <div v-if="currentQuestion" class="question-container">
        <div class="question-header">
          <span class="question-number">Question {{ currentQuestionIndex + 1 }} of {{ totalQuestions }}</span>
        </div>

        <div class="question-text">{{ currentQuestion.question_text }}</div>

        <div class="options-list">
          <label
            v-for="(optionValue, optionKey) in currentQuestion.options"
            :key="optionKey"
            class="option-label"
            :class="{ selected: answers[currentQuestion.id] === optionKey }"
          >
            <input
              v-model="answers[currentQuestion.id]"
              type="radio"
              :name="`question_${currentQuestion.id}`"
              :value="optionKey"
              class="radio-input"
            />
            <span class="option-key">{{ optionKey }}.</span>
            <span class="option-text">{{ optionValue }}</span>
          </label>
        </div>
      </div>

      <div class="navigation">
        <div class="question-nav">
          <Button
            variant="outline"
            :disabled="currentQuestionIndex === 0"
            @click="previousQuestion"
          >
            Previous
          </Button>
          <Button
            v-if="currentQuestionIndex < totalQuestions - 1"
            @click="nextQuestion"
          >
            Next
          </Button>
          <Button
            v-else
            :disabled="!canSubmit || loading"
            :loading="loading"
            @click="submitQuiz"
          >
            Submit Quiz
          </Button>
        </div>

        <div class="question-jumper">
          <span class="jumper-label">Jump to:</span>
          <button
            v-for="(q, index) in quiz.questions"
            :key="q.id"
            class="jumper-button"
            :class="{
              answered: answers[q.id],
              current: index === currentQuestionIndex,
            }"
            @click="goToQuestion(index)"
          >
            {{ index + 1 }}
          </button>
        </div>
      </div>
    </div>

    <div v-else class="quiz-results">
      <div class="results-header">
        <h2 class="results-title">Quiz Results</h2>
        <div class="score-display" :class="{ passed: isPassed, failed: !isPassed }">
          <div class="score-value">{{ score.toFixed(1) }}%</div>
          <div class="score-label">
            {{ isPassed ? 'Passed' : 'Failed' }}
            <span v-if="!isPassed">(Need {{ quiz.passing_score }}%)</span>
          </div>
        </div>
      </div>

      <div class="results-summary">
        <div class="summary-item">
          <span class="summary-label">Correct Answers:</span>
          <span class="summary-value">{{ results?.results.filter(r => r.is_correct).length || 0 }} / {{ totalQuestions }}</span>
        </div>
        <div class="summary-item">
          <span class="summary-label">Passing Score:</span>
          <span class="summary-value">{{ quiz.passing_score }}%</span>
        </div>
      </div>

      <div class="results-questions">
        <h3 class="section-title">Question Review</h3>
        <div
          v-for="(question, index) in quiz.questions"
          :key="question.id"
          class="result-question"
          :class="{
            correct: isCorrect(question.id),
            incorrect: !isCorrect(question.id),
          }"
        >
          <div class="result-question-header">
            <span class="result-question-number">Question {{ index + 1 }}</span>
            <span
              class="result-badge"
              :class="{ correct: isCorrect(question.id), incorrect: !isCorrect(question.id) }"
            >
              {{ isCorrect(question.id) ? 'Correct' : 'Incorrect' }}
            </span>
          </div>

          <div class="result-question-text">{{ question.question_text }}</div>

          <div class="result-options">
            <div
              v-for="(optionValue, optionKey) in question.options"
              :key="optionKey"
              class="result-option"
              :class="{
                selected: getQuestionResult(question.id)?.student_answer === optionKey,
                correct: getQuestionResult(question.id)?.correct_answer === optionKey,
              }"
            >
              <span class="option-key">{{ optionKey }}.</span>
              <span class="option-text">{{ optionValue }}</span>
              <span v-if="getQuestionResult(question.id)?.correct_answer === optionKey" class="correct-mark">✓ Correct</span>
              <span v-if="getQuestionResult(question.id)?.student_answer === optionKey && !isCorrect(question.id)" class="incorrect-mark">✗ Your Answer</span>
            </div>
          </div>

          <div v-if="getQuestionResult(question.id)?.explanation" class="explanation">
            <strong>Explanation:</strong> {{ getQuestionResult(question.id)?.explanation }}
          </div>
        </div>
      </div>

      <div v-if="canRetake" class="retake-section">
        <Button @click="$emit('retake')">Retake Quiz</Button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.quiz-player {
  max-width: 900px;
  margin: 0 auto;
  padding: 2rem;
  background: white;
  border-radius: 8px;
}

.quiz-header {
  margin-bottom: 1.5rem;
}

.quiz-title {
  font-size: 1.75rem;
  font-weight: 600;
  margin: 0 0 0.5rem 0;
}

.quiz-meta {
  display: flex;
  gap: 1.5rem;
  color: var(--text-secondary);
  font-size: 0.875rem;
}

.progress-bar {
  width: 100%;
  height: 8px;
  background: var(--bg-secondary);
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 2rem;
}

.progress-fill {
  height: 100%;
  background: var(--primary-color);
  transition: width 0.3s ease;
}

.error-message {
  padding: 1rem;
  background: var(--error-bg);
  color: var(--error-color);
  border-radius: 8px;
  margin-bottom: 1rem;
}

.question-container {
  margin-bottom: 2rem;
}

.question-header {
  margin-bottom: 1rem;
}

.question-number {
  color: var(--text-secondary);
  font-size: 0.875rem;
}

.question-text {
  font-size: 1.25rem;
  font-weight: 500;
  margin-bottom: 1.5rem;
  line-height: 1.6;
}

.options-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.option-label {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.option-label:hover {
  border-color: var(--primary-color);
  background: var(--bg-secondary);
}

.option-label.selected {
  border-color: var(--primary-color);
  background: rgba(102, 126, 234, 0.1);
}

.radio-input {
  width: 1.25rem;
  height: 1.25rem;
  cursor: pointer;
}

.option-key {
  font-weight: 600;
  color: var(--primary-color);
  min-width: 1.5rem;
}

.option-text {
  flex: 1;
}

.navigation {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 2px solid var(--border-color);
}

.question-nav {
  display: flex;
  justify-content: space-between;
}

.question-jumper {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.jumper-label {
  font-size: 0.875rem;
  color: var(--text-secondary);
}

.jumper-button {
  width: 2.5rem;
  height: 2.5rem;
  border: 2px solid var(--border-color);
  border-radius: 6px;
  background: white;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}

.jumper-button:hover {
  border-color: var(--primary-color);
}

.jumper-button.answered {
  background: var(--primary-color);
  color: white;
  border-color: var(--primary-color);
}

.jumper-button.current {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
}

.quiz-results {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.results-header {
  text-align: center;
  padding: 2rem;
  background: var(--bg-secondary);
  border-radius: 8px;
}

.results-title {
  font-size: 1.75rem;
  font-weight: 600;
  margin: 0 0 1rem 0;
}

.score-display {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.score-value {
  font-size: 3rem;
  font-weight: 700;
}

.score-display.passed .score-value {
  color: var(--success-color);
}

.score-display.failed .score-value {
  color: var(--error-color);
}

.score-label {
  font-size: 1.125rem;
  color: var(--text-secondary);
}

.results-summary {
  display: flex;
  justify-content: space-around;
  padding: 1.5rem;
  background: var(--bg-secondary);
  border-radius: 8px;
}

.summary-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.summary-label {
  font-size: 0.875rem;
  color: var(--text-secondary);
}

.summary-value {
  font-size: 1.5rem;
  font-weight: 600;
}

.results-questions {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.section-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0;
}

.result-question {
  padding: 1.5rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
}

.result-question.correct {
  border-color: var(--success-color);
  background: rgba(34, 197, 94, 0.05);
}

.result-question.incorrect {
  border-color: var(--error-color);
  background: rgba(239, 68, 68, 0.05);
}

.result-question-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.result-question-number {
  font-weight: 600;
  color: var(--text-secondary);
}

.result-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-size: 0.875rem;
  font-weight: 600;
}

.result-badge.correct {
  background: var(--success-color);
  color: white;
}

.result-badge.incorrect {
  background: var(--error-color);
  color: white;
}

.result-question-text {
  font-size: 1.125rem;
  font-weight: 500;
  margin-bottom: 1rem;
}

.result-options {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.result-option {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  border: 2px solid var(--border-color);
  border-radius: 6px;
}

.result-option.correct {
  background: rgba(34, 197, 94, 0.1);
  border-color: var(--success-color);
}

.result-option.selected:not(.correct) {
  background: rgba(239, 68, 68, 0.1);
  border-color: var(--error-color);
}

.correct-mark {
  margin-left: auto;
  color: var(--success-color);
  font-weight: 600;
}

.incorrect-mark {
  margin-left: auto;
  color: var(--error-color);
  font-weight: 600;
}

.explanation {
  margin-top: 1rem;
  padding: 1rem;
  background: var(--bg-secondary);
  border-radius: 6px;
  line-height: 1.6;
}

.retake-section {
  text-align: center;
  padding-top: 2rem;
  border-top: 2px solid var(--border-color);
}
</style>

