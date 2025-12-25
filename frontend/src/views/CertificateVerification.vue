<template>
  <div class="verification-page">
    <div class="container">
      <header class="page-header">
        <h1>Certificate Verification</h1>
        <p>Verify the authenticity of a course completion certificate</p>
      </header>

      <div class="verification-form">
        <div class="input-group">
          <label for="verification-code">Verification Code</label>
          <input
            id="verification-code"
            v-model="verificationCode"
            type="text"
            placeholder="Enter 32-character verification code"
            class="verification-input"
            @keyup.enter="handleVerify"
          />
          <Button @click="handleVerify" :disabled="!verificationCode || isLoading" variant="primary">
            {{ isLoading ? 'Verifying...' : 'Verify Certificate' }}
          </Button>
        </div>
      </div>

      <div v-if="error" class="error-message">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p>{{ error }}</p>
      </div>

      <div v-if="certificate" class="certificate-result">
        <div class="result-header valid">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h2>Certificate Verified</h2>
          <p>This certificate is authentic and valid</p>
        </div>

        <div class="certificate-details">
          <div class="detail-item">
            <label>Student Name:</label>
            <span>{{ certificate.user?.name || 'N/A' }}</span>
          </div>
          <div class="detail-item">
            <label>Course:</label>
            <span>{{ certificate.course?.title || 'N/A' }}</span>
          </div>
          <div class="detail-item">
            <label>Issued Date:</label>
            <span>{{ formatDate(certificate.issued_at) }}</span>
          </div>
          <div class="detail-item">
            <label>Verification Code:</label>
            <code>{{ certificate.verification_code }}</code>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import Button from '@/components/common/Button.vue'
import certificateService from '@/services/certificateService'
import type { Certificate } from '@/types/Certificate'

const route = useRoute()
const verificationCode = ref((route.query.code as string) || '')
const certificate = ref<Certificate | null>(null)
const error = ref<string | null>(null)
const isLoading = ref(false)

// Auto-verify if code is in query params
if (verificationCode.value) {
  handleVerify()
}

async function handleVerify() {
  if (!verificationCode.value) {
    error.value = 'Please enter a verification code'
    return
  }

  isLoading.value = true
  error.value = null
  certificate.value = null

  try {
    const result = await certificateService.verifyCertificate(verificationCode.value)
    
    if (result.valid && result.data) {
      certificate.value = result.data
    } else {
      error.value = result.message || 'Certificate not found or invalid verification code'
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to verify certificate. Please try again.'
  } finally {
    isLoading.value = false
  }
}

function formatDate(dateString: string) {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}
</script>

<style scoped>
.verification-page {
  min-height: 100vh;
  padding: 2rem 0;
  background: #f9fafb;
}

.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.page-header {
  text-align: center;
  margin-bottom: 3rem;
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

.verification-form {
  background: white;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.input-group label {
  font-weight: 500;
  color: #374151;
}

.verification-input {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 1rem;
  font-family: 'Courier New', monospace;
  letter-spacing: 0.05em;
}

.verification-input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.error-message {
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  color: #dc2626;
  margin-bottom: 2rem;
}

.error-message svg {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
}

.certificate-result {
  background: white;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.result-header {
  text-align: center;
  padding: 2rem 0;
  border-bottom: 2px solid #e5e7eb;
  margin-bottom: 2rem;
}

.result-header.valid {
  color: #10b981;
}

.result-header svg {
  width: 64px;
  height: 64px;
  margin: 0 auto 1rem;
  display: block;
}

.result-header h2 {
  font-size: 1.5rem;
  margin: 0 0 0.5rem 0;
}

.result-header p {
  color: #6b7280;
  margin: 0;
}

.certificate-details {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 6px;
}

.detail-item label {
  font-weight: 500;
  color: #6b7280;
}

.detail-item span,
.detail-item code {
  color: #1f2937;
  font-weight: 500;
}

.detail-item code {
  font-family: 'Courier New', monospace;
  font-size: 0.875rem;
  background: white;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  border: 1px solid #d1d5db;
}
</style>

