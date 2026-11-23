<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    auth: Object
})

const notifications = ref([])
const unreadCount = ref(0)
const showDropdown = ref(false)
const loading = ref(false)

// Load notifications
const loadNotifications = async () => {
    if (!props.auth.user) return

    loading.value = true
    try {
        const response = await fetch('/api/notifications')
        const data = await response.json()
        
        notifications.value = data.notifications.data
        unreadCount.value = data.unread_count
    } catch (error) {
        console.error('Failed to load notifications:', error)
    } finally {
        loading.value = false
    }
}

// Mark as read
const markAsRead = async (notificationId) => {
    try {
        await fetch(`/api/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        
        loadNotifications()
    } catch (error) {
        console.error('Failed to mark as read:', error)
    }
}

// Mark all as read
const markAllAsRead = async () => {
    try {
        await fetch('/api/notifications/read-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        
        loadNotifications()
    } catch (error) {
        console.error('Failed to mark all as read:', error)
    }
}

// Toggle dropdown
const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value
    if (showDropdown.value && notifications.value.length === 0) {
        loadNotifications()
    }
}

// Initial load
onMounted(() => {
    if (props.auth.user) {
        loadNotifications()
        // Refresh every 30 seconds
        setInterval(loadNotifications, 30000)
    }
})
</script>

<template>
    <div v-if="auth.user" class="relative">
        <!-- Bell Icon -->
        <button
            @click="toggleDropdown"
            class="relative p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:outline-none"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            
            <!-- Badge -->
            <span
                v-if="unreadCount > 0"
                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
            >
                {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown -->
        <div
            v-if="showDropdown"
            class="absolute right-0 mt-2 w-80 bg-white dark:bg-dark-surface rounded-lg shadow-xl z-50 max-h-96 overflow-hidden"
        >
            <!-- Header -->
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Notifications
                </h3>
                <button
                    v-if="unreadCount > 0"
                    @click="markAllAsRead"
                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline"
                >
                    Mark all read
                </button>
            </div>

            <!-- Notifications List -->
            <div class="overflow-y-auto max-h-80">
                <div
                    v-if="loading"
                    class="flex justify-center items-center py-8"
                >
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>

                <div
                    v-else-if="notifications.length === 0"
                    class="px-4 py-8 text-center text-gray-500 dark:text-gray-400"
                >
                    No notifications
                </div>

                <Link
                    v-for="notification in notifications"
                    :key="notification.id"
                    :href="notification.data.issue_id ? `/issues/${notification.data.issue_id}` : '#'"
                    @click="markAsRead(notification.id)"
                    class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-dark-bg border-b border-gray-100 dark:border-gray-800 last:border-0"
                    :class="{ 'bg-blue-50 dark:bg-blue-900/20': !notification.read_at }"
                >
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ notification.data.issue_title || 'Notification' }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ notification.data.commenter_name ? 
                            `${notification.data.commenter_name} commented` : 
                            'Status updated' 
                        }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                        {{ new Date(notification.created_at).toLocaleDateString() }}
                    </p>
                </Link>
            </div>

            <!-- Footer -->
            <Link
                :href="route('dashboard.index')"
                class="block px-4 py-3 text-center text-sm text-blue-600 dark:text-blue-400 hover:bg-gray-50 dark:hover:bg-dark-bg border-t border-gray-200 dark:border-gray-700"
            >
                View all notifications
            </Link>
        </div>
    </div>
</template>
