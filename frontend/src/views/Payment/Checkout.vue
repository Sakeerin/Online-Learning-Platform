<template>
  <div class="checkout-page">
    <div class="container">
      <div v-if="isLoading" class="loading-state">
        <p>Creating checkout session...</p>
      </div>
      <div v-else-if="error" class="error-state">
        <h2>Checkout Error</h2>
        <p>{{ error }}</p>
        <Button @click="handleRetry">Try Again</Button>
      </div>
      <div v-else class="checkout-content">
        <h1>Redirecting to payment...</h1>
        <p>You will be redirected to Stripe to complete your payment.</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePayment } from '@/composables/usePayment'
import Button from '@/components/common/Button.vue'

const route = useRoute()
const router = useRouter()
const { createCheckout, isLoading, error } = usePayment()

const handleCheckout = async () => {
  const courseId = route.query.course_id as string

  if (!courseId) {
    router.push({ name: 'home' })
    return
  }

  try {
    const response = await createCheckout(courseId)
    // Redirect to Stripe Checkout
    window.location.href = response.checkout_url
  } catch (err) {
    // Error is handled by composable
    console.error('Checkout error:', err)
  }
}

const handleRetry = () => {
  handleCheckout()
}

onMounted(() => {
  handleCheckout()
})
</script>

<style scoped>
.checkout-page {
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
.checkout-content {
  padding: 2rem;
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

