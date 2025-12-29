<script setup lang="ts">
import { ref, computed } from 'vue'
import Button from '@/components/common/Button.vue'
import discussionService from '@/services/discussionService'
import type { CreateDiscussionData } from '@/types/Discussion'

interface Props {
  courseId: string
  lessonId?: string
}

const props = defineProps<Props>()

const emit = defineEmits<{
  created: []
  cancel: []
}>()

const question = ref('')
const loading = ref(false)
const error = ref<string | null>(null)

const canSubmit = computed(() => {
  return question.value.trim().length >= 10 && question.value.trim().length <= 1000
})

const charCount = computed(() => question.value.length)

const handleSubmit = async () => {
  if (!canSubmit.value || loading.value) return

  loading.value = true
  error.value = null

  try {
    const data: CreateDiscussionData = {
      question: question.value.trim(),
      lesson_id: props.lessonId,
    }

    await discussionService.createDiscussion(props.courseId, data)
    question.value = ''
    emit('created')
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to post question'
  } finally {
    loading.value = false
  }
}

const handleCancel = () => {
  question.value = ''
  error.value = null
  emit('cancel')
}
</script>

<template>
  <div class="question-post">
    <h3 class="post-title">Ask a Question</h3>

    <div v-if="error" class="error-message">{{ error }}</div>

    <div class="form-group">
      <label for="question-text" class="form-label">
        Your Question
        <span class="required">*</span>
      </label>
      <textarea
        id="question-text"
        v-model="question"
        class="question-textarea"
        :class="{ 'textarea-error': error && !canSubmit }"
        placeholder="What would you like to know about this course?"
        rows="4"
        maxlength="1000"
      ></textarea>
      <div class="char-count" :class="{ 'char-count-warning': charCount > 900 }">
        {{ charCount }} / 1000 characters
      </div>
      <div v-if="question.length > 0 && question.length < 10" class="help-text">
        Question must be at least 10 characters
      </div>
    </div>

    <div class="form-actions">
      <Button variant="outline" :disabled="loading" @click="handleCancel">
        Cancel
      </Button>
      <Button :disabled="!canSubmit || loading" :loading="loading" @click="handleSubmit">
        Post Question
      </Button>
    </div>
  </div>
</template>

<style scoped>
.question-post {
  padding: 1.5rem;
  background: white;
  border: 2px solid var(--border-color, #e5e7eb);
  border-radius: 8px;
}

.post-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0 0 1.5rem 0;
  color: var(--text-color, #111827);
}

.error-message {
  padding: 0.75rem;
  margin-bottom: 1rem;
  background: #fee2e2;
  color: #dc2626;
  border-radius: 6px;
  font-size: 0.875rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: var(--text-color, #111827);
}

.required {
  color: #dc2626;
  margin-left: 0.25rem;
}

.question-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--border-color, #e5e7eb);
  border-radius: 8px;
  font-family: inherit;
  font-size: 1rem;
  resize: vertical;
  transition: all 0.2s;
}

.question-textarea:focus {
  outline: none;
  border-color: var(--primary-color, #667eea);
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.question-textarea.textarea-error {
  border-color: #dc2626;
}

.question-textarea.textarea-error:focus {
  border-color: #dc2626;
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.char-count {
  margin-top: 0.5rem;
  font-size: 0.75rem;
  color: var(--text-secondary, #6b7280);
  text-align: right;
}

.char-count-warning {
  color: #f59e0b;
}

.help-text {
  margin-top: 0.25rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.form-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
}
</style>

