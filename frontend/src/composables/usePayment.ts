import { ref } from 'vue'
import { paymentService } from '@/services/paymentService'
import type { CheckoutResponse, Transaction } from '@/types/Transaction'

export function usePayment() {
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const createCheckout = async (courseId: string): Promise<CheckoutResponse> => {
    isLoading.value = true
    error.value = null

    try {
      const response = await paymentService.createCheckout(courseId)
      return response
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create checkout session'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const verifyPayment = async (sessionId: string): Promise<Transaction> => {
    isLoading.value = true
    error.value = null

    try {
      const response = await paymentService.verifyPayment(sessionId)
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to verify payment'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const requestRefund = async (transactionId: string, reason?: string): Promise<Transaction> => {
    isLoading.value = true
    error.value = null

    try {
      const response = await paymentService.requestRefund(transactionId, reason)
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to request refund'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const checkRefundEligibility = async (transactionId: string): Promise<boolean> => {
    isLoading.value = true
    error.value = null

    try {
      const response = await paymentService.checkRefundEligibility(transactionId)
      return response.eligible
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to check refund eligibility'
      return false
    } finally {
      isLoading.value = false
    }
  }

  return {
    isLoading,
    error,
    createCheckout,
    verifyPayment,
    requestRefund,
    checkRefundEligibility,
  }
}

