export interface Transaction {
  id: string
  user_id: string
  course_id: string
  amount: number
  currency: string
  platform_fee: number
  instructor_revenue: number
  payment_method: string
  status: 'pending' | 'completed' | 'refunded'
  refunded_at?: string
  refund_reason?: string
  created_at: string
  updated_at: string
  course?: any
  user?: any
}

export interface CheckoutResponse {
  checkout_url: string
  session_id: string
}

export interface RefundEligibilityResponse {
  eligible: boolean
  transaction: Transaction
}

