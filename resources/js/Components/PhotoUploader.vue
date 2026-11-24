<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    issue: Object,
    maxPhotos: {
        type: Number,
        default: 10
    }
})

const emit = defineEmits(['uploaded'])

const selectedFiles = ref([])
const previews = ref([])
const uploading = ref(false)
const dragOver = ref(false)

// Handle file selection
const onFileSelect = (event) => {
    const files = Array.from(event.target.files)
    addFiles(files)
}

// Handle drag and drop
const onDrop = (event) => {
    event.preventDefault()
    dragOver.value = false
    
    const files = Array.from(event.dataTransfer.files).filter(file => 
        file.type.startsWith('image/')
    )
    
    addFiles(files)
}

const onDragOver = (event) => {
    event.preventDefault()
    dragOver.value = true
}

const onDragLeave = () => {
    dragOver.value = false
}

// Add files to selection
const addFiles = (files) => {
    // Check max photos limit
    if (selectedFiles.value.length + files.length > props.maxPhotos) {
        alert(`You can only upload ${props.maxPhotos} photos maximum`)
        return
    }

    files.forEach(file => {
        selectedFiles.value.push(file)
        
        // Create preview
        const reader = new FileReader()
        reader.onload = (e) => {
            previews.value.push({
                file,
                url: e.target.result,
                caption: ''
            })
        }
        reader.readAsDataURL(file)
    })
}

// Remove file from selection
const removeFile = (index) => {
    selectedFiles.value.splice(index, 1)
    previews.value.splice(index, 1)
}

// Upload photos
const upload = async () => {
    if (selectedFiles.value.length === 0) return

    uploading.value = true

    const formData = new FormData()
    selectedFiles.value.forEach((file, index) => {
        formData.append(`photos[${index}]`, file)
        formData.append(`captions[${index}]`, previews.value[index].caption || '')
    })

    try {
        const response = await fetch(`/api/issues/${props.issue.id}/photos`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })

        if (response.ok) {
            const data = await response.json()
            emit('uploaded', data.photos)
            
            // Clear selection
            selectedFiles.value = []
            previews.value = []
            
            // Show success message
            alert(data.message)
        } else {
            alert('Upload failed. Please try again.')
        }
    } catch (error) {
        console.error('Upload error:', error)
        alert('Upload failed. Please try again.')
    } finally {
        uploading.value = false
    }
}

const canUpload = computed(() => selectedFiles.value.length > 0 && !uploading.value)
</script>

<template>
    <div class="space-y-4">
        <!-- Drop Zone -->
        <div
            @drop="onDrop"
            @dragover="onDragOver"
            @dragleave="onDragLeave"
            class="border-2 border-dashed rounded-lg p-8 text-center transition-colors"
            :class="[
                dragOver 
                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' 
                    : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500'
            ]"
        >
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                <label class="cursor-pointer text-blue-600 dark:text-blue-400 hover:underline">
                    <span>Click to upload</span>
                    <input
                        type="file"
                        class="hidden"
                        multiple
                        accept="image/*"
                        @change="onFileSelect"
                        :disabled="uploading"
                    />
                </label>
                or drag and drop
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                PNG, JPG, WEBP up to 10MB (max {{ maxPhotos }} photos)
            </p>
        </div>

        <!-- Preview Grid -->
        <div v-if="previews.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div
                v-for="(preview, index) in previews"
                :key="index"
                class="relative group"
            >
                <img
                    :src="preview.url"
                    :alt="`Preview ${index + 1}`"
                    class="w-full h-32 object-cover rounded-lg"
                />
                
                <!-- Remove button -->
                <button
                    @click="removeFile(index)"
                    class="absolute top-2 right-2 p-1 bg-red-600 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
                    :disabled="uploading"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Caption input -->
                <input
                    v-model="preview.caption"
                    type="text"
                    placeholder="Add caption (optional)"
                    class="mt-2 w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded dark:bg-dark-bg dark:text-white"
                    :disabled="uploading"
                />
            </div>
        </div>

        <!-- Upload Button -->
        <div v-if="previews.length > 0" class="flex justify-end">
            <button
                @click="upload"
                :disabled="!canUpload"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span v-if="uploading">Uploading...</span>
                <span v-else>Upload {{ selectedFiles.length }} Photo{{ selectedFiles.length > 1 ? 's' : '' }}</span>
            </button>
        </div>
    </div>
</template>
