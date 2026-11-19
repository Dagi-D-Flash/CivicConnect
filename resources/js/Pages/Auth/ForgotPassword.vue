<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'

defineProps({
    status: String,
})

const form = useForm({
    email: '',
})

const submit = () => {
    form.post(route('password.email'))
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-dark-bg py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                    Reset your password
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                    Enter your email address and we'll send you a link to reset your password.
                </p>
            </div>

            <div v-if="status" class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                <p class="text-sm text-green-800 dark:text-green-200">
                    {{ status }}
                </p>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="submit">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Email Address
                    </label>
                    <input
                        id="email"
                        v-model="form.email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        required
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-dark-surface"
                        placeholder="you@example.com"
                    />
                    <div v-if="form.errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">
                        {{ form.errors.email }}
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing">Sending link...</span>
                        <span v-else>Send Reset Link</span>
                    </button>
                </div>

                <div class="text-center">
                    <Link :href="route('login')" class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">
                        Back to login
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>
