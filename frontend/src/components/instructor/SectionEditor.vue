<script setup lang="ts">
import { ref } from 'vue'
import Button from '@/components/common/Button.vue'
import Input from '@/components/common/Input.vue'
import type { Section, CreateSectionData } from '@/types/Course'

interface Props {
  section?: Section
  order?: number
}

const props = defineProps<Props>()

const emit = defineEmits<{
  save: [data: CreateSectionData]
  cancel: []
}>()

const formData = ref<CreateSectionData>({
  title: props.section?.title || '',
  description: props.section?.description || '',
  order: props.section?.order || props.order || 1,
})

const handleSave = () => {
  emit('save', { ...formData.value })
}

const handleCancel = () => {
  emit('cancel')
}
</script>

<template>
  <div class="section-editor">
    <Input v-model="formData.title" label="Section Title" placeholder="e.g., Introduction" required />
    <div class="form-group">
      <label class="input-label">Description</label>
      <textarea
        v-model="formData.description"
        class="textarea"
        placeholder="Brief overview of this section"
        rows="3"
      ></textarea>
    </div>
    <div class="editor-actions">
      <Button variant="outline" @click="handleCancel">Cancel</Button>
      <Button @click="handleSave">Save Section</Button>
    </div>
  </div>
</template>

<style scoped>
.section-editor {
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

.textarea {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
  font-family: inherit;
  resize: vertical;
}

.textarea:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.editor-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  margin-top: 1rem;
}
</style>

