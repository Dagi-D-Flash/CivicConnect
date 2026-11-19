<template>
    <AppLayout>
        <div class="py-6">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Report an Issue</h1>
                    <p class="mt-2 text-gray-600">Help improve your community by reporting problems</p>
                </div>

                <!-- Success Message -->
                <div v-if="form.recentlySuccessful" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">✅</span>
                        <div>
                            <h3 class="font-semibold text-green-900">Issue Reported Successfully!</h3>
                            <p class="text-sm text-green-700">Thank you for helping improve our community.</p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="bg-white rounded-lg shadow-lg p-6 space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Issue Title <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="e.g., Large pothole on Main Street"
                            required
                        />
                        <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="category"
                            v-model="form.category_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required
                        >
                            <option value="">Select a category</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.icon }} {{ category.name }}
                            </option>
                        </select>
                        <div v-if="form.errors.category_id" class="mt-1 text-sm text-red-600">{{ form.errors.category_id }}</div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Describe the issue in detail..."
                            required
                        ></textarea>
                        <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</div>
                    </div>

                    <!-- Photo Upload -->
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                            Photo (Optional)
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition">
                            <div class="space-y-1 text-center">
                                <div v-if="photoPreview" class="mb-4">
                                    <img :src="photoPreview" alt="Preview" class="mx-auto h-48 w-auto rounded-lg" />
                                    <button
                                        type="button"
                                        @click="removePhoto"
                                        class="mt-2 text-sm text-red-600 hover:text-red-800"
                                    >
                                        Remove photo
                                    </button>
                                </div>
                                <div v-else>
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="photo" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                            <span>Upload a photo</span>
                                            <input
                                                id="photo"
                                                type="file"
                                                class="sr-only"
                                                accept="image/*"
                                                @change="handlePhotoChange"
                                            />
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                </div>
                            </div>
                        </div>
                        <div v-if="form.errors.photo" class="mt-1 text-sm text-red-600">{{ form.errors.photo }}</div>
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Location <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-3">
                            <button
                                type="button"
                                @click="getCurrentLocation"
                                :disabled="loadingLocation"
                                class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                            >
                                <svg v-if="!loadingLocation" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span v-if="loadingLocation">Getting location...</span>
                                <span v-else>Use My Current Location</span>
                            </button>

                            <div v-if="form.latitude && form.longitude" class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm text-green-800">
                                    📍 Location captured: {{ form.latitude.toFixed(6) }}, {{ form.longitude.toFixed(6) }}
                                </p>
                            </div>

                            <!-- Mini Map for location selection -->
                            <div v-if="form.latitude && form.longitude" class="border border-gray-300 rounded-lg overflow-hidden">
                                <div id="location-map" style="height: 300px;"></div>
                            </div>
                        </div>
                        <div v-if="form.errors.latitude" class="mt-1 text-sm text-red-600">{{ form.errors.latitude }}</div>
                    </div>

                    <!-- Optional Contact Info -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information (Optional)</h3>
                        <p class="text-sm text-gray-600 mb-4">Provide your contact details if you'd like updates on this issue.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="reporter_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Your Name
                                </label>
                                <input
                                    id="reporter_name"
                                    v-model="form.reporter_name"
                                    type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="John Doe"
                                />
                            </div>
                            <div>
                                <label for="reporter_email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input
                                    id="reporter_email"
                                    v-model="form.reporter_email"
                                    type="email"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="john@example.com"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <Link href="/" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="form.processing">Submitting...</span>
                            <span v-else>Submit Report</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import L from 'leaflet';

const props = defineProps({
    categories: {
        type: Array,
        required: true
    }
});

const form = useForm({
    title: '',
    description: '',
    category_id: '',
    latitude: null,
    longitude: null,
    photo: null,
    reporter_name: '',
    reporter_email: '',
});

const photoPreview = ref(null);
const loadingLocation = ref(false);
let locationMap = null;

const handlePhotoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.photo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removePhoto = () => {
    form.photo = null;
    photoPreview.value = null;
};

const getCurrentLocation = () => {
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by your browser');
        return;
    }

    loadingLocation.value = true;

    navigator.geolocation.getCurrentPosition(
        (position) => {
            form.latitude = position.coords.latitude;
            form.longitude = position.coords.longitude;
            loadingLocation.value = false;
            
            // Initialize map after location is set
            setTimeout(() => {
                initLocationMap();
            }, 100);
        },
        (error) => {
            console.error('Error getting location:', error);
            alert('Unable to get your location. Please try again or enter it manually.');
            loadingLocation.value = false;
        }
    );
};

const initLocationMap = () => {
    if (!form.latitude || !form.longitude) return;

    // Remove existing map if any
    if (locationMap) {
        locationMap.remove();
    }

    // Create new map
    locationMap = L.map('location-map').setView([form.latitude, form.longitude], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(locationMap);

    // Add draggable marker
    const marker = L.marker([form.latitude, form.longitude], {
        draggable: true
    }).addTo(locationMap);

    marker.on('dragend', (event) => {
        const position = event.target.getLatLng();
        form.latitude = position.lat;
        form.longitude = position.lng;
    });
};

const submit = () => {
    form.post(route('issues.store'), {
        onSuccess: () => {
            form.reset();
            photoPreview.value = null;
            if (locationMap) {
                locationMap.remove();
                locationMap = null;
            }
        },
    });
};

watch(() => [form.latitude, form.longitude], () => {
    if (form.latitude && form.longitude && !locationMap) {
        setTimeout(() => {
            initLocationMap();
        }, 100);
    }
});
</script>
