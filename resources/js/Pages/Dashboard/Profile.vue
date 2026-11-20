<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    user: Object,
    statistics: Object
})

// Profile form
const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
    bio: props.user.bio || '',
    phone: props.user.phone || ''
})

// Password form
const passwordForm = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
})

// Avatar upload
const avatarPreview = ref(null)
const avatarInput = ref(null)

const updateProfile = () => {
    profileForm.put(route('dashboard.profile.update'), {
        preserveScroll: true
    })
}

const changePassword = () => {
    passwordForm.put(route('dashboard.password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset()
    })
}

const selectAvatar = () => {
    avatarInput.value.click()
}

const updateAvatar = (event) => {
    const file = event.target.files[0]
    if (file) {
        const reader = new FileReader()
        reader.onload = (e) => {
            avatarPreview.value = e.target.result
        }
        reader.readAsDataURL(file)

        // Upload avatar
        const form = new FormData()
        form.append('avatar', file)
        
        router.post(route('dashboard.avatar.update'), form, {
            preserveScroll: true,
            onSuccess: () => {
                avatarPreview.value = null
            }
        })
    }
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-dark-bg py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Profile Settings</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Manage your account information and preferences</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Stats -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-dark-surface rounded-lg shadow p-6">
                        <!-- Avatar -->
                        <div class="text-center mb-6">
                            <div class="relative inline-block">
                                <img
                                    :src="avatarPreview || user.avatar_url || '/images/default-avatar.png'"
                                    alt="Profile picture"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-blue-500"
                                />
                                <button
                                    @click="selectAvatar"
                                    class="absolute bottom-0 right-0 bg-blue-600 text-white rounded-full p-2 hover:bg-blue-700 shadow-lg"
                                    title="Change avatar"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                                <input
                                    ref="avatarInput"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="updateAvatar"
                                />
                            </div>
                            <h2 class="mt-4 text-xl font-bold text-gray-900 dark:text-white">{{ user.name }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">{{ user.email }}</p>
                        </div>

                        <!-- Statistics -->
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Total Issues</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ statistics.total_issues }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Pending</span>
                                <span class="font-bold text-yellow-600">{{ statistics.pending_issues }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">In Progress</span>
                                <span class="font-bold text-blue-600">{{ statistics.in_progress_issues }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600 dark:text-gray-400">Resolved</span>
                                <span class="font-bold text-green-600">{{ statistics.resolved_issues }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Information -->
                    <div class="bg-white dark:bg-dark-surface rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Personal Information</h2>
                        
                        <form @submit.prevent="updateProfile" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Full Name
                                </label>
                                <input
                                    v-model="profileForm.name"
                                    type="text"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-dark-bg dark:text-white"
                                    required
                                />
                                <div v-if="profileForm.errors.name" class="mt-1 text-sm text-red-600">
                                    {{ profileForm.errors.name }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email Address
                                </label>
                                <input
                                    v-model="profileForm.email"
                                    type="email"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-dark-bg dark:text-white"
                                    required
                                />
                                <div v-if="profileForm.errors.email" class="mt-1 text-sm text-red-600">
                                    {{ profileForm.errors.email }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Phone Number
                                </label>
                                <input
                                    v-model="profileForm.phone"
                                    type="tel"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-dark-bg dark:text-white"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Bio
                                </label>
                                <textarea
                                    v-model="profileForm.bio"
                                    rows="4"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-dark-bg dark:text-white"
                                    placeholder="Tell us about yourself..."
                                ></textarea>
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="profileForm.processing"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {{ profileForm.processing ? 'Saving...' : 'Save Changes' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Change Password -->
                    <div class="bg-white dark:bg-dark-surface rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Change Password</h2>
                        
                        <form @submit.prevent="changePassword" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Current Password
                                </label>
                                <input
                                    v-model="passwordForm.current_password"
                                    type="password"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-dark-bg dark:text-white"
                                    required
                                />
                                <div v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-600">
                                    {{ passwordForm.errors.current_password }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    New Password
                                </label>
                                <input
                                    v-model="passwordForm.new_password"
                                    type="password"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-dark-bg dark:text-white"
                                    required
                                />
                                <div v-if="passwordForm.errors.new_password" class="mt-1 text-sm text-red-600">
                                    {{ passwordForm.errors.new_password }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Confirm New Password
                                </label>
                                <input
                                    v-model="passwordForm.new_password_confirmation"
                                    type="password"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-dark-bg dark:text-white"
                                    required
                                />
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="passwordForm.processing"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {{ passwordForm.processing ? 'Changing...' : 'Change Password' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
