<template>
    <AppLayout>
        <Head :title="issue.title" />

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-6">
                <Link href="/issues" class="text-sm text-gray-600 hover:text-blue-700">← Back to Issues</Link>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row gap-6">
                        <div v-if="issue.photo_url" class="md:w-80 flex-shrink-0">
                            <img :src="issue.photo_url" :alt="issue.title" class="w-full h-56 object-cover rounded-xl" />
                        </div>

                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-3">
                                <h1 class="text-2xl font-bold text-gray-900">{{ issue.title }}</h1>
                                <span :class="getStatusBadgeClass(issue.status)">{{ getStatusLabel(issue.status) }}</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white" :style="{ backgroundColor: issue.category.color }">
                                    {{ issue.category.name }}
                                </span>
                                <span>
                                    Reported by {{ issue.user_name || 'Anonymous' }} on {{ formatDate(issue.created_at) }}
                                </span>
                                <span>{{ issue.votes_count }} votes</span>
                                <span>{{ issue.comments_count }} comments</span>
                            </div>
                            <p class="text-gray-800 whitespace-pre-line">{{ issue.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    issue: {
        type: Object,
        required: true
    }
});

const formatDate = (date) => new Date(date).toLocaleDateString();

const getStatusBadgeClass = (status) => {
    const classes = {
        pending: 'px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800',
        in_progress: 'px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800',
        resolved: 'px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800',
        rejected: 'px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800'
    };
    return classes[status] || classes.pending;
};

const getStatusLabel = (status) => {
    const labels = {
        pending: 'Pending',
        in_progress: 'In Progress',
        resolved: 'Resolved',
        rejected: 'Rejected'
    };
    return labels[status] || status;
};
</script>
