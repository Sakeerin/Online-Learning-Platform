<template>
  <div class="success-page">
    <div class="container">
      <div v-if="isLoading" class="loading-state">
        <p>Verifying payment...</p>
      </div>
      <div v-else-if="error" class="error-state">
        <h2>Payment Verification Failed</h2>
        <p>{{ error }}</p>
        <Button @click="handleRetry">Try Again</Button>
        <Button variant="secondary" @click="goToHome">Go to Home</Button>
      </div>
      <div v-else-if="transaction" class="success-content">
        <div class="success-icon">✓</div>
        <h1>Payment Successful!</h1>
        <p>You have successfully enrolled in the course.</p>
        <div class="transaction-details">
          <p><strong>Amount:</strong> {{ formatCurrency(transaction.amount, transaction.currency) }}</p>
          <p><strong>Transaction ID:</strong> {{ transaction.id }}</p>
        </div>
        <div class="actions">
          <Button @click="goToCourse">Start Learning</Button>
          <Button variant="secondary" @click="goToMyLearning">My Learning</Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePayment } from '@/composables/usePayment'
import Button from '@/components/common/Button.vue'
import type { Transaction } from '@/types/Transaction'

const route = useRoute()
const router = useRouter()
const { verifyPayment, isLoading, error } = usePayment()
const transaction = ref<Transaction | null>(null)

const formatCurrency = (amount: number, currency: string): string => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: currency,
  }).format(amount)
}

const handleVerification = async () => {
  const sessionId = route.query.session_id as string

  if (!sessionId) {
    router.push({ name: 'home' })
    return
  }

  try {
    const data = await verifyPayment(sessionId)
    transaction.value = data
  } catch (err) {
    console.error('Verification error:', err)
  }
}

const handleRetry = () => {
  handleVerification()
}

const goToCourse = () => {
  if (transaction.value?.course_id) {
    router.push({ name: 'course-player', params: { courseId: transaction.value.course_id } })
  } else {
    router.push({ name: 'my-learning' })
  }
}

const goToMyLearning = () => {
  router.push({ name: 'my-learning' })
}

const goToHome = () => {
  router.push({ name: 'home' })
}

onMounted(() => {
  handleVerification()
})
</script>

<style scoped>
.success-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.container {
  max-width: 600px;
  width: 100%;
  text-align: center;
}

.loading-state,
.error-state,
.success-content {
  padding: 2rem;
}

.success-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: var(--success);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  margin: 0 auto 2rem;
  font-weight: bold;
}

.success-content h1 {
  margin-bottom: 1rem;
  color: var(--success);
}

.success-content p {
  margin-bottom: 1.5rem;
  color: var(--text-secondary);
}

.transaction-details {
  background: var(--bg-secondary);
  padding: 1.5rem;
  border-radius: 8px;
  margin: 2rem 0;
  text-align: left;
}

.transaction-details p {
  margin: 0.5rem 0;
}

.actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-top: 2rem;
}

.error-state h2 {
  color: var(--error);
  margin-bottom: 1rem;
}

.error-state p {
  margin-bottom: 1.5rem;
  color: var(--text-secondary);
}
</style>

