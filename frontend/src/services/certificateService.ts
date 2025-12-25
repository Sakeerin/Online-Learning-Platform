import api from './api'
import type { Certificate, CertificateVerification } from '@/types/Certificate'

const certificateService = {
  /**
   * Get all certificates for the authenticated user
   */
  async getCertificates(): Promise<Certificate[]> {
    const response = await api.get<{ data: Certificate[] }>('/student/certificates')
    return response.data.data
  },

  /**
   * Get certificate for a specific enrollment
   */
  async getEnrollmentCertificate(enrollmentId: string): Promise<Certificate> {
    const response = await api.get<{ data: Certificate }>(`/student/enrollments/${enrollmentId}/certificate`)
    return response.data.data
  },

  /**
   * Download certificate PDF
   */
  async downloadCertificate(certificateId: string): Promise<void> {
    const response = await api.get(`/student/certificates/${certificateId}/download`, {
      responseType: 'blob',
    })
    
    // Create blob URL and trigger download
    const blob = new Blob([response.data])
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `certificate-${certificateId}.pdf`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
  },

  /**
   * Verify a certificate by verification code (public)
   */
  async verifyCertificate(verificationCode: string): Promise<CertificateVerification> {
    const response = await api.get<CertificateVerification>(`/certificates/verify/${verificationCode}`)
    return response.data
  },
}

export default certificateService

