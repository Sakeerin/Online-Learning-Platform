<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import Button from '@/components/common/Button.vue'
import QuestionPost from './QuestionPost.vue'
import discussionService from '@/services/discussionService'
import type { Discussion, DiscussionFilters } from '@/types/Discussion'

interface Props {
  courseId: string
  lessonId?: string
}

const props = defineProps<Props>()

const discussions = ref<Discussion[]>([])
const loading = ref(false)
const error = ref<string | null>(null)
const showPostForm = ref(false)
const currentPage = ref(1)
const totalPages = ref(1)
const total = ref(0)

// Filters
const searchQuery = ref('')
const filterAnswered = ref<boolean | undefined>(undefined)
const sortBy = ref<'created_at' | 'upvotes'>('created_at')
const sortOrder = ref<'asc' | 'desc'>('desc')

const filters = computed<DiscussionFilters>(() => ({
  lesson_id: props.lessonId,
  is_answered: filterAnswered.value,
  search: searchQuery.value || undefined,
  sort_by: sortBy.value,
  sort_order: sortOrder.value,
  per_page: 10,
}))

const loadDiscussions = async (page = 1) => {
  loading.value = true
  error.value = null

  try {
    const response = await discussionService.getDiscussions(props.courseId, {
      ...filters.value,
      per_page: 10,
    })
    discussions.value = response.data
    currentPage.value = response.meta.current_page
    totalPages.value = response.meta.last_page
    total.value = response.meta.total
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load discussions'
  } finally {
    loading.value = false
  }
}

const handlePostCreated = () => {
  showPostForm.value = false
  loadDiscussions(1)
}

const handleUpvote = async (discussion: Discussion) => {
  try {
    const updated = await discussionService.upvoteDiscussion(props.courseId, discussion.id)
    const index = discussions.value.findIndex(d => d.id === discussion.id)
    if (index !== -1) {
      discussions.value[index] = updated
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to upvote discussion'
  }
}

const toggleFilter = (value: boolean | undefined) => {
  filterAnswered.value = filterAnswered.value === value ? undefined : value
  loadDiscussions(1)
}

watch([searchQuery, sortBy, sortOrder], () => {
  loadDiscussions(1)
}, { debounce: 300 })

onMounted(() => {
  loadDiscussions()
})
</script>

<template>
  <div class="discussion-forum">
    <div class="forum-header">
      <h2 class="forum-title">Course Q&A</h2>
      <Button @click="showPostForm = !showPostForm">
        {{ showPostForm ? 'Cancel' : 'Ask a Question' }}
      </Button>
    </div>

    <QuestionPost
      v-if="showPostForm"
      :course-id="courseId"
      :lesson-id="lessonId"
      @created="handlePostCreated"
      @cancel="showPostForm = false"
    />

    <div class="filters-section">
      <div class="search-box">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search questions..."
          class="search-input"
        />
      </div>

      <div class="filter-buttons">
        <Button
          :variant="filterAnswered === undefined ? 'primary' : 'outline'"
          size="sm"
          @click="toggleFilter(undefined)"
        >
          All
        </Button>
        <Button
          :variant="filterAnswered === false ? 'primary' : 'outline'"
          size="sm"
          @click="toggleFilter(false)"
        >
          Unanswered
        </Button>
        <Button
          :variant="filterAnswered === true ? 'primary' : 'outline'"
          size="sm"
          @click="toggleFilter(true)"
        >
          Answered
        </Button>
      </div>

      <div class="sort-controls">
        <label class="sort-label">Sort by:</label>
        <select v-model="sortBy" class="sort-select">
          <option value="created_at">Newest</option>
          <option value="upvotes">Most Upvoted</option>
        </select>
        <select v-model="sortOrder" class="sort-select">
          <option value="desc">Descending</option>
          <option value="asc">Ascending</option>
        </select>
      </div>
    </div>

    <div v-if="error" class="error-message">{{ error }}</div>

    <div v-if="loading" class="loading">Loading discussions...</div>

    <div v-else-if="discussions.length === 0" class="empty-state">
      <p>No questions yet. Be the first to ask!</p>
    </div>

    <div v-else class="discussions-list">
      <div
        v-for="discussion in discussions"
        :key="discussion.id"
        class="discussion-card"
        :class="{ answered: discussion.is_answered }"
      >
        <div class="discussion-header">
          <div class="discussion-author">
            <img
              v-if="discussion.user?.profile_photo"
              :src="discussion.user.profile_photo"
              :alt="discussion.user.name"
              class="author-avatar"
            />
            <div v-else class="author-avatar placeholder">{{ discussion.user?.name?.[0] || 'U' }}</div>
            <div class="author-info">
              <span class="author-name">{{ discussion.user?.name || 'Anonymous' }}</span>
              <span class="discussion-date">{{ new Date(discussion.created_at).toLocaleDateString() }}</span>
            </div>
          </div>
          <div v-if="discussion.is_answered" class="answered-badge">Answered</div>
        </div>

        <div class="discussion-content">
          <h3 class="discussion-question">{{ discussion.question }}</h3>
          <div v-if="discussion.lesson" class="lesson-context">
            Related to: {{ discussion.lesson.title }}
          </div>
        </div>

        <div class="discussion-footer">
          <div class="discussion-stats">
            <span class="replies-count">{{ discussion.replies_count || 0 }} replies</span>
          </div>
          <div class="discussion-actions">
            <button
              class="upvote-button"
              :class="{ upvoted: false }"
              @click="handleUpvote(discussion)"
            >
              <span class="upvote-icon">▲</span>
              <span>{{ discussion.upvotes }}</span>
            </button>
            <router-link
              :to="`/courses/${courseId}/discussions/${discussion.id}`"
              class="view-link"
            >
              View Discussion
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <div v-if="totalPages > 1" class="pagination">
      <Button
        variant="outline"
        :disabled="currentPage === 1"
        @click="loadDiscussions(currentPage - 1)"
      >
        Previous
      </Button>
      <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
      <Button
        variant="outline"
        :disabled="currentPage === totalPages"
        @click="loadDiscussions(currentPage + 1)"
      >
        Next
      </Button>
    </div>
  </div>
</template>

<style scoped>
.discussion-forum {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.forum-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.forum-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin: 0;
}

.filters-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1rem;
  background: var(--bg-secondary, #f9fafb);
  border-radius: 8px;
}

.search-box {
  width: 100%;
}

.search-input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--border-color, #e5e7eb);
  border-radius: 8px;
  font-size: 1rem;
}

.search-input:focus {
  outline: none;
  border-color: var(--primary-color, #667eea);
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filter-buttons {
  display: flex;
  gap: 0.5rem;
}

.sort-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.sort-label {
  font-weight: 500;
}

.sort-select {
  padding: 0.5rem;
  border: 2px solid var(--border-color, #e5e7eb);
  border-radius: 6px;
  font-size: 0.875rem;
}

.error-message {
  padding: 1rem;
  background: #fee2e2;
  color: #dc2626;
  border-radius: 8px;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: var(--text-secondary, #6b7280);
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: var(--text-secondary, #6b7280);
}

.discussions-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.discussion-card {
  padding: 1.5rem;
  background: white;
  border: 2px solid var(--border-color, #e5e7eb);
  border-radius: 8px;
  transition: all 0.2s;
}

.discussion-card:hover {
  border-color: var(--primary-color, #667eea);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.discussion-card.answered {
  border-color: var(--success-color, #22c55e);
  background: rgba(34, 197, 94, 0.02);
}

.discussion-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.discussion-author {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.author-avatar {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 50%;
  object-fit: cover;
}

.author-avatar.placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--primary-color, #667eea);
  color: white;
  font-weight: 600;
}

.author-info {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.author-name {
  font-weight: 600;
  color: var(--text-color, #111827);
}

.discussion-date {
  font-size: 0.875rem;
  color: var(--text-secondary, #6b7280);
}

.answered-badge {
  padding: 0.25rem 0.75rem;
  background: var(--success-color, #22c55e);
  color: white;
  border-radius: 4px;
  font-size: 0.875rem;
  font-weight: 600;
}

.discussion-content {
  margin-bottom: 1rem;
}

.discussion-question {
  font-size: 1.125rem;
  font-weight: 600;
  margin: 0 0 0.5rem 0;
  color: var(--text-color, #111827);
}

.lesson-context {
  font-size: 0.875rem;
  color: var(--text-secondary, #6b7280);
  font-style: italic;
}

.discussion-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1rem;
  border-top: 1px solid var(--border-color, #e5e7eb);
}

.discussion-stats {
  font-size: 0.875rem;
  color: var(--text-secondary, #6b7280);
}

.discussion-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.upvote-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  background: white;
  border: 2px solid var(--border-color, #e5e7eb);
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.upvote-button:hover {
  border-color: var(--primary-color, #667eea);
  background: rgba(102, 126, 234, 0.05);
}

.upvote-button.upvoted {
  border-color: var(--primary-color, #667eea);
  background: rgba(102, 126, 234, 0.1);
  color: var(--primary-color, #667eea);
}

.upvote-icon {
  font-size: 1rem;
}

.view-link {
  padding: 0.5rem 1rem;
  background: var(--primary-color, #667eea);
  color: white;
  text-decoration: none;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s;
}

.view-link:hover {
  background: #5568d3;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
}

.page-info {
  font-size: 0.875rem;
  color: var(--text-secondary, #6b7280);
}
</style>

