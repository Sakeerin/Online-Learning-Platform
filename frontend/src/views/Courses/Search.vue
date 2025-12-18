<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCourseDiscovery } from '@/composables/useCourseDiscovery'
import CourseGrid from '@/components/course/CourseGrid.vue'
import CourseFilters from '@/components/course/CourseFilters.vue'
import Input from '@/components/common/Input.vue'
import Button from '@/components/common/Button.vue'

const route = useRoute()
const router = useRouter()
const { courses, searchQuery, filters, pagination, searchCourses, updateFilters, isSearching, isLoading } = useCourseDiscovery()

const showFilters = ref(false)
const searchInput = ref('')
const currentPage = ref(1)

// T119: Debounced search is handled in useCourseDiscovery composable

onMounted(() => {
  if (route.query.q) {
    searchInput.value = route.query.q as string
    searchQuery.value = route.query.q as string
  }
  
  const queryFilters: any = {}
  if (route.query.category) queryFilters.category = route.query.category as string
  if (route.query.difficulty_level) queryFilters.difficulty_level = route.query.difficulty_level as string
  if (route.query.page) currentPage.value = parseInt(route.query.page as string)

  updateFilters(queryFilters)
  
  if (searchInput.value) {
    searchCourses(searchInput.value, { ...filters.value, page: currentPage.value })
  }
})

const handleSearch = () => {
  if (searchInput.value.trim().length < 2) return
  
  searchQuery.value = searchInput.value
  currentPage.value = 1
  
  router.push({
    query: {
      q: searchInput.value,
      ...filters.value,
      page: 1,
    },
  })
  
  searchCourses(searchInput.value, { ...filters.value, page: 1 })
}

const handleFilterApply = async (newFilters: any) => {
  updateFilters(newFilters)
  currentPage.value = 1
  
  if (searchQuery.value) {
    await searchCourses(searchQuery.value, { ...newFilters, page: 1 })
  }
  
  router.push({
    query: {
      ...route.query,
      ...newFilters,
      page: 1,
    },
  })
}

const handlePageChange = async (page: number) => {
  currentPage.value = page
  
  if (searchQuery.value) {
    await searchCourses(searchQuery.value, { ...filters.value, page })
  }
  
  router.push({
    query: {
      ...route.query,
      page,
    },
  })
}
</script>

<template>
  <div class="search-page">
    <div class="container">
      <header class="page-header">
        <h1>Search Courses</h1>
      </header>

      <div class="search-bar">
        <Input
          v-model="searchInput"
          placeholder="Search for courses..."
          @keyup.enter="handleSearch"
          class="search-input"
        />
        <Button @click="handleSearch" :loading="isSearching">Search</Button>
      </div>

      <div v-if="searchQuery" class="search-results-header">
        <p>
          Found {{ pagination?.total || 0 }} results for "<strong>{{ searchQuery }}</strong>"
        </p>
      </div>

      <div class="search-content">
        <aside v-if="showFilters" class="filters-sidebar">
          <CourseFilters :filters="filters" @apply="handleFilterApply" @clear="handleFilterApply({})" />
        </aside>

        <main class="courses-main">
          <CourseGrid :courses="courses" :loading="isLoading || isSearching" />

          <!-- T121: Pagination -->
          <div v-if="pagination && pagination.last_page > 1" class="pagination">
            <Button
              variant="outline"
              :disabled="pagination.current_page === 1"
              @click="handlePageChange(pagination.current_page - 1)"
            >
              Previous
            </Button>
            <span class="page-info">
              Page {{ pagination.current_page }} of {{ pagination.last_page }}
            </span>
            <Button
              variant="outline"
              :disabled="pagination.current_page === pagination.last_page"
              @click="handlePageChange(pagination.current_page + 1)"
            >
              Next
            </Button>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<style scoped>
.search-page {
  min-height: 100vh;
  padding: 2rem 0;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2.5rem;
  margin: 0;
}

.search-bar {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.search-input {
  flex: 1;
}

.search-results-header {
  margin-bottom: 1.5rem;
  color: #6b7280;
}

.search-content {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 2rem;
}

.filters-sidebar {
  position: sticky;
  top: 2rem;
  height: fit-content;
}

.courses-main {
  min-height: 400px;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 3rem;
  padding-top: 2rem;
  border-top: 1px solid var(--border-color);
}

.page-info {
  font-weight: 500;
  color: #6b7280;
}

@media (max-width: 1024px) {
  .search-content {
    grid-template-columns: 1fr;
  }

  .filters-sidebar {
    position: static;
  }
}
</style>

