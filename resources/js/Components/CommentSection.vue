<script setup>
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import CommentItem from './CommentItem.vue'

const props = defineProps({
    issue: Object,
    auth: Object
})

const comments = ref([])
const loading = ref(false)
const page = ref(1)
const hasMore = ref(true)

const commentForm = useForm({
    content: '',
    parent_id: null
})

const replyingTo = ref(null)

// Load comments
const loadComments = async () => {
    loading.value = true
    try {
        const response = await fetch(`/api/issues/${props.issue.id}/comments?page=${page.value}`)
        const data = await response.json()
        
        if (page.value === 1) {
            comments.value = data.data
        } else {
            comments.value.push(...data.data)
        }
        
        hasMore.value = data.next_page_url !== null
    } catch (error) {
        console.error('Failed to load comments:', error)
    } finally {
        loading.value = false
    }
}

// Submit comment
const submitComment = () => {
    if (!props.auth.user) {
        router.visit('/login')
        return
    }

    commentForm.post(`/api/issues/${props.issue.id}/comments`, {
        preserveScroll: true,
        onSuccess: () => {
            commentForm.reset()
            replyingTo.value = null
            page.value = 1
            loadComments()
        }
    })
}

// Reply to comment
const replyTo = (comment) => {
    replyingTo.value = comment
    commentForm.parent_id = comment.id
}

// Cancel reply
const cancelReply = () => {
    replyingTo.value = null
    commentForm.parent_id = null
}

// Load more comments
const loadMore = () => {
    page.value++
    loadComments()
}

// Initial load
loadComments()
</script>

<template>
    <div class="bg-white dark:bg-dark-surface rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            Comments ({{ issue.comments_count }})
        </h2>

        <!-- Comment Form -->
        <div class="mb-6">
            <div v-if="!auth.user" class="text-center p-4 bg-gray-50 dark:bg-dark-bg rounded-lg">
                <p class="text-gray-600 dark:text-gray-400 mb-2">
                    Please login to leave a comment
                </p>
                <a
                    :href="route('login')"
                    class="text-blue-600 dark:text-blue-400 hover:underline"
                >
                    Login here
                </a>
            </div>

            <div v-else>
                <!-- Reply indicator -->
                <div v-if="replyingTo" class="mb-2 flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <span>Replying to {{ replyingTo.user.name }}</span>
                    <button @click="cancelReply" class="ml-2 text-red-600 hover:text-red-700">
                        Cancel
                    </button>
                </div>

                <form @submit.prevent="submitComment" class="space-y-3">
                    <textarea
                        v-model="commentForm.content"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-dark-bg dark:text-white resize-none"
                        placeholder="Write a comment..."
                        required
                    ></textarea>

                    <div v-if="commentForm.errors.content" class="text-sm text-red-600">
                        {{ commentForm.errors.content }}
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="commentForm.processing || !commentForm.content.trim()"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ commentForm.processing ? 'Posting...' : 'Post Comment' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Comments List -->
        <div class="space-y-4">
            <CommentItem
                v-for="comment in comments"
                :key="comment.id"
                :comment="comment"
                :auth="auth"
                @reply="replyTo"
            />

            <!-- Load More -->
            <div v-if="hasMore && !loading" class="text-center">
                <button
                    @click="loadMore"
                    class="px-4 py-2 text-blue-600 dark:text-blue-400 hover:underline"
                >
                    Load more comments
                </button>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="text-center py-4">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>

            <!-- No comments -->
            <div v-if="!loading && comments.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                No comments yet. Be the first to comment!
            </div>
        </div>
    </div>
</template>
