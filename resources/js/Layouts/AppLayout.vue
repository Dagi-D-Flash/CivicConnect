<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- Navigation -->
        <nav class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link href="/" class="flex items-center space-x-3 group">
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-2 rounded-xl shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-105">
                                <MapPinIcon class="h-6 w-6 text-white" />
                            </div>
                            <div class="hidden sm:block">
                                <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                                    CivicConnect
                                </span>
                                <span class="text-xs text-gray-500 block -mt-1">.com</span>
                            </div>
                        </Link>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-2">
                        <Link 
                            href="/" 
                            :class="[
                                'flex items-center space-x-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                                $page.url === '/' 
                                    ? 'bg-blue-50 text-blue-700' 
                                    : 'text-gray-700 hover:bg-gray-100'
                            ]"
                        >
                            <MapIcon class="h-5 w-5" />
                            <span>Map</span>
                        </Link>

                        <Link 
                            href="/issues" 
                            :class="[
                                'flex items-center space-x-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                                $page.url.startsWith('/issues') 
                                    ? 'bg-purple-50 text-purple-700' 
                                    : 'text-gray-700 hover:bg-gray-100'
                            ]"
                        >
                            <DocumentTextIcon class="h-5 w-5" />
                            <span>Issues Board</span>
                        </Link>
                        
                        <Link 
                            href="/report" 
                            class="flex items-center space-x-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-5 py-2.5 rounded-lg text-sm font-medium shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
                        >
                            <PlusCircleIcon class="h-5 w-5" />
                            <span>Report Issue</span>
                        </Link>

                        <!-- Auth Links -->
                        <template v-if="$page.props.auth.user">
                            <!-- Logged In -->
                            <Link 
                                href="/dashboard" 
                                class="flex items-center space-x-2 text-gray-700 hover:bg-gray-100 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                            >
                                <UserCircleIcon class="h-5 w-5" />
                                <span>Dashboard</span>
                            </Link>

                            <!-- User Dropdown -->
                            <div class="relative">
                                <button
                                    @click="userMenuOpen = !userMenuOpen"
                                    class="flex items-center space-x-2 text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                                >
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-xs">
                                        {{ getInitials($page.props.auth.user.name) }}
                                    </div>
                                    <ChevronDownIcon class="h-4 w-4" />
                                </button>

                                <!-- Dropdown Menu -->
                                <Transition
                                    enter-active-class="transition ease-out duration-100"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95"
                                >
                                    <div v-show="userMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200">
                                        <div class="px-4 py-2 border-b border-gray-100">
                                            <p class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.name }}</p>
                                            <p class="text-xs text-gray-500">{{ $page.props.auth.user.email }}</p>
                                        </div>
                                        <Link href="/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Dashboard
                                        </Link>
                                        <Link href="/dashboard/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Profile
                                        </Link>
                                        <Link href="/dashboard/my-issues" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            My Issues
                                        </Link>
                                        <a href="/admin" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Admin Panel
                                        </a>
                                        <div class="border-t border-gray-100"></div>
                                        <Link href="/logout" method="post" as="button" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            Logout
                                        </Link>
                                    </div>
                                </Transition>
                            </div>
                        </template>
                        <template v-else>
                            <!-- Not Logged In -->
                            <Link 
                                href="/login" 
                                class="flex items-center space-x-2 text-gray-700 hover:bg-gray-100 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                            >
                                <ArrowRightOnRectangleIcon class="h-5 w-5" />
                                <span>Login</span>
                            </Link>
                            <Link 
                                href="/register" 
                                class="flex items-center space-x-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                            >
                                <UserPlusIcon class="h-5 w-5" />
                                <span>Register</span>
                            </Link>
                        </template>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex md:hidden items-center">
                        <button 
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="inline-flex items-center justify-center p-2 rounded-lg text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors"
                        >
                            <Bars3Icon v-if="!mobileMenuOpen" class="h-6 w-6" />
                            <XMarkIcon v-else class="h-6 w-6" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 -translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-1"
            >
                <div v-show="mobileMenuOpen" class="md:hidden border-t border-gray-200 bg-white">
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        <Link 
                            href="/" 
                            @click="mobileMenuOpen = false"
                            :class="[
                                'flex items-center space-x-3 px-3 py-3 rounded-lg text-base font-medium transition-colors',
                                $page.url === '/' 
                                    ? 'bg-blue-50 text-blue-700' 
                                    : 'text-gray-700 hover:bg-gray-100'
                            ]"
                        >
                            <MapIcon class="h-6 w-6" />
                            <span>Explore Map</span>
                        </Link>
                        
                        <Link 
                            href="/report" 
                            @click="mobileMenuOpen = false"
                            class="flex items-center space-x-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-3 py-3 rounded-lg text-base font-medium"
                        >
                            <PlusCircleIcon class="h-6 w-6" />
                            <span>Report Issue</span>
                        </Link>

                        <!-- Mobile Auth Links -->
                        <template v-if="$page.props.auth.user">
                            <Link 
                                href="/dashboard" 
                                @click="mobileMenuOpen = false"
                                class="flex items-center space-x-3 text-gray-700 hover:bg-gray-100 px-3 py-3 rounded-lg text-base font-medium"
                            >
                                <UserCircleIcon class="h-6 w-6" />
                                <span>Dashboard</span>
                            </Link>
                            <Link 
                                href="/dashboard/my-issues" 
                                @click="mobileMenuOpen = false"
                                class="flex items-center space-x-3 text-gray-700 hover:bg-gray-100 px-3 py-3 rounded-lg text-base font-medium"
                            >
                                <DocumentTextIcon class="h-6 w-6" />
                                <span>My Issues</span>
                            </Link>
                            <a 
                                href="/admin" 
                                class="flex items-center space-x-3 text-gray-700 hover:bg-gray-100 px-3 py-3 rounded-lg text-base font-medium"
                            >
                                <ShieldCheckIcon class="h-6 w-6" />
                                <span>Admin Panel</span>
                            </a>
                            <Link 
                                href="/logout" 
                                method="post" 
                                as="button"
                                @click="mobileMenuOpen = false"
                                class="flex items-center space-x-3 text-red-600 hover:bg-red-50 px-3 py-3 rounded-lg text-base font-medium w-full"
                            >
                                <ArrowRightOnRectangleIcon class="h-6 w-6" />
                                <span>Logout</span>
                            </Link>
                        </template>
                        <template v-else>
                            <Link 
                                href="/login" 
                                @click="mobileMenuOpen = false"
                                class="flex items-center space-x-3 text-gray-700 hover:bg-gray-100 px-3 py-3 rounded-lg text-base font-medium"
                            >
                                <ArrowRightOnRectangleIcon class="h-6 w-6" />
                                <span>Login</span>
                            </Link>
                            <Link 
                                href="/register" 
                                @click="mobileMenuOpen = false"
                                class="flex items-center space-x-3 bg-green-600 text-white px-3 py-3 rounded-lg text-base font-medium"
                            >
                                <UserPlusIcon class="h-6 w-6" />
                                <span>Register</span>
                            </Link>
                        </template>
                    </div>
                </div>
            </Transition>
        </nav>

        <!-- Page Content -->
        <main class="pb-12">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-white/80 backdrop-blur-md border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- About -->
                    <div>
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-1.5 rounded-lg">
                                <MapPinIcon class="h-5 w-5 text-white" />
                            </div>
                            <span class="font-bold text-gray-900">CivicConnect.com</span>
                        </div>
                        <p class="text-sm text-gray-600">
                            Making our communities better, one report at a time.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Quick Links</h3>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <Link href="/" class="text-gray-600 hover:text-blue-600 transition-colors">
                                    Explore Map
                                </Link>
                            </li>
                            <li>
                                <Link href="/report" class="text-gray-600 hover:text-blue-600 transition-colors">
                                    Report Issue
                                </Link>
                            </li>
                            <li>
                                <a href="/admin" class="text-gray-600 hover:text-blue-600 transition-colors">
                                    Admin Panel
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Get in Touch</h3>
                        <p class="text-sm text-gray-600 mb-2">
                            Have questions or feedback?
                        </p>
                        <a href="mailto:contact@civicconnect.com" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            contact@civicconnect.com
                        </a>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-500">
                        &copy; {{ new Date().getFullYear() }} CivicConnect.com. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { 
    MapPinIcon, 
    MapIcon, 
    PlusCircleIcon, 
    ShieldCheckIcon,
    Bars3Icon,
    XMarkIcon,
    UserCircleIcon,
    ChevronDownIcon,
    ArrowRightOnRectangleIcon,
    UserPlusIcon,
    DocumentTextIcon
} from '@heroicons/vue/24/outline';

const mobileMenuOpen = ref(false);
const userMenuOpen = ref(false);

const getInitials = (name) => {
    return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};
</script>
