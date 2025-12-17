<script setup lang="ts">
import { ref, computed } from 'vue'
import Input from '@/components/common/Input.vue'
import Button from '@/components/common/Button.vue'
import type { CreateCourseData, Course } from '@/types/Course'

interface Props {
  course?: Course
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  loading: false,
})

const emit = defineEmits<{
  submit: [data: CreateCourseData]
}>()

const formData = ref<CreateCourseData>({
  title: props.course?.title || '',
  subtitle: props.course?.subtitle || '',
  description: props.course?.description || '',
  learning_objectives: props.course?.learning_objectives || [],
  category: props.course?.category || '',
  subcategory: props.course?.subcategory || '',
  difficulty_level: props.course?.difficulty_level || 'beginner',
  thumbnail: props.course?.thumbnail || '',
  price: props.course?.price || 0,
  currency: props.course?.currency || 'THB',
})

const categories = ['Development', 'Design', 'Business', 'Marketing', 'Photography', 'Music', 'Health', 'Lifestyle', 'Other']
const difficultyLevels = [
  { value: 'beginner', label: 'Beginner' },
  { value: 'intermediate', label: 'Intermediate' },
  { value: 'advanced', label: 'Advanced' },
]

const newObjective = ref('')
const errors = ref<Record<string, string>>({})

const addObjective = () => {
  if (newObjective.value.trim()) {
    if (!formData.value.learning_objectives) {
      formData.value.learning_objectives = []
    }
    formData.value.learning_objectives.push(newObjective.value.trim())
    newObjective.value = ''
  }
}

const removeObjective = (index: number) => {
  if (formData.value.learning_objectives) {
    formData.value.learning_objectives.splice(index, 1)
  }
}

const validate = (): boolean => {
  errors.value = {}

  if (!formData.value.title.trim()) {
    errors.value.title = 'Title is required'
  }
  if (!formData.value.description.trim()) {
    errors.value.description = 'Description is required'
  } else if (formData.value.description.length < 100) {
    errors.value.description = 'Description must be at least 100 characters'
  }
  if (!formData.value.category) {
    errors.value.category = 'Category is required'
  }
  if (formData.value.price < 0) {
    errors.value.price = 'Price cannot be negative'
  }

  return Object.keys(errors.value).length === 0
}

const handleSubmit = () => {
  if (validate()) {
    emit('submit', { ...formData.value })
  }
}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="course-form">
    <div class="form-grid">
      <Input
        v-model="formData.title"
        label="Course Title"
        placeholder="e.g., Complete Web Development Bootcamp"
        required
        :error="errors.title"
      />

      <Input
        v-model="formData.subtitle"
        label="Subtitle"
        placeholder="Short tagline for your course"
        :error="errors.subtitle"
      />

      <div class="form-group full-width">
        <label class="input-label">
          Description
          <span class="required">*</span>
        </label>
        <textarea
          v-model="formData.description"
          class="textarea"
          placeholder="Describe your course in detail (minimum 100 characters)"
          rows="6"
          required
          :class="{ 'input-error': errors.description }"
        ></textarea>
        <div v-if="errors.description" class="input-error-message">{{ errors.description }}</div>
        <div class="char-count">{{ formData.description.length }} / 100 characters minimum</div>
      </div>

      <div class="form-group">
        <label class="input-label">
          Category
          <span class="required">*</span>
        </label>
        <select v-model="formData.category" class="select" required :class="{ 'input-error': errors.category }">
          <option value="">Select a category</option>
          <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
        </select>
        <div v-if="errors.category" class="input-error-message">{{ errors.category }}</div>
      </div>

      <Input v-model="formData.subcategory" label="Subcategory" placeholder="e.g., Web Development" />

      <div class="form-group">
        <label class="input-label">
          Difficulty Level
          <span class="required">*</span>
        </label>
        <select v-model="formData.difficulty_level" class="select" required>
          <option v-for="level in difficultyLevels" :key="level.value" :value="level.value">
            {{ level.label }}
          </option>
        </select>
      </div>

      <Input
        v-model.number="formData.price"
        type="number"
        label="Price"
        placeholder="0.00"
        required
        :error="errors.price"
        step="0.01"
        min="0"
      />

      <Input v-model="formData.currency" label="Currency" placeholder="THB" />

      <Input v-model="formData.thumbnail" label="Thumbnail URL" placeholder="https://..." />

      <div class="form-group full-width">
        <label class="input-label">Learning Objectives</label>
        <div class="objectives-input">
          <Input v-model="newObjective" placeholder="Add a learning objective" @keyup.enter.prevent="addObjective" />
          <Button type="button" @click="addObjective">Add</Button>
        </div>
        <ul v-if="formData.learning_objectives && formData.learning_objectives.length > 0" class="objectives-list">
          <li v-for="(objective, index) in formData.learning_objectives" :key="index" class="objective-item">
            <span>{{ objective }}</span>
            <Button type="button" variant="danger" size="sm" @click="removeObjective(index)">Remove</Button>
          </li>
        </ul>
      </div>
    </div>

    <div class="form-actions">
      <Button type="submit" :loading="loading">Save Course</Button>
    </div>
  </form>
</template>

<style scoped>
.course-form {
  max-width: 900px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.textarea {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
  font-family: inherit;
  transition: all 0.2s;
  resize: vertical;
}

.textarea:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.textarea.input-error {
  border-color: var(--error);
}

.select {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
  font-family: inherit;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
}

.select:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.char-count {
  font-size: 0.875rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

.objectives-input {
  display: flex;
  gap: 0.5rem;
}

.objectives-list {
  list-style: none;
  padding: 0;
  margin: 1rem 0 0 0;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.objective-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: #f9fafb;
  border-radius: 8px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding-top: 1rem;
  border-top: 1px solid var(--border-color);
}
</style>

