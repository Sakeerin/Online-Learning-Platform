<script setup lang="ts">
import { ref, computed } from 'vue'
import Button from '@/components/common/Button.vue'
import Input from '@/components/common/Input.vue'
import type { Quiz, CreateQuizData, CreateQuestionData } from '@/types/Quiz'

interface Props {
  quiz?: Quiz
  lessonId: string
  courseId: string
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  loading: false,
})

const emit = defineEmits<{
  submit: [data: CreateQuizData]
  cancel: []
}>()

const formData = ref<CreateQuizData>({
  title: props.quiz?.title || '',
  passing_score: props.quiz?.passing_score || 80,
  allow_retake: props.quiz?.allow_retake ?? true,
  questions: props.quiz?.questions?.map(q => ({
    question_text: q.question_text,
    question_type: q.question_type,
    options: q.options,
    correct_answer: q.correct_answer || '',
    explanation: q.explanation || '',
    order: q.order,
  })) || [],
})

const errors = ref<Record<string, string>>({})

const addQuestion = () => {
  formData.value.questions.push({
    question_text: '',
    question_type: 'multiple_choice',
    options: { A: '', B: '', C: '', D: '' },
    correct_answer: '',
    explanation: '',
    order: formData.value.questions.length + 1,
  })
}

const removeQuestion = (index: number) => {
  formData.value.questions.splice(index, 1)
  // Reorder questions
  formData.value.questions.forEach((q, i) => {
    q.order = i + 1
  })
}

const addOption = (questionIndex: number) => {
  const question = formData.value.questions[questionIndex]
  const optionKeys = Object.keys(question.options)
  const lastKey = optionKeys[optionKeys.length - 1]
  const nextKey = String.fromCharCode(lastKey.charCodeAt(0) + 1)
  question.options[nextKey] = ''
}

const removeOption = (questionIndex: number, optionKey: string) => {
  const question = formData.value.questions[questionIndex]
  delete question.options[optionKey]
  // If correct answer was this option, clear it
  if (question.correct_answer === optionKey) {
    question.correct_answer = ''
  }
}

const validate = (): boolean => {
  errors.value = {}

  if (!formData.value.title.trim()) {
    errors.value.title = 'Quiz title is required'
  }

  if (formData.value.passing_score < 0 || formData.value.passing_score > 100) {
    errors.value.passing_score = 'Passing score must be between 0 and 100'
  }

  if (formData.value.questions.length === 0) {
    errors.value.questions = 'At least one question is required'
  }

  formData.value.questions.forEach((q, index) => {
    if (!q.question_text.trim()) {
      errors.value[`question_${index}_text`] = 'Question text is required'
    }

    const optionCount = Object.keys(q.options).filter(k => q.options[k].trim()).length
    if (optionCount < 2) {
      errors.value[`question_${index}_options`] = 'At least 2 options are required'
    }

    if (!q.correct_answer) {
      errors.value[`question_${index}_correct`] = 'Correct answer is required'
    } else if (!q.options[q.correct_answer]) {
      errors.value[`question_${index}_correct`] = 'Correct answer must be one of the options'
    }
  })

  return Object.keys(errors.value).length === 0
}

const handleSubmit = () => {
  if (validate()) {
    emit('submit', { ...formData.value })
  }
}

const handleCancel = () => {
  emit('cancel')
}
</script>

<template>
  <div class="quiz-builder">
    <h3 class="builder-title">{{ quiz ? 'Edit Quiz' : 'Create Quiz' }}</h3>

    <div class="form-section">
      <Input
        v-model="formData.title"
        label="Quiz Title"
        placeholder="e.g., Chapter 1 Quiz"
        :error="errors.title"
        required
      />

      <div class="form-row">
        <div class="form-group">
          <label class="input-label">Passing Score (%)</label>
          <input
            v-model.number="formData.passing_score"
            type="number"
            min="0"
            max="100"
            class="input"
            :class="{ 'input-error': errors.passing_score }"
          />
          <span v-if="errors.passing_score" class="error-message">{{ errors.passing_score }}</span>
        </div>

        <div class="form-group">
          <label class="checkbox-label">
            <input
              v-model="formData.allow_retake"
              type="checkbox"
              class="checkbox"
            />
            <span>Allow Retakes</span>
          </label>
        </div>
      </div>
    </div>

    <div class="questions-section">
      <div class="section-header">
        <h4>Questions</h4>
        <Button variant="outline" size="sm" @click="addQuestion">+ Add Question</Button>
      </div>

      <div v-if="errors.questions" class="error-message">{{ errors.questions }}</div>

      <div
        v-for="(question, qIndex) in formData.questions"
        :key="qIndex"
        class="question-card"
      >
        <div class="question-header">
          <span class="question-number">Question {{ qIndex + 1 }}</span>
          <Button variant="outline" size="sm" @click="removeQuestion(qIndex)">Remove</Button>
        </div>

        <div class="form-group">
          <label class="input-label">Question Type</label>
          <select v-model="question.question_type" class="select">
            <option value="multiple_choice">Multiple Choice</option>
            <option value="true_false">True/False</option>
          </select>
        </div>

        <div class="form-group">
          <label class="input-label">Question Text</label>
          <textarea
            v-model="question.question_text"
            class="textarea"
            :class="{ 'input-error': errors[`question_${qIndex}_text`] }"
            placeholder="Enter your question here..."
            rows="3"
          ></textarea>
          <span v-if="errors[`question_${qIndex}_text`]" class="error-message">
            {{ errors[`question_${qIndex}_text`] }}
          </span>
        </div>

        <div class="options-section">
          <label class="input-label">Answer Options</label>
          <div
            v-for="(optionValue, optionKey) in question.options"
            :key="optionKey"
            class="option-row"
          >
            <input
              v-model="question.correct_answer"
              type="radio"
              :name="`correct_${qIndex}`"
              :value="optionKey"
              class="radio"
            />
            <input
              v-model="question.options[optionKey]"
              type="text"
              class="input option-input"
              :placeholder="`Option ${optionKey}`"
            />
            <Button
              v-if="Object.keys(question.options).length > 2"
              variant="outline"
              size="sm"
              @click="removeOption(qIndex, optionKey)"
            >
              Remove
            </Button>
          </div>

          <div v-if="errors[`question_${qIndex}_options`]" class="error-message">
            {{ errors[`question_${qIndex}_options`] }}
          </div>

          <Button
            v-if="Object.keys(question.options).length < 6"
            variant="outline"
            size="sm"
            @click="addOption(qIndex)"
          >
            + Add Option
          </Button>
        </div>

        <div v-if="errors[`question_${qIndex}_correct`]" class="error-message">
          {{ errors[`question_${qIndex}_correct`] }}
        </div>

        <div class="form-group">
          <label class="input-label">Explanation (Optional)</label>
          <textarea
            v-model="question.explanation"
            class="textarea"
            placeholder="Explain why this is the correct answer..."
            rows="2"
          ></textarea>
        </div>
      </div>
    </div>

    <div class="builder-actions">
      <Button variant="outline" @click="handleCancel">Cancel</Button>
      <Button :loading="loading" @click="handleSubmit">
        {{ quiz ? 'Update Quiz' : 'Create Quiz' }}
      </Button>
    </div>
  </div>
</template>

<style scoped>
.quiz-builder {
  display: flex;
  flex-direction: column;
  gap: 2rem;
  padding: 2rem;
  background: white;
  border-radius: 8px;
}

.builder-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin: 0;
}

.form-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.input-label {
  font-weight: 500;
  color: var(--text-color);
}

.input,
.select,
.textarea {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
  font-family: inherit;
}

.input:focus,
.select:focus,
.textarea:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.input-error {
  border-color: var(--error-color);
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.checkbox {
  width: 1.25rem;
  height: 1.25rem;
  cursor: pointer;
}

.questions-section {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.section-header h4 {
  margin: 0;
  font-size: 1.25rem;
}

.question-card {
  padding: 1.5rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  background: var(--bg-secondary);
}

.question-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.question-number {
  font-weight: 600;
  color: var(--primary-color);
}

.options-section {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-top: 1rem;
}

.option-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.radio {
  width: 1.25rem;
  height: 1.25rem;
  cursor: pointer;
}

.option-input {
  flex: 1;
}

.error-message {
  color: var(--error-color);
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.builder-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  padding-top: 1rem;
  border-top: 2px solid var(--border-color);
}
</style>

