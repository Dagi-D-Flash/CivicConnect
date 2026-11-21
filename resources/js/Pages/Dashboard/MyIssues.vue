<template>
    <AuthenticatedLayout>
        <Head title="My Issues" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-900">My Reported Issues</h2>
                        <p class="text-sm text-gray-600 mt-1">Track all the issues you've reported</p>
                    </div>

                    <div class="p-6">
                        <div v-if="issues.data.length === 0" class="text-center py-12">
                            <DocumentTextIcon class="h-16 w-16 text-gray-300 mx-auto mb-4" />
                            <p class="text-gray-600 mb-4">You haven't reported any issues yet</p>
                            <Link
                                href="/report"
                                class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-all"
                            >
                                <PlusCircleIcon class="h-5 w-5" />
                                <span>Report Your First Issue</span>
                            </Link>
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="issue in issues.data"
                                :key="issue.id"
                                class="border border-gray-200 rounded-lg p-6 hover:border-blue-300 hover:shadow-md transition-all"
                            >
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ issue.title }}</h3>
                                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                                            <span class="flex items-center">
                                                <TagIcon class="h-4 w-4 mr-1" />
                                                {{ issue.category.name }}
                                            </span>
                                            <span class="flex items-center">
                                                <CalendarIcon class="h-4 w-4 mr-1" />
                                                {{ formatDate(issue.created_at) }}
                                            </span>
                                            <span class="flex items-center">
                                                <HandThumbUpIcon class="h-4 w-4 mr-1" />
                                                {{ issue.votes_count }} votes
                                            </span>
                                            <span class="flex items-center">
                                                <ChatBubbleLeftRightIcon class="h-4 w-4 mr-1" />
                                                {{ issue.comments_count }} comments
                                            </span>
                                        </div>
                                    </div>
                                    <span :class="getStatusClass(issue.status)">
                                        {{ getStatusLabel(issue.status) }}
                                    </span>
                                </div>
                                <p class="text-gray-700 mb-4">{{ issue.description }}</p>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="issues.data.length > 0" class="mt-6 flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                Showing {{ issues.from }} to {{ issues.to }} of {{ issues.total }} issues
                            </div>
                            <div class="flex gap-2">
                                <template v-for="link in issues.links" :key="link.label">
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        :class="[
                                            'px-4 py-2 rounded-lg text-sm font-medium transition-all',
                                            link.active
                                                ? 'bg-blue-600 text-white'
                                                : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'
                                        ]"
                                        v-html="link.label"
                                    />
                                    <span
                                        v-else
                                        class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-400 cursor-not-allowed"
                                        v-html="link.label"
                                    />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    DocumentTextIcon,
    PlusCircleIcon,
    TagIcon,
    CalendarIcon,
    HandThumbUpIcon,
    ChatBubbleLeftRightIcon
} from '@heroicons/vue/24/outline';

defineProps({
    issues: {
        type: Object,
        required: true
    }
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const getStatusClass = (status) => {
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
