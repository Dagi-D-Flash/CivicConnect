<template>
    <AppLayout>
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">
                        Community Issues Map
                    </h1>
                    <p class="text-xl text-blue-100 mb-6">
                        View and track reported issues in your neighborhood
                    </p>
                    <Link 
                        href="/report" 
                        class="inline-flex items-center space-x-2 bg-white text-blue-700 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
                    >
                        <PlusCircleIcon class="h-6 w-6" />
                        <span>Report New Issue</span>
                    </Link>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-gray-500 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Issues</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ issues.length }}</p>
                        </div>
                        <DocumentTextIcon class="h-12 w-12 text-gray-400" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-yellow-600">Pending</p>
                            <p class="text-3xl font-bold text-yellow-700 mt-1">{{ getStatusCount('pending') }}</p>
                        </div>
                        <ClockIcon class="h-12 w-12 text-yellow-400" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600">In Progress</p>
                            <p class="text-3xl font-bold text-blue-700 mt-1">{{ getStatusCount('in_progress') }}</p>
                        </div>
                        <ArrowPathIcon class="h-12 w-12 text-blue-400" />
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600">Resolved</p>
                            <p class="text-3xl font-bold text-green-700 mt-1">{{ getStatusCount('resolved') }}</p>
                        </div>
                        <CheckCircleIcon class="h-12 w-12 text-green-400" />
                    </div>
                </div>
            </div>

            <!-- Category Filters -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <FunnelIcon class="h-5 w-5 mr-2 text-gray-600" />
                    Filter by Category
                </h3>
                <div class="flex flex-wrap gap-2">
                    <button
                        @click="selectedCategory = null"
                        :class="[
                            'px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2',
                            selectedCategory === null
                                ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ]"
                    >
                        <span>All Issues</span>
                        <span class="bg-white/20 px-2 py-0.5 rounded-full text-xs">{{ issues.length }}</span>
                    </button>
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        @click="selectedCategory = category.id"
                        :class="[
                            'px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow-md',
                            selectedCategory === category.id
                                ? 'text-white shadow-lg scale-105'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ]"
                        :style="selectedCategory === category.id ? { backgroundColor: category.color } : {}"
                    >
                        <component :is="getCategoryIcon(category.slug)" class="h-5 w-5" />
                        <span>{{ category.name }}</span>
                        <span :class="selectedCategory === category.id ? 'bg-white/20' : 'bg-gray-200'" class="px-2 py-0.5 rounded-full text-xs">
                            {{ getIssueCount(category.id) }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- Status Filters -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <AdjustmentsHorizontalIcon class="h-5 w-5 mr-2 text-gray-600" />
                    Filter by Status
                </h3>
                <div class="flex flex-wrap gap-2">
                    <button
                        @click="selectedStatus = null"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2',
                            selectedStatus === null
                                ? 'bg-gray-800 text-white shadow-lg'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ]"
                    >
                        <span>All Statuses</span>
                    </button>
                    <button
                        @click="selectedStatus = 'pending'"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2',
                            selectedStatus === 'pending'
                                ? 'bg-yellow-500 text-white shadow-lg'
                                : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200'
                        ]"
                    >
                        <ClockIcon class="h-4 w-4" />
                        <span>Pending</span>
                    </button>
                    <button
                        @click="selectedStatus = 'in_progress'"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2',
                            selectedStatus === 'in_progress'
                                ? 'bg-blue-500 text-white shadow-lg'
                                : 'bg-blue-100 text-blue-800 hover:bg-blue-200'
                        ]"
                    >
                        <ArrowPathIcon class="h-4 w-4" />
                        <span>In Progress</span>
                    </button>
                    <button
                        @click="selectedStatus = 'resolved'"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2',
                            selectedStatus === 'resolved'
                                ? 'bg-green-500 text-white shadow-lg'
                                : 'bg-green-100 text-green-800 hover:bg-green-200'
                        ]"
                    >
                        <CheckCircleIcon class="h-4 w-4" />
                        <span>Resolved</span>
                    </button>
                </div>
            </div>

            <!-- Map -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <MapIcon class="h-6 w-6 mr-2 text-blue-600" />
                        Interactive Map
                        <span class="ml-auto text-sm font-normal text-gray-600">
                            {{ filteredIssues.length }} issue{{ filteredIssues.length !== 1 ? 's' : '' }} shown
                        </span>
                    </h3>
                </div>
                <MapView 
                    :issues="filteredIssues" 
                    :selected-category="selectedCategory"
                    map-height="600px"
                    @issue-selected="handleIssueSelected"
                />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MapView from '@/Components/MapView.vue';
import {
    PlusCircleIcon,
    MapIcon,
    DocumentTextIcon,
    ClockIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    FunnelIcon,
    AdjustmentsHorizontalIcon,
    TruckIcon,
    TrashIcon,
    LightBulbIcon,
    WrenchScrewdriverIcon,
    BoltIcon,
    ShieldExclamationIcon,
    BuildingLibraryIcon,
    EllipsisHorizontalCircleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    issues: {
        type: Array,
        required: true
    },
    categories: {
        type: Array,
        required: true
    }
});

const selectedCategory = ref(null);
const selectedStatus = ref(null);

const filteredIssues = computed(() => {
    let filtered = props.issues;
    
    if (selectedCategory.value !== null) {
        filtered = filtered.filter(issue => issue.category_id === selectedCategory.value);
    }
    
    if (selectedStatus.value !== null) {
        filtered = filtered.filter(issue => issue.status === selectedStatus.value);
    }
    
    return filtered;
});

const getIssueCount = (categoryId) => {
    return props.issues.filter(issue => issue.category_id === categoryId).length;
};

const getStatusCount = (status) => {
    return props.issues.filter(issue => issue.status === status).length;
};

const getCategoryIcon = (slug) => {
    const iconMap = {
        'road-issues': TruckIcon,
        'waste-management': TrashIcon,
        'street-lighting': LightBulbIcon,
        'water-drainage': WrenchScrewdriverIcon,
        'electricity': BoltIcon,
        'public-safety': ShieldExclamationIcon,
        'parks-recreation': BuildingLibraryIcon,
        'other': EllipsisHorizontalCircleIcon
    };
    return iconMap[slug] || EllipsisHorizontalCircleIcon;
};

const handleIssueSelected = (issue) => {
    console.log('Issue selected:', issue);
};
</script>
