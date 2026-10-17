<script setup>
import { ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    programs: Array,
})

const showModal = ref(false)
const form = useForm({
    jenjang: 'paket_c',
    nama_program: '',
    kode_program: '',
    total_semester: 6,
    deskripsi: '',
})

const submit = () => {
    form.post('/admin/programs', {
        onSuccess: () => {
            showModal.value = false
            form.reset()
        },
    })
}

const deleteProgram = (item) => {
    if (confirm(`Yakin ingin menghapus ${item.nama_program}?`)) {
        router.delete(`/admin/programs/${item.id}`)
    }
}
</script>

<template>
    <Head title="Manajemen Program Kesetaraan PKBM" />

    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Program Pendidikan Kesetaraan</h1>
                    <p class="text-gray-500 text-sm">Paket A (Setara SD), Paket B (Setara SMP), dan Paket C (Setara SMA)</p>
                </div>
                <button
                    @click="showModal = true"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm shadow transition flex items-center gap-2"
                >
                    <span>+</span>
                    <span>Tambah Program Baru</span>
                </button>
            </div>

            <!-- Program Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div v-for="item in programs" :key="item.id" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-4 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                                :class="item.jenjang === 'paket_a' ? 'bg-blue-100 text-blue-800' : (item.jenjang === 'paket_b' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800')">
                                {{ item.jenjang.replace('_', ' ') }}
                            </span>
                            <span class="font-mono text-xs font-bold text-gray-400">{{ item.kode_program }}</span>
                        </div>
                        <h3 class="text-xl font-extrabold text-gray-900 mt-3">{{ item.nama_program }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ item.deskripsi || 'Program pendidikan kesetaraan resmi Kementerian Pendidikan.' }}</p>
                    </div>

                    <div class="space-y-3 pt-4 border-t border-gray-100 text-sm">
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Durasi Program:</span>
                            <span class="font-bold text-gray-800">{{ item.total_semester }} Semester</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Jumlah Angkatan:</span>
                            <span class="font-bold text-indigo-600">{{ item.angkatans_count || 0 }} Angkatan</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Mata Pelajaran:</span>
                            <span class="font-bold text-gray-800">{{ item.mata_pelajarans_count || 0 }} Mapel</span>
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button @click="deleteProgram(item)" class="text-xs text-rose-600 hover:underline font-semibold">
                            Hapus Program
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Program -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="font-bold text-gray-900 text-lg">Tambah Program Kesetaraan</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jenjang</label>
                        <select v-model="form.jenjang" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="paket_a">Paket A (Setara SD)</option>
                            <option value="paket_b">Paket B (Setara SMP)</option>
                            <option value="paket_c">Paket C (Setara SMA)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Program</label>
                        <input v-model="form.nama_program" placeholder="Pendidikan Kesetaraan Paket C" required type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kode Program</label>
                            <input v-model="form.kode_program" placeholder="PKC" required type="text" class="w-full px-3 py-2 border rounded-lg text-sm font-mono" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Total Semester</label>
                            <input v-model.number="form.total_semester" required type="number" min="1" max="12" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Deskripsi Singkat</label>
                        <textarea v-model="form.deskripsi" rows="2" placeholder="Keterangan kurikulum atau profil lulusan..." class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow transition">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Program' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
