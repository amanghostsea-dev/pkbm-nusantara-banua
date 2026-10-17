<script setup>
import { ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    angkatans: Array,
    programs: Array,
})

const showModal = ref(false)
const form = useForm({
    program_id: '',
    nama_angkatan: '',
    tahun_ajaran: '2024/2025',
    tanggal_mulai: new Date().toISOString().slice(0, 10),
    tanggal_selesai: '',
    status: 'aktif',
})

const submit = () => {
    form.post('/admin/angkatans', {
        onSuccess: () => {
            showModal.value = false
            form.reset()
        },
    })
}

const deleteAngkatan = (item) => {
    if (confirm(`Yakin ingin menghapus ${item.nama_angkatan}?`)) {
        router.delete(`/admin/angkatans/${item.id}`)
    }
}
</script>

<template>
    <Head title="Manajemen Angkatan PKBM" />

    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Manajemen Angkatan</h1>
                    <p class="text-gray-500 text-sm">Kelola tahun ajaran dan angkatan belajar per program kesetaraan</p>
                </div>
                <button
                    @click="showModal = true"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm shadow transition flex items-center gap-2"
                >
                    <span>+</span>
                    <span>Tambah Angkatan Baru</span>
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="py-3 px-4">Nama Angkatan</th>
                            <th class="py-3 px-4">Program</th>
                            <th class="py-3 px-4">Tahun Ajaran</th>
                            <th class="py-3 px-4">Jumlah Siswa</th>
                            <th class="py-3 px-4">Jumlah Kelas</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <tr v-for="item in angkatans" :key="item.id" class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4 font-semibold text-gray-800">{{ item.nama_angkatan }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md text-xs font-semibold">
                                    {{ item.program?.nama_program || 'Program' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-medium">{{ item.tahun_ajaran }}</td>
                            <td class="py-3 px-4 font-bold text-gray-800">{{ item.siswas_count || 0 }} Siswa</td>
                            <td class="py-3 px-4">{{ item.kelas_count || 0 }} Kelas</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize"
                                    :class="item.status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700'">
                                    {{ item.status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <button @click="deleteAngkatan(item)" class="text-xs text-rose-600 hover:underline font-semibold">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!angkatans || angkatans.length === 0">
                            <td colspan="7" class="py-8 text-center text-gray-400">Belum ada angkatan terdaftar.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Tambah Angkatan -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="font-bold text-gray-900 text-lg">Tambah Angkatan Baru</h3>
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

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Angkatan</label>
                        <input v-model="form.nama_angkatan" placeholder="Contoh: Angkatan 2024/2025" required type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tahun Ajaran</label>
                        <input v-model="form.tahun_ajaran" placeholder="2024/2025" required type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tgl Mulai</label>
                            <input v-model="form.tanggal_mulai" required type="date" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tgl Selesai (Opsi)</label>
                            <input v-model="form.tanggal_selesai" type="date" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status</label>
                        <select v-model="form.status" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                            <option value="tutup">Tutup</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow transition">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Angkatan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
