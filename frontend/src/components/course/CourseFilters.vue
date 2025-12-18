<script setup lang="ts">
import { ref, watch } from 'vue'
import Input from '@/components/common/Input.vue'
import Button from '@/components/common/Button.vue'
import type { CourseFilters } from '@/composables/useCourseDiscovery'

interface Props {
  filters: CourseFilters
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'update:filters': [filters: CourseFilters]
  'apply': [filters: CourseFilters]
  'clear': []
}>()

const localFilters = ref<CourseFilters>({ ...props.filters })

const categories = ['Development', 'Design', 'Business', 'Marketing', 'Photography', 'Music', 'Health', 'Lifestyle', 'Other']
const difficultyLevels = [
  { value: 'beginner', label: 'Beginner' },
  { value: 'intermediate', label: 'Intermediate' },
  { value: 'advanced', label: 'Advanced' },
]

const sortOptions = [
  { value: 'relevance', label: 'Relevance' },
  { value: 'price_asc', label: 'Price: Low to High' },
  { value: 'price_desc', label: 'Price: High to Low' },
  { value: 'rating', label: 'Highest Rated' },
  { value: 'enrollments', label: 'Most Popular' },
  { value: 'newest', label: 'Newest' },
]

watch(() => props.filters, (newFilters) => {
  localFilters.value = { ...newFilters }
}, { deep: true })

const applyFilters = () => {
  emit('apply', { ...localFilters.value })
}

const clearFilters = () => {
  localFilters.value = {}
  emit('clear')
}
</script>

<template>
  <div class="course-filters">
    <div class="filters-header">
      <h3>Filters</h3>
      <Button variant="outline" size="sm" @click="clearFilters">Clear All</Button>
    </div>

    <div class="filters-content">
      <div class="filter-group">
        <label class="filter-label">Category</label>
        <select v-model="localFilters.category" class="filter-select">
          <option value="">All Categories</option>
          <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
        </select>
      </div>

      <div class="filter-group">
        <label class="filter-label">Difficulty Level</label>
        <select v-model="localFilters.difficulty_level" class="filter-select">
          <option value="">All Levels</option>
          <option v-for="level in difficultyLevels" :key="level.value" :value="level.value">
            {{ level.label }}
          </option>
        </select>
      </div>

      <div class="filter-group">
        <label class="filter-label">Price Range</label>
        <div class="price-range">
          <Input
            v-model.number="localFilters.min_price"
            type="number"
            placeholder="Min"
            min="0"
            class="price-input"
          />
          <span>to</span>
          <Input
            v-model.number="localFilters.max_price"
            type="number"
            placeholder="Max"
            min="0"
            class="price-input"
          />
        </div>
        <label class="checkbox-label">
          <input v-model="localFilters.free_only" type="checkbox" class="checkbox" />
          <span>Free courses only</span>
        </label>
      </div>

      <div class="filter-group">
        <label class="filter-label">Minimum Rating</label>
        <select v-model.number="localFilters.min_rating" class="filter-select">
          <option :value="undefined">Any Rating</option>
          <option :value="4">4+ Stars</option>
          <option :value="3">3+ Stars</option>
          <option :value="2">2+ Stars</option>
        </select>
      </div>

      <div class="filter-group">
        <label class="filter-label">Sort By</label>
        <select v-model="localFilters.sort_by" class="filter-select">
          <option v-for="option in sortOptions" :key="option.value" :value="option.value">
            {{ option.label }}
          </option>
        </select>
      </div>

      <Button @click="applyFilters" class="apply-button">Apply Filters</Button>
    </div>
  </div>
</template>

<style scoped>
.course-filters {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--border-color);
}

.filters-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
}

.filters-content {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-label {
  font-weight: 500;
  font-size: 0.875rem;
  color: #374151;
}

.filter-select {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
  font-family: inherit;
  background: white;
  cursor: pointer;
}

.filter-select:focus {
  outline: none;
  border-color: var(--primary-color);
}

.price-range {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.price-input {
  flex: 1;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  margin-top: 0.5rem;
}

.checkbox {
  width: 1.25rem;
  height: 1.25rem;
  cursor: pointer;
}

.apply-button {
  width: 100%;
  margin-top: 0.5rem;
}
</style>

