<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import analyticsService from '@/services/analyticsService'
import type { RevenueBreakdown, AnalyticsFilters } from '@/types/Analytics'

const loading = ref(false)
const error = ref<string | null>(null)
const revenueData = ref<RevenueBreakdown[]>([])
const groupBy = ref<'course' | 'day' | 'month'>('course')
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
    group_by: groupBy.value,
  }
})

const totalRevenue = computed(() => {
  return revenueData.value.reduce((sum, item) => sum + item.total_revenue, 0)
})

const totalAmount = computed(() => {
  return revenueData.value.reduce((sum, item) => sum + item.total_amount, 0)
})

const totalPlatformFees = computed(() => {
  return revenueData.value.reduce((sum, item) => sum + item.platform_fees, 0)
})

const loadRevenue = async () => {
  loading.value = true
  error.value = null

  try {
    revenueData.value = await analyticsService.getRevenueBreakdown(filters.value)
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load revenue data'
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
  loadRevenue()
})
</script>

<template>
  <div class="revenue-page">
    <div class="container">
      <div class="page-header">
        <h1>Revenue Dashboard</h1>
        <div class="controls">
          <div class="date-range-selector">
            <button
              v-for="range in ['7d', '30d', '90d', 'all']"
              :key="range"
              class="range-button"
              :class="{ active: dateRange === range }"
              @click="dateRange = range as any; loadRevenue()"
            >
              {{ range === '7d' ? '7 Days' : range === '30d' ? '30 Days' : range === '90d' ? '90 Days' : 'All Time' }}
            </button>
          </div>
          <div class="group-by-selector">
            <label>Group by:</label>
            <select v-model="groupBy" @change="loadRevenue()" class="select">
              <option value="course">Course</option>
              <option value="day">Day</option>
              <option value="month">Month</option>
            </select>
          </div>
        </div>
      </div>

      <div v-if="error" class="error-message">{{ error }}</div>
      <div v-if="loading" class="loading">Loading revenue data...</div>

      <div v-else class="revenue-content">
        <!-- Summary Cards -->
        <div class="summary-grid">
          <div class="summary-card">
            <div class="summary-label">Total Revenue (Gross)</div>
            <div class="summary-value">{{ formatCurrency(totalAmount) }}</div>
          </div>
          <div class="summary-card">
            <div class="summary-label">Platform Fees (30%)</div>
            <div class="summary-value fees">{{ formatCurrency(totalPlatformFees) }}</div>
          </div>
          <div class="summary-card highlight">
            <div class="summary-label">Your Revenue (Net)</div>
            <div class="summary-value revenue">{{ formatCurrency(totalRevenue) }}</div>
            <div class="summary-subtext">70% of gross revenue</div>
          </div>
        </div>

        <!-- Revenue Breakdown Table -->
        <div class="revenue-table-section">
          <h2 class="section-title">Revenue Breakdown</h2>
          <div class="table-container">
            <table class="revenue-table">
              <thead>
                <tr>
                  <th v-if="groupBy === 'course'">Course</th>
                  <th v-else-if="groupBy === 'day'">Date</th>
                  <th v-else>Month</th>
                  <th>Gross Revenue</th>
                  <th>Platform Fees</th>
                  <th>Your Revenue</th>
                  <th>Transactions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in revenueData" :key="index">
                  <td>
                    <span v-if="groupBy === 'course'">{{ item.course_title || 'Unknown' }}</span>
                    <span v-else-if="groupBy === 'day'">{{ new Date(item.date || '').toLocaleDateString() }}</span>
                    <span v-else>{{ item.month }}</span>
                  </td>
                  <td>{{ formatCurrency(item.total_amount) }}</td>
                  <td class="fees">{{ formatCurrency(item.platform_fees) }}</td>
                  <td class="revenue">{{ formatCurrency(item.total_revenue) }}</td>
                  <td>{{ formatNumber(item.transaction_count) }}</td>
                </tr>
                <tr v-if="revenueData.length === 0" class="empty-row">
                  <td :colspan="groupBy === 'course' ? 5 : 5">No revenue data available</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Revenue Chart -->
        <div v-if="revenueData.length > 0" class="chart-section">
          <h2 class="section-title">Revenue Visualization</h2>
          <div class="simple-chart">
            <div
              v-for="(item, index) in revenueData"
              :key="index"
              class="chart-bar revenue-bar"
              :style="{ height: `${(item.total_revenue / Math.max(...revenueData.map(i => i.total_revenue), 1)) * 100}%` }"
              :title="`${formatCurrency(item.total_revenue)}`"
            ></div>
          </div>
          <div class="chart-labels">
            <span
              v-for="(item, index) in revenueData.filter((_, i) => i % Math.ceil(revenueData.length / 8) === 0)"
              :key="index"
            >
              <span v-if="groupBy === 'course'">{{ item.course_title?.substring(0, 10) || 'Course' }}</span>
              <span v-else-if="groupBy === 'day'">{{ new Date(item.date || '').toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}</span>
              <span v-else>{{ item.month }}</span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.revenue-page {
  min-height: 100vh;
  padding: 2rem 0;
  background: var(--bg-color, #f9fafb);
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
  font-weight: 600;
  margin: 0 0 1.5rem 0;
}

.controls {
  display: flex;
  gap: 1.5rem;
  align-items: center;
  flex-wrap: wrap;
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

.group-by-selector {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.group-by-selector label {
  font-weight: 500;
}

.select {
  padding: 0.5rem 1rem;
  border: 2px solid var(--border-color, #e5e7eb);
  border-radius: 6px;
  font-size: 0.875rem;
  background: white;
  cursor: pointer;
}

.error-message {
  padding: 1rem;
  background: #fee2e2;
  color: #dc2626;
  border-radius: 8px;
  margin-bottom: 1.5rem;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: var(--text-secondary, #6b7280);
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.summary-card {
  padding: 1.5rem;
  background: white;
  border: 2px solid var(--border-color, #e5e7eb);
  border-radius: 8px;
}

.summary-card.highlight {
  border-color: var(--success-color, #22c55e);
  background: rgba(34, 197, 94, 0.05);
}

.summary-label {
  font-size: 0.875rem;
  color: var(--text-secondary, #6b7280);
  margin-bottom: 0.5rem;
}

.summary-value {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-color, #111827);
}

.summary-value.fees {
  color: #f59e0b;
}

.summary-value.revenue {
  color: var(--success-color, #22c55e);
}

.summary-subtext {
  font-size: 0.75rem;
  color: var(--text-secondary, #6b7280);
  margin-top: 0.25rem;
}

.revenue-table-section {
  background: white;
  border: 2px solid var(--border-color, #e5e7eb);
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 2rem;
}

.section-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0 0 1.5rem 0;
}

.table-container {
  overflow-x: auto;
}

.revenue-table {
  width: 100%;
  border-collapse: collapse;
}

.revenue-table th {
  text-align: left;
  padding: 0.75rem;
  border-bottom: 2px solid var(--border-color, #e5e7eb);
  font-weight: 600;
  color: var(--text-secondary, #6b7280);
}

.revenue-table td {
  padding: 0.75rem;
  border-bottom: 1px solid var(--border-color, #e5e7eb);
}

.revenue-table tbody tr:hover {
  background: var(--bg-secondary, #f9fafb);
}

.revenue-table td.fees {
  color: #f59e0b;
}

.revenue-table td.revenue {
  color: var(--success-color, #22c55e);
  font-weight: 600;
}

.empty-row {
  text-align: center;
  color: var(--text-secondary, #6b7280);
}

.chart-section {
  background: white;
  border: 2px solid var(--border-color, #e5e7eb);
  border-radius: 8px;
  padding: 1.5rem;
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
  background: var(--success-color, #22c55e);
  border-radius: 4px 4px 0 0;
  min-height: 4px;
  cursor: pointer;
  transition: opacity 0.2s;
}

.chart-bar:hover {
  opacity: 0.8;
}

.chart-labels {
  display: flex;
  justify-content: space-between;
  font-size: 0.75rem;
  color: var(--text-secondary, #6b7280);
}
</style>

