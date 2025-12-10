<script setup lang="ts">
import { ref, onMounted } from 'vue'

const message = ref('Welcome to Course Marketplace')

onMounted(() => {
  console.log('Course Marketplace Platform Loaded')
})
</script>

<template>
  <div class="home">
    <header class="header">
      <h1>{{ message }}</h1>
      <p>Your Online Learning Platform</p>
    </header>
    <main class="main-content">
      <p>Platform is being set up...</p>
    </main>
  </div>
</template>

<style scoped>
.home {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 3rem 1.5rem;
  text-align: center;
}

.header h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.main-content {
  flex: 1;
  padding: 2rem 1.5rem;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}
</style>

