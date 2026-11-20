<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
    photo: Object
})

const emit = defineEmits(['close', 'crop'])

const canvas = ref(null)
const cropArea = ref({
    x: 0,
    y: 0,
    width: 200,
    height: 200
})

const isDragging = ref(false)
const isResizing = ref(false)
const dragStart = ref({ x: 0, y: 0 })
const imageLoaded = ref(false)

let img = null

onMounted(() => {
    img = new Image()
    img.crossOrigin = 'anonymous'
    img.onload = () => {
        imageLoaded.value = true
        drawCanvas()
        
        // Center crop area
        cropArea.value = {
            x: (img.width - 200) / 2,
            y: (img.height - 200) / 2,
            width: 200,
            height: 200
        }
        drawCanvas()
    }
    img.src = props.photo.url
})

const drawCanvas = () => {
    if (!canvas.value || !imageLoaded.value) return

    const ctx = canvas.value.getContext('2d')
    canvas.value.width = img.width
    canvas.value.height = img.height

    // Draw image
    ctx.drawImage(img, 0, 0)

    // Draw overlay
    ctx.fillStyle = 'rgba(0, 0, 0, 0.5)'
    ctx.fillRect(0, 0, canvas.value.width, canvas.value.height)

    // Clear crop area
    ctx.clearRect(cropArea.value.x, cropArea.value.y, cropArea.value.width, cropArea.value.height)
    ctx.drawImage(
        img,
        cropArea.value.x, cropArea.value.y, cropArea.value.width, cropArea.value.height,
        cropArea.value.x, cropArea.value.y, cropArea.value.width, cropArea.value.height
    )

    // Draw crop border
    ctx.strokeStyle = '#3b82f6'
    ctx.lineWidth = 2
    ctx.strokeRect(cropArea.value.x, cropArea.value.y, cropArea.value.width, cropArea.value.height)

    // Draw resize handles
    const handleSize = 10
    ctx.fillStyle = '#3b82f6'
    // Top-left
    ctx.fillRect(cropArea.value.x - handleSize/2, cropArea.value.y - handleSize/2, handleSize, handleSize)
    // Top-right
    ctx.fillRect(cropArea.value.x + cropArea.value.width - handleSize/2, cropArea.value.y - handleSize/2, handleSize, handleSize)
    // Bottom-left
    ctx.fillRect(cropArea.value.x - handleSize/2, cropArea.value.y + cropArea.value.height - handleSize/2, handleSize, handleSize)
    // Bottom-right
    ctx.fillRect(cropArea.value.x + cropArea.value.width - handleSize/2, cropArea.value.y + cropArea.value.height - handleSize/2, handleSize, handleSize)
}

const handleMouseDown = (e) => {
    const rect = canvas.value.getBoundingClientRect()
    const x = e.clientX - rect.left
    const y = e.clientY - rect.top

    // Check if clicked inside crop area
    if (
        x >= cropArea.value.x &&
        x <= cropArea.value.x + cropArea.value.width &&
        y >= cropArea.value.y &&
        y <= cropArea.value.y + cropArea.value.height
    ) {
        isDragging.value = true
        dragStart.value = { x: x - cropArea.value.x, y: y - cropArea.value.y }
    }
}

const handleMouseMove = (e) => {
    if (!isDragging.value) return

    const rect = canvas.value.getBoundingClientRect()
    const x = e.clientX - rect.left
    const y = e.clientY - rect.top

    cropArea.value.x = Math.max(0, Math.min(x - dragStart.value.x, img.width - cropArea.value.width))
    cropArea.value.y = Math.max(0, Math.min(y - dragStart.value.y, img.height - cropArea.value.height))

    drawCanvas()
}

const handleMouseUp = () => {
    isDragging.value = false
    isResizing.value = false
}

const applyCrop = () => {
    emit('crop', {
        x: Math.round(cropArea.value.x),
        y: Math.round(cropArea.value.y),
        width: Math.round(cropArea.value.width),
        height: Math.round(cropArea.value.height)
    })
}
</script>

<template>
    <div class="fixed inset-0 z-50 bg-black bg-opacity-90 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-dark-surface rounded-lg max-w-4xl w-full p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                Crop Photo
            </h2>

            <div class="mb-4 overflow-auto max-h-[60vh] bg-gray-100 dark:bg-gray-900 rounded">
                <canvas
                    ref="canvas"
                    @mousedown="handleMouseDown"
                    @mousemove="handleMouseMove"
                    @mouseup="handleMouseUp"
                    @mouseleave="handleMouseUp"
                    class="cursor-move"
                ></canvas>
            </div>

            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Drag to move • Arrow keys for precision
                </p>

                <div class="flex space-x-3">
                    <button
                        @click="emit('close')"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                    <button
                        @click="applyCrop"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        Apply Crop
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
