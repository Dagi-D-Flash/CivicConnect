<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    comment: Object,
    auth: Object
})

const emit = defineEmits(['reply'])

const isEditing = ref(false)
const showReplies = ref(false)

const editForm = useForm({
    content: props.comment.content
})

const startEdit = () => {
    isEditing.value = true
}

const cancelEdit = () => {
    isEditing.value = false
    editForm.content = props.comment.content
}

const updateComment = () => {
    editForm.put(`/api/comments/${props.comment.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false
        }
    })
}

const deleteComment = () => {
    if (confirm('Are you sure you want to delete this comment?')) {
        useForm({}).delete(`/api/comments/${props.comment.id}`, {
            preserveScroll: true
        })
    }
}

const toggleReplies = () => {
    showReplies.value = !showReplies.value
}
</script>

<template>
    <div class="border-l-2 border-gray-200 dark:border-gray-700 pl-4">
        <div class="flex items-start space-x-3">
            <!-- Avatar -->
            <img
                :src="comment.user.avatar_url || '/images/default-avatar.png'"
                :alt="comment.user.name"
                class="w-10 h-10 rounded-full flex-shrink-0"
            />

            <div class="flex-1">
                <!-- Header -->
                <div class="flex items-center space-x-2 mb-1">
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {{ comment.user.name }}
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ new Date(comment.created_at).toLocaleDateString() }}
                    </span>
                </div>

                <!-- Content or Edit Form -->
                <div v-if="!isEditing" class="text-gray-700 dark:text-gray-300 mb-2">
                    {{ comment.content }}
                </div>

                <form v-else @submit.prevent="updateComment" class="mb-2">
                    <textarea
                        v-model="editForm.content"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-dark-bg dark:text-white"
                    ></textarea>
                    <div class="flex space-x-2 mt-2">
                        <button
                            type="submit"
                            :disabled="editForm.processing"
                            class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm"
                        >
                            Save
                        </button>
                        <button
                            type="button"
                            @click="cancelEdit"
                            class="px-3 py-1 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-400 dark:hover:bg-gray-600 text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </form>

                <!-- Actions -->
                <div class="flex items-center space-x-4 text-sm">
                    <button
                        v-if="auth.user"
                        @click="emit('reply', comment)"
                        class="text-blue-600 dark:text-blue-400 hover:underline"
                    >
                        Reply
                    </button>

                    <template v-if="auth.user && auth.user.id === comment.user_id">
                        <button
                            @click="startEdit"
                            class="text-gray-600 dark:text-gray-400 hover:underline"
                        >
                            Edit
                        </button>
                        <button
                            @click="deleteComment"
                            class="text-red-600 dark:text-red-400 hover:underline"
                        >
                            Delete
                        </button>
                    </template>

                    <!-- Show replies button -->
                    <button
                        v-if="comment.replies_count > 0"
                        @click="toggleReplies"
                        class="text-gray-600 dark:text-gray-400 hover:underline"
                    >
                        {{ showReplies ? 'Hide' : 'Show' }} {{ comment.replies_count }} 
                        {{ comment.replies_count === 1 ? 'reply' : 'replies' }}
                    </button>
                </div>

                <!-- Replies -->
                <div v-if="showReplies && comment.replies" class="mt-4 space-y-3">
                    <CommentItem
                        v-for="reply in comment.replies"
                        :key="reply.id"
                        :comment="reply"
                        :auth="auth"
                        @reply="emit('reply', $event)"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
