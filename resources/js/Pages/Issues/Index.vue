<template>
    <AppLayout>
        <Head title="All Issues - Notice Board" />

        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">
                        Community Notice Board
                    </h1>
                    <p class="text-xl text-indigo-100 mb-6 max-w-3xl mx-auto">
                        Browse all reported issues in your community. Vote and comment to help prioritize what matters most.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link 
                            href="/report" 
                            class="inline-flex items-center justify-center space-x-2 bg-white text-indigo-700 px-8 py-4 rounded-xl font-semibold hover:bg-indigo-50 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:scale-105"
                        >
                            <PlusCircleIcon class="h-6 w-6" />
                            <span>Report New Issue</span>
                        </Link>
                        <Link 
                            href="/" 
                            class="inline-flex items-center justify-center space-x-2 bg-white/10 backdrop-blur-md text-white px-8 py-4 rounded-xl font-semibold hover:bg-white/20 transition-all duration-200 border-2 border-white/30"
                        >
                            <MapIcon class="h-6 w-6" />
                            <span>View Map</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Search and Filters -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <MagnifyingGlassIcon class="h-5 w-5 inline mr-2" />
                            Search Issues
                        </label>
                        <input
                            v-model="filters.search"
                            @input="debouncedSearch"
                            type="text"
                            placeholder="Search by title, description, or location..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                        />
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <FunnelIcon class="h-5 w-5 inline mr-2" />
                            Status
                        </label>
                        <select
                            v-model="filters.status"
                            @change="applyFilters"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                        >
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <!-- Category Filters -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        <TagIcon class="h-5 w-5 inline mr-2" />
                        Filter by Category
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            @click="filters.category = null; applyFilters()"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                                filters.category === null
                                    ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                            ]"
                        >
                            All Categories
                        </button>
                        <button
                            v-for="category in categories"
                            :key="category.id"
                            @click="filters.category = category.id; applyFilters()"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md',
                                filters.category === category.id
                                    ? 'text-white shadow-lg scale-105'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                            ]"
                            :style="filters.category === category.id ? { backgroundColor: category.color } : {}"
                        >
                            {{ category.name }}
                        </button>
                    </div>
                </div>

                <!-- Sort Options -->
                <div class="mt-6 flex flex-wrap items-center gap-4">
                    <label class="text-sm font-medium text-gray-700">
                        <ArrowsUpDownIcon class="h-5 w-5 inline mr-2" />
                        Sort by:
                    </label>
                    <button
                        v-for="option in sortOptions"
                        :key="option.value"
                        @click="filters.sort = option.value; applyFilters()"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-medium transition-all',
                            filters.sort === option.value
                                ? 'bg-indigo-100 text-indigo-700 border-2 border-indigo-300'
                                : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                        ]"
                    >
                        {{ option.label }}
                    </button>
                </div>
            </div>

            <!-- Stats Summary -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Total Issues</p>
                            <p class="text-3xl font-bold mt-1">{{ issues.total }}</p>
                        </div>
                        <DocumentTextIcon class="h-12 w-12 opacity-80" />
                    </div>
                </div>

                <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Pending</p>
                            <p class="text-3xl font-bold mt-1">{{ getStatusCount('pending') }}</p>
                        </div>
                        <ClockIcon class="h-12 w-12 opacity-80" />
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">In Progress</p>
                            <p class="text-3xl font-bold mt-1">{{ getStatusCount('in_progress') }}</p>
                        </div>
                        <ArrowPathIcon class="h-12 w-12 opacity-80" />
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Resolved</p>
                            <p class="text-3xl font-bold mt-1">{{ getStatusCount('resolved') }}</p>
                        </div>
                        <CheckCircleIcon class="h-12 w-12 opacity-80" />
                    </div>
                </div>
            </div>

            <!-- Issues Grid -->
            <div v-if="issues.data.length > 0" class="space-y-6">
                <div
                    v-for="issue in issues.data"
                    :key="issue.id"
                    class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-200 group"
                >
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Issue Image -->
                            <div v-if="issue.photo_url" class="md:w-48 flex-shrink-0">
                                <img
                                    :src="issue.photo_url"
                                    :alt="issue.title"
                                    class="w-full h-48 object-cover rounded-xl shadow-md group-hover:scale-105 transition-transform duration-300"
                                />
                            </div>

                            <!-- Issue Content -->
                            <div class="flex-1">
                                <!-- Header -->
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h3 class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">
                                            {{ issue.title }}
                                        </h3>
                                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                                            <span class="flex items-center">
                                                <MapPinIcon class="h-4 w-4 mr-1" />
                                                {{ issue.location }}
                                            </span>
                                            <span class="flex items-center">
                                                <CalendarIcon class="h-4 w-4 mr-1" />
                                                {{ formatDate(issue.created_at) }}
                                            </span>
                                            <span class="flex items-center">
                                                <UserIcon class="h-4 w-4 mr-1" />
                                                {{ issue.user_name || 'Anonymous' }}
                                            </span>
                                        </div>
                                    </div>
                                    <span :class="getStatusBadgeClass(issue.status)">
                                        {{ getStatusLabel(issue.status) }}
                                    </span>
                                </div>

                                <!-- Description -->
                                <p class="text-gray-700 mb-4 line-clamp-3">
                                    {{ issue.description }}
                                </p>

                                <!-- Category -->
                                <div class="flex items-center gap-2 mb-4">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white shadow-sm"
                                        :style="{ backgroundColor: issue.category.color }"
                                    >
                                        {{ issue.category.name }}
                                    </span>
                                    <span v-if="issue.priority >= 8" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <ExclamationTriangleIcon class="h-4 w-4 mr-1" />
                                        High Priority
                                    </span>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-wrap items-center gap-4">
                                    <!-- Vote Button -->
                                    <VoteButton
                                        :issue-id="issue.id"
                                        :initial-vote-count="issue.votes_count"
                                        :initial-has-voted="issue.user_has_voted"
                                    />

                                    <!-- Comments -->
                                    <button
                                        @click="showComments(issue.id)"
                                        class="flex items-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all"
                                    >
                                        <ChatBubbleLeftRightIcon class="h-5 w-5" />
                                        <span>{{ issue.comments_count }} Comments</span>
                                    </button>

                                    <!-- View Details -->
                                    <button
                                        @click="viewIssue(issue.id)"
                                        class="flex items-center space-x-2 px-4 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg transition-all font-medium"
                                    >
                                        <EyeIcon class="h-5 w-5" />
                                        <span>View Details</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Comments Section (Expandable) -->
                        <div v-if="expandedIssue === issue.id" class="mt-6 pt-6 border-t border-gray-200">
                            <CommentSection
                                :issue-id="issue.id"
                                :can-comment="$page.props.auth.user !== null"
                                :current-user-id="$page.props.auth.user?.id"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16">
                <DocumentTextIcon class="h-24 w-24 text-gray-300 mx-auto mb-4" />
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">No issues found</h3>
                <p class="text-gray-600 mb-6">Try adjusting your filters or search terms</p>
                <Link
                    href="/report"
                    class="inline-flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition-all"
                >
                    <PlusCircleIcon class="h-5 w-5" />
                    <span>Report First Issue</span>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="issues.data.length > 0" class="mt-8 flex items-center justify-between">
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
                                    ? 'bg-indigo-600 text-white shadow-lg'
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

            <!-- Login Prompt for Guests -->
            <div v-if="!$page.props.auth.user" class="mt-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-xl p-8 text-white text-center">
                <h3 class="text-2xl font-bold mb-3">Want to make a difference?</h3>
                <p class="text-indigo-100 mb-6 max-w-2xl mx-auto">
                    Register now to vote on issues, post comments, and help prioritize what matters most in your community.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        href="/register"
                        class="inline-flex items-center justify-center space-x-2 bg-white text-indigo-700 px-8 py-4 rounded-xl font-semibold hover:bg-indigo-50 transition-all shadow-lg"
                    >
                        <UserPlusIcon class="h-6 w-6" />
                        <span>Create Account</span>
                    </Link>
                    <Link
                        href="/login"
                        class="inline-flex items-center justify-center space-x-2 bg-white/10 backdrop-blur-md text-white px-8 py-4 rounded-xl font-semibold hover:bg-white/20 transition-all border-2 border-white/30"
                    >
                        <ArrowRightOnRectangleIcon class="h-6 w-6" />
                        <span>Sign In</span>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import VoteButton from '@/Components/VoteButton.vue';
import CommentSection from '@/Components/CommentSection.vue';
import {
    MagnifyingGlassIcon,
    FunnelIcon,
    TagIcon,
    ArrowsUpDownIcon,
    DocumentTextIcon,
    ClockIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    MapPinIcon,
    CalendarIcon,
    UserIcon,
    ExclamationTriangleIcon,
    ChatBubbleLeftRightIcon,
    EyeIcon,
    PlusCircleIcon,
    MapIcon,
    UserPlusIcon,
    ArrowRightOnRectangleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    issues: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const filters = ref({
    search: props.filters.search || '',
    category: props.filters.category || null,
    status: props.filters.status || '',
    sort: props.filters.sort || 'latest'
});

const expandedIssue = ref(null);

const sortOptions = [
    { label: 'Latest', value: 'latest' },
    { label: 'Most Voted', value: 'votes' },
    { label: 'Most Commented', value: 'comments' },
    { label: 'Oldest', value: 'oldest' }
];

let searchTimeout = null;
const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
};

const applyFilters = () => {
    router.get('/issues', filters.value, {
        preserveState: true,
        preserveScroll: true
    });
};

const showComments = (issueId) => {
    expandedIssue.value = expandedIssue.value === issueId ? null : issueId;
};

const viewIssue = (issueId) => {
    // TODO: Navigate to issue detail page
    router.visit(`/issues/${issueId}`);
};

const getStatusCount = (status) => {
    return props.issues.data.filter(issue => issue.status === status).length;
};

const formatDate = (date) => {
    const d = new Date(date);
    const now = new Date();
    const diff = now - d;
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));

    if (days === 0) return 'Today';
    if (days === 1) return 'Yesterday';
    if (days < 7) return `${days} days ago`;
    if (days < 30) return `${Math.floor(days / 7)} weeks ago`;
    
    return d.toLocaleDateString();
};

const getStatusBadgeClass = (status) => {
    const classes = {
        pending: 'px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border-2 border-yellow-300',
        in_progress: 'px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 border-2 border-blue-300',
        resolved: 'px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 border-2 border-green-300',
        rejected: 'px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800 border-2 border-red-300'
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
