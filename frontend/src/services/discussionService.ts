import api from './api'
import type {
  Discussion,
  Reply,
  CreateDiscussionData,
  CreateReplyData,
  DiscussionFilters,
  ReplyFilters,
  PaginatedResponse,
} from '@/types/Discussion'

const discussionService = {
  /**
   * Get all discussions for a course
   */
  async getDiscussions(courseId: string, filters?: DiscussionFilters): Promise<PaginatedResponse<Discussion>> {
    const params = new URLSearchParams()
    if (filters?.lesson_id) params.append('lesson_id', filters.lesson_id)
    if (filters?.is_answered !== undefined) params.append('is_answered', String(filters.is_answered))
    if (filters?.search) params.append('search', filters.search)
    if (filters?.sort_by) params.append('sort_by', filters.sort_by)
    if (filters?.sort_order) params.append('sort_order', filters.sort_order)
    if (filters?.per_page) params.append('per_page', String(filters.per_page))

    const response = await api.get<PaginatedResponse<Discussion>>(
      `/student/courses/${courseId}/discussions?${params.toString()}`
    )
    return response.data
  },

  /**
   * Get a specific discussion with replies
   */
  async getDiscussion(courseId: string, discussionId: string): Promise<Discussion> {
    const response = await api.get<{ data: Discussion }>(
      `/student/courses/${courseId}/discussions/${discussionId}`
    )
    return response.data.data
  },

  /**
   * Create a new discussion/question
   */
  async createDiscussion(courseId: string, data: CreateDiscussionData): Promise<Discussion> {
    const response = await api.post<{ message: string; data: Discussion }>(
      `/student/courses/${courseId}/discussions`,
      data
    )
    return response.data.data
  },

  /**
   * Upvote a discussion
   */
  async upvoteDiscussion(courseId: string, discussionId: string): Promise<Discussion> {
    const response = await api.post<{ message: string; data: Discussion }>(
      `/student/courses/${courseId}/discussions/${discussionId}/upvote`
    )
    return response.data.data
  },

  /**
   * Get replies for a discussion
   */
  async getReplies(courseId: string, discussionId: string, filters?: ReplyFilters): Promise<PaginatedResponse<Reply>> {
    const params = new URLSearchParams()
    if (filters?.instructor_only !== undefined) params.append('instructor_only', String(filters.instructor_only))
    if (filters?.sort_by) params.append('sort_by', filters.sort_by)
    if (filters?.sort_order) params.append('sort_order', filters.sort_order)
    if (filters?.per_page) params.append('per_page', String(filters.per_page))

    const response = await api.get<PaginatedResponse<Reply>>(
      `/student/courses/${courseId}/discussions/${discussionId}/replies?${params.toString()}`
    )
    return response.data
  },

  /**
   * Create a reply to a discussion
   */
  async createReply(courseId: string, discussionId: string, data: CreateReplyData): Promise<Reply> {
    const response = await api.post<{ message: string; data: Reply }>(
      `/student/courses/${courseId}/discussions/${discussionId}/replies`,
      data
    )
    return response.data.data
  },

  /**
   * Upvote a reply
   */
  async upvoteReply(courseId: string, discussionId: string, replyId: string): Promise<Reply> {
    const response = await api.post<{ message: string; data: Reply }>(
      `/student/courses/${courseId}/discussions/${discussionId}/replies/${replyId}/upvote`
    )
    return response.data.data
  },
}

export default discussionService

