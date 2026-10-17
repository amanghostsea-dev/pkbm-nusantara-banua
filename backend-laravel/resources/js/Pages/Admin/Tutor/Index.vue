<script setup>
import { ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    tutors: Array,
})

const showModal = ref(false)
const form = useForm({
    nama_lengkap: '',
    email: '',
    password: 'password',
    nip: '',
    spesialisasi: '',
    no_telepon: '',
    alamat: '',
})

const submit = () => {
    form.post('/admin/tutors', {
        onSuccess: () => {
            showModal.value = false
            form.reset()
        },
    })
}

const deleteTutor = (item) => {
    if (confirm(`Yakin ingin menghapus data tutor ${item.nama_lengkap}? Akun login tutor juga akan terhapus.`)) {
        router.delete(`/admin/tutors/${item.id}`)
    }
}
</script>

<template>
    <Head title="Manajemen Tutor PKBM" />

    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Data Tutor & Pendidik Kesetaraan</h1>
                    <p class="text-gray-500 text-sm">Kelola profil guru/tutor pembimbing, akun sistem, dan bidang keahlian</p>
                </div>
                <button
                    @click="showModal = true"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm shadow transition flex items-center gap-2"
                >
                    <span>+</span>
                    <span>Tambah Tutor Baru</span>
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="py-3 px-4">Nama Lengkap & NIP</th>
                            <th class="py-3 px-4">Email Login</th>
                            <th class="py-3 px-4">Spesialisasi Mapel</th>
                            <th class="py-3 px-4">Kontak Telepon</th>
                            <th class="py-3 px-4">Kelas Diampu</th>
                            <th class="py-3 px-4">Total Sesi Jadwal</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <tr v-for="item in tutors" :key="item.id" class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-gray-900">{{ item.nama_lengkap }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ item.nip || 'NIP: -' }}</div>
                            </td>
                            <td class="py-3 px-4 font-mono text-gray-600">{{ item.user?.email || '-' }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-md text-xs font-semibold">
                                    {{ item.spesialisasi || 'Umum' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-600">{{ item.no_telepon || '-' }}</td>
                            <td class="py-3 px-4 font-bold text-gray-800">{{ item.kelas_count || 0 }} Kelas</td>
                            <td class="py-3 px-4">{{ item.jadwals_count || 0 }} Sesi</td>
                            <td class="py-3 px-4 text-right">
                                <button @click="deleteTutor(item)" class="text-xs text-rose-600 hover:underline font-semibold">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!tutors || tutors.length === 0">
                            <td colspan="7" class="py-8 text-center text-gray-400">Belum ada data tutor terdaftar.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Tambah Tutor -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="font-bold text-gray-900 text-lg">Tambah Tutor Baru</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Lengkap & Gelar</label>
                        <input v-model="form.nama_lengkap" placeholder="Contoh: Budi Santoso, S.Pd" required type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">NIP (Opsi)</label>
                            <input v-model="form.nip" placeholder="1985..." type="text" class="w-full px-3 py-2 border rounded-lg text-sm font-mono" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">No. Telepon / WA</label>
                            <input v-model="form.no_telepon" placeholder="08..." type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Email Akun (Login)</label>
                        <input v-model="form.email" placeholder="tutor@pkbm.sch.id" required type="email" class="w-full px-3 py-2 border rounded-lg text-sm" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Password Default</label>
                        <input v-model="form.password" required type="password" class="w-full px-3 py-2 border rounded-lg text-sm" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Spesialisasi Mapel</label>
                        <input v-model="form.spesialisasi" placeholder="Contoh: Matematika, IPA" type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Alamat</label>
                        <textarea v-model="form.alamat" rows="2" placeholder="Alamat domisili..." class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow transition">
                            {{ form.processing ? 'Menyimpan...' : 'Daftarkan Tutor' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
