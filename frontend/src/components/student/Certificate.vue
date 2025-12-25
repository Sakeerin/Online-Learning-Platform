<template>
  <div class="certificate-card">
    <div class="certificate-header">
      <div class="certificate-icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
        </svg>
      </div>
      <div class="certificate-info">
        <h3 class="course-title">{{ certificate.course?.title || 'Course Certificate' }}</h3>
        <p class="issued-date">Issued on {{ formatDate(certificate.issued_at) }}</p>
      </div>
    </div>
    
    <div class="certificate-actions">
      <Button
        v-if="certificate.certificate_url"
        @click="handleDownload"
        :disabled="isDownloading"
        variant="primary"
      >
        <svg v-if="!isDownloading" xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        {{ isDownloading ? 'Downloading...' : 'Download PDF' }}
      </Button>
      <Button
        v-else
        disabled
        variant="secondary"
      >
        Generating...
      </Button>
      <Button
        @click="handleVerify"
        variant="outline"
      >
        Verify Certificate
      </Button>
    </div>

    <div v-if="showVerification" class="verification-section">
      <div class="verification-code">
        <label>Verification Code:</label>
        <code>{{ certificate.verification_code }}</code>
        <Button
          @click="copyVerificationCode"
          variant="ghost"
          size="sm"
        >
          Copy
        </Button>
      </div>
      <p class="verification-note">
        Share this code to verify your certificate authenticity
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import Button from '@/components/common/Button.vue'
import type { Certificate } from '@/types/Certificate'
import certificateService from '@/services/certificateService'

interface Props {
  certificate: Certificate
}

const props = defineProps<Props>()

const isDownloading = ref(false)
const showVerification = ref(false)

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const handleDownload = async () => {
  isDownloading.value = true
  try {
    await certificateService.downloadCertificate(props.certificate.id)
  } catch (error) {
    console.error('Failed to download certificate:', error)
    alert('Failed to download certificate. Please try again.')
  } finally {
    isDownloading.value = false
  }
}

const handleVerify = () => {
  showVerification.value = !showVerification.value
}

const copyVerificationCode = () => {
  navigator.clipboard.writeText(props.certificate.verification_code)
  alert('Verification code copied to clipboard!')
}
</script>

<style scoped>
.certificate-card {
  background: white;
  border-radius: 8px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.certificate-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 20px;
}

.certificate-icon {
  width: 64px;
  height: 64px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  flex-shrink: 0;
}

.certificate-icon svg {
  width: 32px;
  height: 32px;
}

.certificate-info {
  flex: 1;
}

.course-title {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 4px 0;
}

.issued-date {
  font-size: 14px;
  color: #6b7280;
  margin: 0;
}

.certificate-actions {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
}

.certificate-actions .icon {
  width: 16px;
  height: 16px;
  margin-right: 8px;
}

.verification-section {
  padding: 16px;
  background: #f9fafb;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
}

.verification-code {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 8px;
}

.verification-code label {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
}

.verification-code code {
  font-family: 'Courier New', monospace;
  font-size: 14px;
  background: white;
  padding: 8px 12px;
  border-radius: 4px;
  border: 1px solid #d1d5db;
  flex: 1;
  color: #1f2937;
}

.verification-note {
  font-size: 12px;
  color: #6b7280;
  margin: 0;
}
</style>

