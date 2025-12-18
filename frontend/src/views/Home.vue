<script setup lang="ts">
import { onMounted } from 'vue'
import { useCourseDiscovery } from '@/composables/useCourseDiscovery'
import CourseGrid from '@/components/course/CourseGrid.vue'
import Button from '@/components/common/Button.vue'

const { featuredCourses, fetchFeaturedCourses, isLoading } = useCourseDiscovery()

onMounted(async () => {
  await fetchFeaturedCourses()
})
</script>

<template>
  <div class="home">
    <header class="hero">
      <div class="hero-content">
        <h1>Learn Anything, Anytime</h1>
        <p>Discover thousands of courses from expert instructors</p>
        <div class="hero-actions">
          <Button size="lg" @click="$router.push({ name: 'browse-courses' })">
            Browse Courses
          </Button>
        </div>
      </div>
    </header>

    <main class="main-content">
      <section class="featured-section">
        <div class="section-header">
          <h2>Featured Courses</h2>
          <Button variant="outline" @click="$router.push({ name: 'browse-courses' })">
            View All
          </Button>
        </div>
        <CourseGrid :courses="featuredCourses" :loading="isLoading" />
      </section>
    </main>
  </div>
</template>

<style scoped>
.home {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.hero {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 4rem 1.5rem;
  text-align: center;
}

.hero-content {
  max-width: 800px;
  margin: 0 auto;
}

.hero-content h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
  font-weight: 700;
}

.hero-content p {
  font-size: 1.25rem;
  margin-bottom: 2rem;
  opacity: 0.9;
}

.hero-actions {
  display: flex;
  justify-content: center;
  gap: 1rem;
}

.main-content {
  flex: 1;
  padding: 3rem 1.5rem;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

.featured-section {
  margin-bottom: 3rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.section-header h2 {
  font-size: 2rem;
  font-weight: 600;
  margin: 0;
}
</style>
