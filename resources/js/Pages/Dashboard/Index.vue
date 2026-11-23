<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />

        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold mb-2">
                            Welcome back, {{ displayName }}!
                        </h1>
                        <p class="text-blue-100 text-lg">
                            Here's your community impact summary
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white/20 backdrop-blur-md rounded-xl p-6 text-center">
                            <TrophyIcon class="h-12 w-12 mx-auto mb-2 text-yellow-300" />
                            <p class="text-2xl font-bold">{{ stats.reputation_points }}</p>
                            <p class="text-sm text-blue-100">Reputation Points</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Issues Reported -->
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Issues Reported</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.issues_reported }}</p>
                        </div>
                        <DocumentTextIcon class="h-12 w-12 text-blue-500" />
                    </div>
                </div>

                <!-- Issues Resolved -->
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Issues Resolved</p>
                            <p class="text-3xl font-bold text-green-700 mt-2">{{ stats.issues_resolved }}</p>
                        </div>
                        <CheckCircleIcon class="h-12 w-12 text-green-500" />
                    </div>
                </div>

                <!-- Total Votes -->
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Votes</p>
                            <p class="text-3xl font-bold text-purple-700 mt-2">{{ stats.total_votes }}</p>
                        </div>
                        <HandThumbUpIcon class="h-12 w-12 text-purple-500" />
                    </div>
                </div>

                <!-- Total Comments -->
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Comments</p>
                            <p class="text-3xl font-bold text-orange-700 mt-2">{{ stats.total_comments }}</p>
                        </div>
                        <ChatBubbleLeftRightIcon class="h-12 w-12 text-orange-500" />
                    </div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Issues -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <DocumentTextIcon class="h-6 w-6 mr-2 text-blue-600" />
                            Recent Issues
                        </h3>
                    </div>
                    <div class="p-6">
                        <div v-if="recentIssues.length === 0" class="text-center py-8">
                            <DocumentTextIcon class="h-16 w-16 text-gray-300 mx-auto mb-3" />
                            <p class="text-gray-600">No issues reported yet</p>
                            <Link href="/report" class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-medium">
                                Report your first issue →
                            </Link>
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="issue in recentIssues"
                                :key="issue.id"
                                class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition-all"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 mb-1">{{ issue.title }}</h4>
                                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                                            <span class="flex items-center">
                                                <TagIcon class="h-4 w-4 mr-1" />
                                                {{ issue.category.name }}
                                            </span>
                                            <span class="flex items-center">
                                                <CalendarIcon class="h-4 w-4 mr-1" />
                                                {{ formatDate(issue.created_at) }}
                                            </span>
                                        </div>
                                    </div>
                                    <span :class="getStatusClass(issue.status)">
                                        {{ getStatusLabel(issue.status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-if="recentIssues.length > 0" class="mt-6 text-center">
                            <Link href="/dashboard/my-issues" class="text-blue-600 hover:text-blue-800 font-medium">
                                View all issues →
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Recent Comments -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <ChatBubbleLeftRightIcon class="h-6 w-6 mr-2 text-orange-600" />
                            Recent Comments
                        </h3>
                    </div>
                    <div class="p-6">
                        <div v-if="recentComments.length === 0" class="text-center py-8">
                            <ChatBubbleLeftRightIcon class="h-16 w-16 text-gray-300 mx-auto mb-3" />
                            <p class="text-gray-600">No comments yet</p>
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="comment in recentComments"
                                :key="comment.id"
                                class="border border-gray-200 rounded-lg p-4 hover:border-orange-300 hover:shadow-md transition-all"
                            >
                                <p class="text-gray-700 mb-2">{{ comment.content }}</p>
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span class="flex items-center">
                                        <DocumentTextIcon class="h-4 w-4 mr-1" />
                                        {{ comment.issue.title }}
                                    </span>
                                    <span>{{ formatDate(comment.created_at) }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-if="recentComments.length > 0" class="mt-6 text-center">
                            <Link href="/dashboard/my-comments" class="text-orange-600 hover:text-orange-800 font-medium">
                                View all comments →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 border border-blue-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <Link
                        href="/report"
                        class="flex items-center space-x-3 bg-white p-4 rounded-lg hover:shadow-md transition-all group"
                    >
                        <div class="bg-blue-100 p-3 rounded-lg group-hover:bg-blue-200 transition-colors">
                            <PlusCircleIcon class="h-6 w-6 text-blue-600" />
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Report Issue</p>
                            <p class="text-sm text-gray-600">Submit a new report</p>
                        </div>
                    </Link>

                    <Link
                        href="/"
                        class="flex items-center space-x-3 bg-white p-4 rounded-lg hover:shadow-md transition-all group"
                    >
                        <div class="bg-green-100 p-3 rounded-lg group-hover:bg-green-200 transition-colors">
                            <MapIcon class="h-6 w-6 text-green-600" />
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Explore Map</p>
                            <p class="text-sm text-gray-600">View all issues</p>
                        </div>
                    </Link>

                    <Link
                        href="/dashboard/profile"
                        class="flex items-center space-x-3 bg-white p-4 rounded-lg hover:shadow-md transition-all group"
                    >
                        <div class="bg-purple-100 p-3 rounded-lg group-hover:bg-purple-200 transition-colors">
                            <UserCircleIcon class="h-6 w-6 text-purple-600" />
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Edit Profile</p>
                            <p class="text-sm text-gray-600">Update your info</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    DocumentTextIcon,
    CheckCircleIcon,
    HandThumbUpIcon,
    ChatBubbleLeftRightIcon,
    TrophyIcon,
    TagIcon,
    CalendarIcon,
    PlusCircleIcon,
    MapIcon,
    UserCircleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    stats: {
        type: Object,
        required: true
    },
    recentIssues: {
        type: Array,
        default: () => []
    },
    recentComments: {
        type: Array,
        default: () => []
    }
});

const page = usePage();
const displayName = computed(() => {
    const user = page.props.auth?.user;
    if (!user) return '';
    if (user.is_anonymous && user.alias) return user.alias;
    return user.name;
});

const formatDate = (date) => {
    const d = new Date(date);
    const now = new Date();
    const diff = now - d;
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));

    if (days === 0) return 'Today';
    if (days === 1) return 'Yesterday';
    if (days < 7) return `${days} days ago`;
    
    return d.toLocaleDateString();
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
