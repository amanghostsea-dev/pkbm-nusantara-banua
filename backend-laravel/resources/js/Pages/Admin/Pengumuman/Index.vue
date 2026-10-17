<script setup>
import { ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    pengumumans: Array,
})

const showModal = ref(false)
const form = useForm({
    judul: '',
    isi: '',
    target: 'semua',
})

const submit = () => {
    form.post('/admin/pengumumans', {
        onSuccess: () => {
            showModal.value = false
            form.reset()
        },
    })
}

const deletePengumuman = (item) => {
    if (confirm(`Hapus pengumuman "${item.judul}"?`)) {
        router.delete(`/admin/pengumumans/${item.id}`)
    }
}
</script>

<template>
    <Head title="Manajemen Pengumuman PKBM" />

    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Pusat Informasi & Pengumuman</h1>
                    <p class="text-gray-500 text-sm">Publikasikan pengumuman akademik untuk seluruh warga belajar, tutor, dan staf</p>
                </div>
                <button
                    @click="showModal = true"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm shadow transition flex items-center gap-2"
                >
                    <span>+</span>
                    <span>Buat Pengumuman Baru</span>
                </button>
            </div>

            <!-- List Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-for="item in pengumumans" :key="item.id" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700">
                                Target: {{ item.target }}
                            </span>
                            <span class="text-xs text-gray-400 font-medium">{{ item.published_at ? new Date(item.published_at).toLocaleDateString('id-ID') : '-' }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mt-2">{{ item.judul }}</h3>
                        <p class="text-sm text-gray-600 mt-1 whitespace-pre-line leading-relaxed">{{ item.isi }}</p>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                        <span>Oleh: {{ item.user?.name || 'Administrator' }}</span>
                        <button @click="deletePengumuman(item)" class="text-rose-600 hover:underline font-semibold">
                            Hapus
                        </button>
                    </div>
                </div>

                <div v-if="!pengumumans || pengumumans.length === 0" class="col-span-full py-12 text-center text-gray-400 bg-white rounded-2xl border border-gray-100">
                    Belum ada pengumuman yang diterbitkan.
                </div>
            </div>
        </div>

        <!-- Modal Buat Pengumuman -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="font-bold text-gray-900 text-lg">Buat Pengumuman</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Judul Pengumuman</label>
                        <input v-model="form.judul" placeholder="Jadwal Ujian Kesetaraan Semester Genap" required type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Target Audiens</label>
                        <select v-model="form.target" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="semua">Semua Warga PKBM</option>
                            <option value="siswa">Hanya Warga Belajar (Siswa)</option>
                            <option value="tutor">Hanya Tutor Pengajar</option>
                            <option value="admin">Internal Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Isi Pengumuman</label>
                        <textarea v-model="form.isi" rows="4" placeholder="Tuliskan detail informasi di sini..." required class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow transition">
                            {{ form.processing ? 'Menerbitkan...' : 'Terbitkan Sekarang' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
