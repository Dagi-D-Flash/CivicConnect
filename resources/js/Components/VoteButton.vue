<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    issue: Object,
    auth: Object
})

const voted = ref(false)
const votesCount = ref(props.issue.votes_count)
const processing = ref(false)

// Check if user has voted
const checkVoted = async () => {
    try {
        const response = await fetch(`/api/issues/${props.issue.id}/has-voted`)
        const data = await response.json()
        voted.value = data.voted
    } catch (error) {
        console.error('Failed to check vote status:', error)
    }
}

// Toggle vote
const toggleVote = async () => {
    processing.value = true

    try {
        const response = await fetch(`/api/issues/${props.issue.id}/vote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })

        const data = await response.json()
        voted.value = data.voted
        votesCount.value = data.votes_count
    } catch (error) {
        console.error('Failed to toggle vote:', error)
    } finally {
        processing.value = false
    }
}

// Initial check
onMounted(() => {
    checkVoted()
})
</script>

<template>
    <button
        @click="toggleVote"
        :disabled="processing"
        class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors disabled:opacity-50"
        :class="[
            voted 
                ? 'bg-blue-600 text-white hover:bg-blue-700' 
                : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'
        ]"
    >
        <!-- Thumbs up icon -->
        <svg 
            class="w-5 h-5" 
            :class="{ 'fill-current': voted }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"
            />
        </svg>

        <span class="font-medium">
            {{ voted ? 'Upvoted' : 'Upvote' }}
        </span>

        <span class="font-bold">
            {{ votesCount }}
        </span>
    </button>
</template>
