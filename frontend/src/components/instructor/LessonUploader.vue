<script setup lang="ts">
import { ref } from 'vue'
import Button from '@/components/common/Button.vue'
import Input from '@/components/common/Input.vue'
import type { Lesson, CreateLessonData } from '@/types/Course'

interface Props {
  lesson?: Lesson
  order?: number
}

const props = defineProps<Props>()

const emit = defineEmits<{
  save: [data: CreateLessonData]
  cancel: []
}>()

const formData = ref<CreateLessonData>({
  title: props.lesson?.title || '',
  type: props.lesson?.type || 'video',
  content: props.lesson?.content || {},
  duration: props.lesson?.duration,
  is_preview: props.lesson?.is_preview || false,
  order: props.lesson?.order || props.order || 1,
})

const videoFile = ref<File | null>(null)
const videoFileInput = ref<HTMLInputElement | null>(null)

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    videoFile.value = target.files[0]
  }
}

const handleSave = () => {
  emit('save', { ...formData.value })
}

const handleCancel = () => {
  emit('cancel')
}
</script>

<template>
  <div class="lesson-uploader">
    <Input v-model="formData.title" label="Lesson Title" placeholder="e.g., Introduction to HTML" required />

    <div class="form-group">
      <label class="input-label">
        Lesson Type
        <span class="required">*</span>
      </label>
      <select v-model="formData.type" class="select" required>
        <option value="video">Video</option>
        <option value="quiz">Quiz</option>
        <option value="article">Article</option>
      </select>
    </div>

    <div v-if="formData.type === 'video'" class="form-group">
      <label class="input-label">Video File</label>
      <input
        ref="videoFileInput"
        type="file"
        accept="video/*"
        @change="handleFileSelect"
        class="file-input"
      />
      <div v-if="videoFile" class="file-info">
        Selected: {{ videoFile.name }} ({{ (videoFile.size / 1024 / 1024).toFixed(2) }} MB)
      </div>
    </div>

    <Input
      v-model.number="formData.duration"
      type="number"
      label="Duration (seconds)"
      placeholder="e.g., 600"
    />

    <div class="form-group">
      <label class="checkbox-label">
        <input v-model="formData.is_preview" type="checkbox" class="checkbox" />
        <span>Make this lesson a free preview</span>
      </label>
    </div>

    <div class="editor-actions">
      <Button variant="outline" @click="handleCancel">Cancel</Button>
      <Button @click="handleSave">Save Lesson</Button>
    </div>
  </div>
</template>

<style scoped>
.lesson-uploader {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1.5rem;
  background: white;
  border-radius: 8px;
  border: 2px solid var(--border-color);
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.select {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
  font-family: inherit;
  background: white;
}

.file-input {
  padding: 0.5rem;
  border: 2px dashed var(--border-color);
  border-radius: 8px;
  cursor: pointer;
}

.file-info {
  margin-top: 0.5rem;
  padding: 0.5rem;
  background: #f0f9ff;
  border-radius: 4px;
  font-size: 0.875rem;
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

.editor-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  margin-top: 1rem;
}
</style>

