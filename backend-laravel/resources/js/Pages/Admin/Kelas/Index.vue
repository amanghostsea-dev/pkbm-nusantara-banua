<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    kelasList: Array,
    angkatans: Array,
    tutors: Array,
})

const showModal = ref(false)
const form = useForm({
    angkatan_id: '',
    tutor_id: '',
    nama_kelas: '',
    semester: 1,
    kapasitas: 30,
})

const submit = () => {
    form.post('/admin/kelas', {
        onSuccess: () => {
            showModal.value = false
            form.reset()
        },
    })
}
</script>

<template>
    <Head title="Manajemen Kelas PKBM" />

    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Manajemen Rombel / Kelas</h1>
                    <p class="text-gray-500 text-sm">Kelola rombongan belajar, wali kelas/tutor pendamping, dan kapasitas</p>
                </div>
                <button
                    @click="showModal = true"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm shadow transition flex items-center gap-2"
                >
                    <span>+</span>
                    <span>Tambah Kelas Baru</span>
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="py-3 px-4">Nama Kelas</th>
                            <th class="py-3 px-4">Program & Angkatan</th>
                            <th class="py-3 px-4">Semester</th>
                            <th class="py-3 px-4">Tutor Pendamping</th>
                            <th class="py-3 px-4">Kapasitas Siswa</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <tr v-for="item in kelasList" :key="item.id" class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4 font-semibold text-gray-900">{{ item.nama_kelas }}</td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-800">{{ item.angkatan?.program?.nama_program || 'Paket' }}</div>
                                <div class="text-xs text-gray-400">{{ item.angkatan?.nama_angkatan }}</div>
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-indigo-600">Semester {{ item.semester }}</td>
                            <td class="py-3 px-4 text-gray-800">{{ item.tutor?.nama_lengkap || 'Belum Ditentukan' }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-800">{{ item.siswas_count || 0 }}</span>
                                    <span class="text-gray-400 text-xs">/ {{ item.kapasitas }} Siswa</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <Link :href="`/admin/kelas/${item.id}`" class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-semibold transition">
                                    Kelola Siswa &rarr;
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="!kelasList || kelasList.length === 0">
                            <td colspan="6" class="py-8 text-center text-gray-400">Belum ada kelas terdaftar.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Tambah Kelas -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="font-bold text-gray-900 text-lg">Tambah Kelas / Rombel Baru</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Angkatan</label>
                        <select v-model="form.angkatan_id" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="" disabled>Pilih Angkatan</option>
                            <option v-for="a in angkatans" :key="a.id" :value="a.id">
                                {{ a.nama_angkatan }} ({{ a.tahun_ajaran }})
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Kelas</label>
                        <input v-model="form.nama_kelas" placeholder="Contoh: Paket C - Kelas 10 Mandiri" required type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Semester</label>
                            <input v-model.number="form.semester" required type="number" min="1" max="6" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kapasitas</label>
                            <input v-model.number="form.kapasitas" required type="number" min="5" max="100" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tutor Wali Kelas</label>
                        <select v-model="form.tutor_id" class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="">Pilih Tutor (Opsi)</option>
                            <option v-for="t in tutors" :key="t.id" :value="t.id">{{ t.nama_lengkap }}</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow transition">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Kelas' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
