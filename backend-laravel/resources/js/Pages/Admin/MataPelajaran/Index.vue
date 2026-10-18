<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    mapelList: Array,
    programs: Array,
})

const showModal = ref(false)
const form = useForm({
    program_id: '',
    kode_mapel: '',
    nama_mapel: '',
    semester: 1,
    kkm: 65,
    jam_per_minggu: 2,
})

const submit = () => {
    form.post('/admin/mata-pelajarans', {
        onSuccess: () => {
            showModal.value = false
            form.reset()
        },
    })
}
</script>

<template>
    <Head title="Manajemen Mata Pelajaran PKBM" />

    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Mata Pelajaran & Kurikulum</h1>
                    <p class="text-gray-500 text-sm">Kelola daftar mata pelajaran, KKM, dan beban jam belajar per program</p>
                </div>
                <button
                    @click="showModal = true"
                    class="w-full sm:w-auto justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-semibold rounded-lg text-sm shadow transition flex items-center gap-2"
                >
                    <span>+</span>
                    <span>Tambah Mata Pelajaran</span>
                </button>
            </div>

            <!-- Table with Responsive Overflow -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[650px]">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4">Kode</th>
                                <th class="py-3 px-4">Mata Pelajaran</th>
                                <th class="py-3 px-4">Program</th>
                                <th class="py-3 px-4">Semester</th>
                                <th class="py-3 px-4">KKM</th>
                                <th class="py-3 px-4">Jam/Minggu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            <tr v-for="item in mapelList" :key="item.id" class="hover:bg-gray-50/80 transition">
                                <td class="py-3 px-4 font-mono font-bold text-indigo-600">{{ item.kode_mapel }}</td>
                                <td class="py-3 px-4 font-semibold text-gray-900">{{ item.nama_mapel }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md text-xs font-semibold">
                                        {{ item.program?.nama_program || 'Umum' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">Semester {{ item.semester }}</td>
                                <td class="py-3 px-4 font-bold text-emerald-600">{{ item.kkm }}</td>
                                <td class="py-3 px-4">{{ item.jam_per_minggu }} Jam</td>
                            </tr>
                            <tr v-if="!mapelList || mapelList.length === 0">
                                <td colspan="6" class="py-8 text-center text-gray-400">Belum ada mata pelajaran terdaftar.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Mapel -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="font-bold text-gray-900 text-lg">Tambah Mata Pelajaran</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Program</label>
                        <select v-model="form.program_id" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="" disabled>Pilih Program</option>
                            <option v-for="p in programs" :key="p.id" :value="p.id">{{ p.nama_program }}</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kode Mapel</label>
                            <input v-model="form.kode_mapel" placeholder="MATC" required type="text" class="w-full px-3 py-2 border rounded-lg text-sm font-mono" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Semester</label>
                            <input v-model.number="form.semester" required type="number" min="1" max="6" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Mata Pelajaran</label>
                        <input v-model="form.nama_mapel" placeholder="Matematika Terapan" required type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">KKM Standar</label>
                            <input v-model.number="form.kkm" required type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jam Belajar / Pekan</label>
                            <input v-model.number="form.jam_per_minggu" required type="number" min="1" max="20" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow transition">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Mapel' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
