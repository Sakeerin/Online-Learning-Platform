import api from './api'
import type { CheckoutResponse, RefundEligibilityResponse, Transaction } from '@/types/Transaction'

export const paymentService = {
  /**
   * Create a checkout session for course purchase
   */
  async createCheckout(courseId: string): Promise<CheckoutResponse> {
    const response = await api.post<CheckoutResponse>('/v1/payment/checkout', {
      course_id: courseId,
    })
    return response.data
  },

  /**
   * Verify payment after redirect from Stripe
   */
  async verifyPayment(sessionId: string): Promise<{ message: string; data: Transaction }> {
    const response = await api.post<{ message: string; data: Transaction }>('/v1/payment/verify', {
      session_id: sessionId,
    })
    return response.data
  },

  /**
   * Check refund eligibility for a transaction
   */
  async checkRefundEligibility(transactionId: string): Promise<RefundEligibilityResponse> {
    const response = await api.get<RefundEligibilityResponse>(
      `/v1/payment/transactions/${transactionId}/refund/eligibility`
    )
    return response.data
  },

  /**
   * Request a refund for a transaction
   */
  async requestRefund(transactionId: string, reason?: string): Promise<{ message: string; data: Transaction }> {
    const response = await api.post<{ message: string; data: Transaction }>(
      `/v1/payment/transactions/${transactionId}/refund`,
      { reason }
    )
    return response.data
  },
}

