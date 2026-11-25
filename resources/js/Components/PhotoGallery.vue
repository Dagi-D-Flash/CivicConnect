<script setup>
import { ref, computed } from 'vue'
import Lightbox from './Lightbox.vue'

const props = defineProps({
    photos: {
        type: Array,
        required: true
    },
    auth: Object,
    editable: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['delete', 'update', 'reorder'])

const lightboxOpen = ref(false)
const currentIndex = ref(0)
const editingCaption = ref(null)
const captionText = ref('')

// Open lightbox
const openLightbox = (index) => {
    currentIndex.value = index
    lightboxOpen.value = true
}

// Close lightbox
const closeLightbox = () => {
    lightboxOpen.value = false
}

// Navigate lightbox
const nextPhoto = () => {
    if (currentIndex.value < props.photos.length - 1) {
        currentIndex.value++
    }
}

const prevPhoto = () => {
    if (currentIndex.value > 0) {
        currentIndex.value--
    }
}

// Edit caption
const startEditCaption = (photo) => {
    editingCaption.value = photo.id
    captionText.value = photo.caption || ''
}

const saveCaption = (photo) => {
    emit('update', {
        id: photo.id,
        caption: captionText.value
    })
    editingCaption.value = null
}

const cancelEdit = () => {
    editingCaption.value = null
    captionText.value = ''
}

// Delete photo
const deletePhoto = (photo) => {
    if (confirm('Are you sure you want to delete this photo?')) {
        emit('delete', photo.id)
    }
}

// Current photo for lightbox
const currentPhoto = computed(() => props.photos[currentIndex.value])
</script>

<template>
    <div>
        <!-- Gallery Grid -->
        <div v-if="photos.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div
                v-for="(photo, index) in photos"
                :key="photo.id"
                class="relative group cursor-pointer"
                @click="openLightbox(index)"
            >
                <img
                    :src="photo.thumbnail_url"
                    :alt="photo.caption || `Photo ${index + 1}`"
                    class="w-full h-48 object-cover rounded-lg transition-transform group-hover:scale-105"
                />
                
                <!-- Overlay on hover -->
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity rounded-lg flex items-center justify-center">
                    <svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                    </svg>
                </div>

                <!-- Caption -->
                <div v-if="photo.caption" class="mt-2">
                    <p v-if="editingCaption !== photo.id" class="text-sm text-gray-700 dark:text-gray-300">
                        {{ photo.caption }}
                    </p>
                    <div v-else class="space-y-2" @click.stop>
                        <input
                            v-model="captionText"
                            type="text"
                            class="w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded dark:bg-dark-bg dark:text-white"
                        />
                        <div class="flex space-x-2">
                            <button
                                @click="saveCaption(photo)"
                                class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Save
                            </button>
                            <button
                                @click="cancelEdit"
                                class="px-2 py-1 text-xs bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-400 dark:hover:bg-gray-600"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Edit buttons (only if editable) -->
                <div
                    v-if="editable && auth.user"
                    class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity flex space-x-2"
                    @click.stop
                >
                    <button
                        @click="startEditCaption(photo)"
                        class="p-1.5 bg-blue-600 text-white rounded-full hover:bg-blue-700"
                        title="Edit caption"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </button>
                    <button
                        @click="deletePhoto(photo)"
                        class="p-1.5 bg-red-600 text-white rounded-full hover:bg-red-700"
                        title="Delete photo"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-else class="text-center py-12 text-gray-500 dark:text-gray-400">
            <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p>No photos yet</p>
        </div>

        <!-- Lightbox -->
        <Lightbox
            v-if="lightboxOpen"
            :photo="currentPhoto"
            :total="photos.length"
            :current="currentIndex + 1"
            @close="closeLightbox"
            @next="nextPhoto"
            @prev="prevPhoto"
        />
    </div>
</template>
