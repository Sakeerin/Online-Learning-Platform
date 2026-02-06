<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import analyticsService from '@/services/analyticsService'
import type { InstructorAnalytics, AnalyticsFilters } from '@/types/Analytics'

interface Props {
  courseId?: string
}

const props = defineProps<Props>()

const loading = ref(false)
const error = ref<string | null>(null)
const analytics = ref<InstructorAnalytics | null>(null)

const dateRange = ref<'7d' | '30d' | '90d' | 'all'>('30d')

const filters = computed<AnalyticsFilters>(() => {
  const now = new Date()
  let dateFrom: Date | undefined

  switch (dateRange.value) {
    case '7d':
      dateFrom = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000)
      break
    case '30d':
      dateFrom = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000)
      break
    case '90d':
      dateFrom = new Date(now.getTime() - 90 * 24 * 60 * 60 * 1000)
      break
    default:
      dateFrom = undefined
  }

  return {
    date_from: dateFrom?.toISOString().split('T')[0],
    date_to: now.toISOString().split('T')[0],
  }
})

const loadAnalytics = async () => {
  loading.value = true
  error.value = null

  try {
    analytics.value = await analyticsService.getInstructorAnalytics(filters.value)
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load analytics'
  } finally {
    loading.value = false
  }
}

const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount)
}

const formatNumber = (num: number): string => {
  return new Intl.NumberFormat('en-US').format(num)
}

onMounted(() => {
  loadAnalytics()
})
</script>

<template>
  <div class="analytics-dashboard">
    <div class="dashboard-header">
      <h2 class="dashboard-title">Analytics Dashboard</h2>
      <div class="date-range-selector">
        <button
          v-for="range in ['7d', '30d', '90d', 'all']"
          :key="range"
          class="range-button"
          :class="{ active: dateRange === range }"
          @click="dateRange = range as any; loadAnalytics()"
        >
          {{ range === '7d' ? '7 Days' : range === '30d' ? '30 Days' : range === '90d' ? '90 Days' : 'All Time' }}
        </button>
      </div>
    </div>

    <div v-if="error" class="error-message">{{ error }}</div>
    <div v-if="loading" class="loading">Loading analytics...</div>

    <div v-else-if="analytics" class="dashboard-content">
      <!-- Key Metrics -->
      <div class="metrics-grid">
        <div class="metric-card">
          <div class="metric-label">Total Courses</div>
          <div class="metric-value">{{ formatNumber(analytics.total_courses) }}</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Total Enrollments</div>
          <div class="metric-value">{{ formatNumber(analytics.total_enrollments) }}</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Total Revenue</div>
          <div class="metric-value">{{ formatCurrency(analytics.total_revenue) }}</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Net Revenue</div>
          <div class="metric-value revenue">{{ formatCurrency(analytics.net_revenue) }}</div>
          <div class="metric-subtext">After platform fees ({{ formatCurrency(analytics.total_platform_fees) }})</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Completion Rate</div>
          <div class="metric-value">{{ analytics.completion_rate.toFixed(1) }}%</div>
        </div>
      </div>

      <!-- Enrollment Trends Chart -->
      <div class="chart-section">
        <h3 class="chart-title">Enrollment Trends</h3>
        <div class="simple-chart">
          <div
            v-for="(point, index) in analytics.enrollment_trends"
            :key="index"
            class="chart-bar"
            :style="{ height: `${(point.count || 0) / Math.max(...analytics.enrollment_trends.map(p => p.count || 0)) * 100}%` }"
            :title="`${new Date(point.date).toLocaleDateString()}: ${point.count} enrollments`"
          ></div>
        </div>
        <div class="chart-labels">
          <span v-for="(point, index) in analytics.enrollment_trends.filter((_, i) => i % Math.ceil(analytics.enrollment_trends.length / 5) === 0)" :key="index">
            {{ new Date(point.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}
          </span>
        </div>
      </div>

      <!-- Revenue Trends Chart -->
      <div class="chart-section">
        <h3 class="chart-title">Revenue Trends</h3>
        <div class="simple-chart">
          <div
            v-for="(point, index) in analytics.revenue_trends"
            :key="index"
            class="chart-bar revenue-bar"
            :style="{ height: `${(point.revenue || 0) / Math.max(...analytics.revenue_trends.map(p => p.revenue || 0), 1) * 100}%` }"
            :title="`${new Date(point.date).toLocaleDateString()}: ${formatCurrency(point.revenue || 0)}`"
          ></div>
        </div>
        <div class="chart-labels">
          <span v-for="(point, index) in analytics.revenue_trends.filter((_, i) => i % Math.ceil(analytics.revenue_trends.length / 5) === 0)" :key="index">
            {{ new Date(point.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}
          </span>
        </div>
      </div>

      <!-- Top Courses -->
      <div class="top-courses-section">
        <h3 class="section-title">Top Performing Courses</h3>
        <div class="courses-list">
          <div
            v-for="(course, index) in analytics.top_courses"
            :key="course.course_id"
            class="course-item"
          >
            <div class="course-rank">#{{ index + 1 }}</div>
            <div class="course-info">
              <div class="course-title">{{ course.course_title }}</div>
              <div class="course-stats">
                <span>{{ formatNumber(course.enrollments) }} enrollments</span>
                <span>{{ formatCurrency(course.revenue) }} revenue</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.analytics-dashboard {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.dashboard-title {
  font-size: 1.75rem;
  font-weight: 600;
  margin: 0;
}

.date-range-selector {
  display: flex;
  gap: 0.5rem;
}

.range-button {
  padding: 0.5rem 1rem;
  border: 2px solid var(--border-color, #e5e7eb);
  background: white;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.875rem;
  transition: all 0.2s;
}

.range-button:hover {
  border-color: var(--primary-color, #667eea);
}

.range-button.active {
  background: var(--primary-color, #667eea);
  color: white;
  border-color: var(--primary-color, #667eea);
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

.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
}

.metric-card {
  padding: 1.5rem;
  background: white;
  border: 2px solid var(--border-color, #e5e7eb);
  border-radius: 8px;
}

.metric-label {
  font-size: 0.875rem;
  color: var(--text-secondary, #6b7280);
  margin-bottom: 0.5rem;
}

.metric-value {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-color, #111827);
}

.metric-value.revenue {
  color: var(--success-color, #22c55e);
}

.metric-subtext {
  font-size: 0.75rem;
  color: var(--text-secondary, #6b7280);
  margin-top: 0.25rem;
}

.chart-section {
  padding: 1.5rem;
  background: white;
  border: 2px solid var(--border-color, #e5e7eb);
  border-radius: 8px;
}

.chart-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0 0 1.5rem 0;
}

.simple-chart {
  display: flex;
  align-items: flex-end;
  gap: 0.5rem;
  height: 200px;
  margin-bottom: 1rem;
}

.chart-bar {
  flex: 1;
  background: var(--primary-color, #667eea);
  border-radius: 4px 4px 0 0;
  min-height: 4px;
  cursor: pointer;
  transition: opacity 0.2s;
}

.chart-bar:hover {
  opacity: 0.8;
}

.chart-bar.revenue-bar {
  background: var(--success-color, #22c55e);
}

.chart-labels {
  display: flex;
  justify-content: space-between;
  font-size: 0.75rem;
  color: var(--text-secondary, #6b7280);
}

.top-courses-section {
  padding: 1.5rem;
  background: white;
  border: 2px solid var(--border-color, #e5e7eb);
  border-radius: 8px;
}

.section-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0 0 1.5rem 0;
}

.courses-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.course-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: var(--bg-secondary, #f9fafb);
  border-radius: 6px;
}

.course-rank {
  width: 2.5rem;
  height: 2.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--primary-color, #667eea);
  color: white;
  border-radius: 50%;
  font-weight: 700;
}

.course-info {
  flex: 1;
}

.course-title {
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.course-stats {
  display: flex;
  gap: 1rem;
  font-size: 0.875rem;
  color: var(--text-secondary, #6b7280);
}
</style>

