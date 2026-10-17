<script setup>
defineProps({
    activities: Array,
})
</script>

<template>
    <div class="space-y-3">
        <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-gray-50 transition">
            <span class="p-2 rounded-full bg-blue-100 text-blue-600 text-xs">🎓</span>
            <div>
                <p class="text-xs font-medium text-gray-800">Siswa baru mendaftar</p>
                <p class="text-[10px] text-gray-400">Ahmad Fauzi · Paket C</p>
            </div>
        </div>
        <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-gray-50 transition">
            <span class="p-2 rounded-full bg-emerald-100 text-emerald-600 text-xs">📄</span>
            <div>
                <p class="text-xs font-medium text-gray-800">Raport Semester 1 digenerate</p>
                <p class="text-[10px] text-gray-400">Kelas Paket B-1</p>
            </div>
        </div>
        <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-gray-50 transition">
            <span class="p-2 rounded-full bg-amber-100 text-amber-600 text-xs">📲</span>
            <div>
                <p class="text-xs font-medium text-gray-800">Sesi Absensi QR dibuka</p>
                <p class="text-[10px] text-gray-400">Mata Pelajaran Matematika</p>
            </div>
        </div>
    </div>
</template>
