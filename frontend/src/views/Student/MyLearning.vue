<script setup lang="ts">
import { onMounted } from 'vue'
import { useAuth } from '@/composables/useAuth'
import Card from '@/components/common/Card.vue'

const { user } = useAuth()

onMounted(() => {
  // TODO: Fetch enrolled courses
})
</script>

<template>
  <div class="my-learning">
    <div class="container">
      <header class="page-header">
        <h1>My Learning</h1>
        <p>Continue your learning journey</p>
      </header>

      <div class="learning-content">
        <Card title="Enrolled Courses" subtitle="Your active courses">
          <p>Your enrolled courses will appear here.</p>
          <router-link to="/courses">
            <button>Browse Courses</button>
          </router-link>
        </Card>

        <Card title="Progress" subtitle="Track your learning">
          <p>Your learning progress will be displayed here.</p>
        </Card>
      </div>
    </div>
  </div>
</template>

<style scoped>
.my-learning {
  min-height: 100vh;
  padding: 2rem 0;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.learning-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}
</style>

