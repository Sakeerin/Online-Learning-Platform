export interface ApiErrorResponse {
  message: string
  error_code: string
  errors?: Record<string, string[]>
  retry_after?: number
}
