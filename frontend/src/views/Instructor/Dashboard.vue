<script setup lang="ts">
import { onMounted } from 'vue'
import { useAuth } from '@/composables/useAuth'
import Card from '@/components/common/Card.vue'

const { user } = useAuth()

onMounted(() => {
  // TODO: Fetch instructor courses and analytics
})
</script>

<template>
  <div class="instructor-dashboard">
    <div class="container">
      <header class="dashboard-header">
        <h1>Instructor Dashboard</h1>
        <p>Welcome back, {{ user?.name }}!</p>
      </header>

      <div class="dashboard-content">
        <Card title="My Courses" subtitle="Manage your courses">
          <p>Your courses will appear here once you create them.</p>
          <router-link to="/instructor/courses/create">
            <button>Create Your First Course</button>
          </router-link>
        </Card>

        <Card title="Analytics" subtitle="Track your performance">
          <p>Analytics dashboard coming soon.</p>
        </Card>
      </div>
    </div>
  </div>
</template>

<style scoped>
.instructor-dashboard {
  min-height: 100vh;
  padding: 2rem 0;
}

.dashboard-header {
  margin-bottom: 2rem;
}

.dashboard-header h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.dashboard-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}
</style>

