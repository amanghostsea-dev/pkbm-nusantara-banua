<script setup>
defineProps({
    data: [Array, Object],
})
</script>

<template>
    <div class="w-full h-64 flex items-end justify-between gap-2 pt-6 pb-2 px-4">
        <!-- Elegant SVG Area / Line representation -->
        <svg viewBox="0 0 500 150" class="w-full h-full overflow-visible">
            <defs>
                <linearGradient id="gradient-blue" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.3"/>
                    <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0"/>
                </linearGradient>
            </defs>
            <path
                d="M 10 120 Q 80 40 150 70 T 300 50 T 450 30 L 450 150 L 10 150 Z"
                fill="url(#gradient-blue)"
            />
            <path
                d="M 10 120 Q 80 40 150 70 T 300 50 T 450 30"
                fill="none"
                stroke="#2563eb"
                stroke-width="3"
                stroke-linecap="round"
            />
            <circle cx="10" cy="120" r="4" fill="#2563eb"/>
            <circle cx="150" cy="70" r="4" fill="#2563eb"/>
            <circle cx="300" cy="50" r="4" fill="#2563eb"/>
            <circle cx="450" cy="30" r="4" fill="#2563eb"/>
        </svg>
    </div>
</template>
