<template>
  <div class="certificates-page">
    <div class="container">
      <header class="page-header">
        <h1>My Certificates</h1>
        <p>View and download your course completion certificates</p>
      </header>

      <div v-if="isLoading" class="loading">Loading certificates...</div>
      <div v-else-if="certificates.length === 0" class="empty-state">
        <div class="empty-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
          </svg>
        </div>
        <h2>No certificates yet</h2>
        <p>Complete courses to earn certificates</p>
        <Button @click="$router.push({ name: 'my-learning' })">View My Learning</Button>
      </div>
      <div v-else class="certificates-grid">
        <Certificate
          v-for="certificate in certificates"
          :key="certificate.id"
          :certificate="certificate"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import Certificate from '@/components/student/Certificate.vue'
import Button from '@/components/common/Button.vue'
import certificateService from '@/services/certificateService'
import type { Certificate as CertificateType } from '@/types/Certificate'

const certificates = ref<CertificateType[]>([])
const isLoading = ref(true)

onMounted(async () => {
  try {
    certificates.value = await certificateService.getCertificates()
  } catch (error) {
    console.error('Failed to load certificates:', error)
  } finally {
    isLoading.value = false
  }
})
</script>

<style scoped>
.certificates-page {
  min-height: 100vh;
  padding: 2rem 0;
  background: #f9fafb;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
  color: #1f2937;
}

.page-header p {
  color: #6b7280;
  font-size: 1.125rem;
}

.loading,
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
}

.empty-state {
  background: white;
  border-radius: 8px;
  padding: 4rem 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.empty-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 1.5rem;
  color: #9ca3af;
}

.empty-icon svg {
  width: 100%;
  height: 100%;
}

.empty-state h2 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
  color: #1f2937;
}

.empty-state p {
  color: #6b7280;
  margin-bottom: 1.5rem;
}

.certificates-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 2rem;
}
</style>

