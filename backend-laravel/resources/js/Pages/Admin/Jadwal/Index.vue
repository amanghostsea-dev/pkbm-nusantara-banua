<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    jadwals: Array,
    kelasList: Array,
    mapelList: Array,
    tutors: Array,
})

const showModal = ref(false)
const form = useForm({
    kelas_id: '',
    mata_pelajaran_id: '',
    tutor_id: '',
    hari: 'senin',
    jam_mulai: '08:00',
    jam_selesai: '10:00',
    ruangan: 'Ruang Belajar Utama',
})

const submit = () => {
    form.post('/admin/jadwals', {
        onSuccess: () => {
            showModal.value = false
            form.reset()
        },
    })
}
</script>

<template>
    <Head title="Manajemen Jadwal Belajar PKBM" />

    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Jadwal Pelajaran</h1>
                    <p class="text-gray-500 text-sm">Pengaturan alokasi waktu sesi belajar, tutor pengampu, dan ruang belajar</p>
                </div>
                <button
                    @click="showModal = true"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm shadow transition flex items-center gap-2"
                >
                    <span>+</span>
                    <span>Tambah Jadwal Sesi</span>
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="py-3 px-4">Hari</th>
                            <th class="py-3 px-4">Waktu</th>
                            <th class="py-3 px-4">Kelas / Rombel</th>
                            <th class="py-3 px-4">Mata Pelajaran</th>
                            <th class="py-3 px-4">Tutor Pengampu</th>
                            <th class="py-3 px-4">Ruangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <tr v-for="item in jadwals" :key="item.id" class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4 font-bold capitalize text-indigo-700">{{ item.hari }}</td>
                            <td class="py-3 px-4 font-mono font-medium">{{ item.jam_mulai }} - {{ item.jam_selesai }}</td>
                            <td class="py-3 px-4 font-semibold text-gray-900">{{ item.kelas?.nama_kelas || '-' }}</td>
                            <td class="py-3 px-4 font-medium text-gray-800">{{ item.mata_pelajaran?.nama_mapel || '-' }}</td>
                            <td class="py-3 px-4">{{ item.tutor?.nama_lengkap || '-' }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                    {{ item.ruangan || 'Daring / Lab' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!jadwals || jadwals.length === 0">
                            <td colspan="6" class="py-8 text-center text-gray-400">Belum ada sesi jadwal belajar terdaftar.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Tambah Jadwal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="font-bold text-gray-900 text-lg">Tambah Jadwal Belajar</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kelas</label>
                        <select v-model="form.kelas_id" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="" disabled>Pilih Kelas</option>
                            <option v-for="k in kelasList" :key="k.id" :value="k.id">{{ k.nama_kelas }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Mata Pelajaran</label>
                        <select v-model="form.mata_pelajaran_id" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="" disabled>Pilih Mapel</option>
                            <option v-for="m in mapelList" :key="m.id" :value="m.id">{{ m.nama_mapel }} ({{ m.kode_mapel }})</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tutor Pengampu</label>
                        <select v-model="form.tutor_id" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="" disabled>Pilih Tutor</option>
                            <option v-for="t in tutors" :key="t.id" :value="t.id">{{ t.nama_lengkap }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Hari</label>
                        <select v-model="form.hari" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="senin">Senin</option>
                            <option value="selasa">Selasa</option>
                            <option value="rabu">Rabu</option>
                            <option value="kamis">Kamis</option>
                            <option value="jumat">Jumat</option>
                            <option value="sabtu">Sabtu</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jam Mulai</label>
                            <input v-model="form.jam_mulai" required type="time" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jam Selesai</label>
                            <input v-model="form.jam_selesai" required type="time" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Ruangan / Tempat</label>
                        <input v-model="form.ruangan" placeholder="Contoh: Lab Digital / Ruang C-01" type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow transition">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Jadwal' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
