<script setup lang="ts">
import { computed } from 'vue'
import type { Section, Lesson } from '@/types/Course'

interface Props {
  sections?: Section[]
}

const props = defineProps<Props>()

const totalLessons = computed(() => {
  return props.sections?.reduce((total, section) => {
    return total + (section.lessons?.length || 0)
  }, 0) || 0
})

const totalDuration = computed(() => {
  return props.sections?.reduce((total, section) => {
    const sectionDuration = section.lessons?.reduce((secTotal, lesson) => {
      return secTotal + (lesson.duration || 0)
    }, 0) || 0
    return total + sectionDuration
  }, 0) || 0
})

const formatDuration = (seconds: number): string => {
  const hours = Math.floor(seconds / 3600)
  const minutes = Math.floor((seconds % 3600) / 60)
  
  if (hours > 0) {
    return `${hours}h ${minutes}m`
  }
  return `${minutes}m`
}
</script>

<template>
  <div class="course-curriculum">
    <div class="curriculum-header">
      <h3>Course Curriculum</h3>
      <div class="curriculum-stats">
        <span>{{ sections?.length || 0 }} sections</span>
        <span>{{ totalLessons }} lessons</span>
        <span v-if="totalDuration > 0">{{ formatDuration(totalDuration) }} total</span>
      </div>
    </div>

    <div v-if="sections && sections.length > 0" class="sections-list">
      <div v-for="(section, sectionIndex) in sections" :key="section.id" class="section-item">
        <div class="section-header">
          <div class="section-info">
            <span class="section-number">{{ sectionIndex + 1 }}</span>
            <div>
              <h4 class="section-title">{{ section.title }}</h4>
              <p v-if="section.description" class="section-description">{{ section.description }}</p>
            </div>
          </div>
          <span class="section-lesson-count">{{ section.lessons?.length || 0 }} lessons</span>
        </div>

        <div v-if="section.lessons && section.lessons.length > 0" class="lessons-list">
          <div
            v-for="(lesson, lessonIndex) in section.lessons"
            :key="lesson.id"
            class="lesson-item"
            :class="{ 'lesson-preview': lesson.is_preview }"
          >
            <div class="lesson-info">
              <span class="lesson-number">{{ sectionIndex + 1 }}.{{ lessonIndex + 1 }}</span>
              <div class="lesson-details">
                <span class="lesson-type">{{ lesson.type }}</span>
                <span class="lesson-title">{{ lesson.title }}</span>
              </div>
            </div>
            <div class="lesson-meta">
              <span v-if="lesson.duration" class="lesson-duration">{{ formatDuration(lesson.duration) }}</span>
              <span v-if="lesson.is_preview" class="preview-badge">Preview</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="empty-curriculum">
      <p>No curriculum available</p>
    </div>
  </div>
</template>

<style scoped>
.course-curriculum {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.curriculum-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--border-color);
}

.curriculum-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
}

.curriculum-stats {
  display: flex;
  gap: 1rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.sections-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.section-item {
  border: 1px solid var(--border-color);
  border-radius: 8px;
  overflow: hidden;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 1rem;
  background: #f9fafb;
  border-bottom: 1px solid var(--border-color);
}

.section-info {
  display: flex;
  gap: 1rem;
  flex: 1;
}

.section-number {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  background: var(--primary-color);
  color: white;
  border-radius: 50%;
  font-weight: 600;
  flex-shrink: 0;
}

.section-title {
  margin: 0 0 0.25rem 0;
  font-size: 1rem;
  font-weight: 600;
}

.section-description {
  margin: 0;
  font-size: 0.875rem;
  color: #6b7280;
}

.section-lesson-count {
  font-size: 0.875rem;
  color: #6b7280;
  white-space: nowrap;
}

.lessons-list {
  padding: 0.5rem;
}

.lesson-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  border-radius: 6px;
  transition: background 0.2s;
}

.lesson-item:hover {
  background: #f9fafb;
}

.lesson-item.lesson-preview {
  background: #f0f9ff;
}

.lesson-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex: 1;
}

.lesson-number {
  font-size: 0.875rem;
  color: #6b7280;
  min-width: 2rem;
}

.lesson-details {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex: 1;
}

.lesson-type {
  padding: 0.25rem 0.5rem;
  background: #e0e7ff;
  color: #6366f1;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.lesson-title {
  font-size: 0.875rem;
  color: #374151;
}

.lesson-meta {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.lesson-duration {
  font-size: 0.875rem;
  color: #6b7280;
}

.preview-badge {
  padding: 0.25rem 0.5rem;
  background: #3b82f6;
  color: white;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
}

.empty-curriculum {
  text-align: center;
  padding: 3rem 2rem;
  color: #6b7280;
}
</style>

