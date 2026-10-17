<script setup>
defineProps({
    labels: Array,
    values: Array,
})
</script>

<template>
    <div class="flex flex-col items-center justify-center p-4">
        <svg viewBox="0 0 100 100" class="w-36 h-36 -rotate-90">
            <!-- Paket C -->
            <circle cx="50" cy="50" r="38" fill="none" stroke="#f59e0b" stroke-width="14" stroke-dasharray="120 240" stroke-dashoffset="0"/>
            <!-- Paket B -->
            <circle cx="50" cy="50" r="38" fill="none" stroke="#10b981" stroke-width="14" stroke-dasharray="70 240" stroke-dashoffset="-120"/>
            <!-- Paket A -->
            <circle cx="50" cy="50" r="38" fill="none" stroke="#3b82f6" stroke-width="14" stroke-dasharray="50 240" stroke-dashoffset="-190"/>
        </svg>
        <div class="mt-4 flex gap-3 text-xs">
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Paket A</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Paket B</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Paket C</span>
        </div>
    </div>
</template>
