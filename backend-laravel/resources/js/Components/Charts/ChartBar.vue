<script setup>
defineProps({
    data: [Array, Object],
})
</script>

<template>
    <div class="w-full h-48 flex items-end justify-around gap-2 p-4">
        <div class="flex flex-col items-center gap-1 w-8">
            <div class="w-full bg-blue-500 rounded-t h-28"></div>
            <span class="text-[10px] text-gray-400">MTK</span>
        </div>
        <div class="flex flex-col items-center gap-1 w-8">
            <div class="w-full bg-indigo-500 rounded-t h-36"></div>
            <span class="text-[10px] text-gray-400">IND</span>
        </div>
        <div class="flex flex-col items-center gap-1 w-8">
            <div class="w-full bg-emerald-500 rounded-t h-32"></div>
            <span class="text-[10px] text-gray-400">ING</span>
        </div>
        <div class="flex flex-col items-center gap-1 w-8">
            <div class="w-full bg-amber-500 rounded-t h-30"></div>
            <span class="text-[10px] text-gray-400">IPA</span>
        </div>
        <div class="flex flex-col items-center gap-1 w-8">
            <div class="w-full bg-purple-500 rounded-t h-34"></div>
            <span class="text-[10px] text-gray-400">IPS</span>
        </div>
    </div>
</template>
