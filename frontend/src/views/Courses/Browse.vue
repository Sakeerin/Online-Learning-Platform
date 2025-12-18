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
const { courses, filters, pagination, browseCourses, updateFilters, isLoading } = useCourseDiscovery()

const showFilters = ref(false)
const currentPage = ref(1)

// Initialize filters from query params
onMounted(async () => {
  const queryFilters: any = {}
  if (route.query.category) queryFilters.category = route.query.category as string
  if (route.query.difficulty_level) queryFilters.difficulty_level = route.query.difficulty_level as string
  if (route.query.sort_by) queryFilters.sort_by = route.query.sort_by as string
  if (route.query.page) currentPage.value = parseInt(route.query.page as string)

  updateFilters(queryFilters)
  await loadCourses()
})

const loadCourses = async () => {
  await browseCourses({ ...filters.value, page: currentPage.value, per_page: 12 })
}

const handleFilterApply = async (newFilters: any) => {
  updateFilters(newFilters)
  currentPage.value = 1
  await loadCourses()
  
  // Update URL
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
  await loadCourses()
  
  router.push({
    query: {
      ...route.query,
      page,
    },
  })
}
</script>

<template>
  <div class="browse-page">
    <div class="container">
      <header class="page-header">
        <h1>Browse Courses</h1>
        <Button variant="outline" @click="showFilters = !showFilters">
          {{ showFilters ? 'Hide' : 'Show' }} Filters
        </Button>
      </header>

      <div class="browse-content">
        <aside v-if="showFilters" class="filters-sidebar">
          <CourseFilters :filters="filters" @apply="handleFilterApply" @clear="handleFilterApply({})" />
        </aside>

        <main class="courses-main">
          <CourseGrid :courses="courses" :loading="isLoading" />

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
.browse-page {
  min-height: 100vh;
  padding: 2rem 0;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2.5rem;
  margin: 0;
}

.browse-content {
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
  .browse-content {
    grid-template-columns: 1fr;
  }

  .filters-sidebar {
    position: static;
  }
}
</style>

