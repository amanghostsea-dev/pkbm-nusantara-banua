<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    kelas: Object,
    availableSiswa: Array,
})

const showAddModal = ref(false)
const selectedSiswaIds = ref([])

const allSelected = computed(() => {
    return props.availableSiswa?.length > 0 && selectedSiswaIds.value.length === props.availableSiswa.length
})

const toggleSelectAll = () => {
    if (allSelected.value) {
        selectedSiswaIds.value = []
    } else {
        selectedSiswaIds.value = (props.availableSiswa || []).map(s => s.id)
    }
}

const form = useForm({
    siswa_ids: [],
})

const submit = () => {
    if (selectedSiswaIds.value.length === 0) {
        alert('Mohon pilih minimal satu siswa untuk dimasukkan ke kelas.')
        return
    }

    form.siswa_ids = selectedSiswaIds.value
    form.post(`/admin/kelas/${props.kelas.id}/siswa`, {
        onSuccess: () => {
            showAddModal.value = false
            selectedSiswaIds.value = []
            form.reset()
        },
    })
}
</script>

<template>
    <Head :title="`Detail Kelas - ${kelas.nama_kelas}`" />

    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ kelas.nama_kelas }}</h1>
                    <p class="text-gray-500 text-sm">
                        {{ kelas.angkatan?.program?.nama_program }} • Angkatan: {{ kelas.angkatan?.nama_angkatan }} • Semester {{ kelas.semester }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <Link href="/admin/kelas" class="px-3.5 py-2 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-xl text-sm font-semibold transition">
                        &larr; Kembali
                    </Link>
                    <button
                        @click="showAddModal = true"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow transition flex items-center gap-2"
                    >
                        <span>👥</span>
                        <span>Tambah Siswa Kolektif</span>
                    </button>
                </div>
            </div>

            <!-- Stats Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                        👨‍🏫
                    </div>
                    <div>
                        <div class="text-xs uppercase font-bold text-gray-400">Tutor Pengajar</div>
                        <div class="text-lg font-bold text-gray-800">{{ kelas.tutor?.nama_lengkap || 'Belum Ada' }}</div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                        👥
                    </div>
                    <div>
                        <div class="text-xs uppercase font-bold text-gray-400">Total Warga Belajar</div>
                        <div class="text-lg font-bold text-gray-800">{{ kelas.siswas?.length || 0 }} / {{ kelas.kapasitas }} Siswa</div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                        📚
                    </div>
                    <div>
                        <div class="text-xs uppercase font-bold text-gray-400">Status Rombel</div>
                        <div class="text-lg font-bold text-emerald-600">{{ kelas.is_active ? 'Aktif Berjalan' : 'Non-aktif' }}</div>
                    </div>
                </div>
            </div>

            <!-- Siswa Terdaftar Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">Daftar Warga Belajar di Kelas Ini</h3>
                    <span class="text-xs font-semibold text-gray-500">{{ kelas.siswas?.length || 0 }} Siswa Terdaftar</span>
                </div>

                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="py-3 px-4">NISN</th>
                            <th class="py-3 px-4">Nama Lengkap</th>
                            <th class="py-3 px-4">L/P</th>
                            <th class="py-3 px-4">Tgl Masuk Kelas</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <tr v-for="s in (kelas.siswas || [])" :key="s.id" class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4 font-mono font-medium">{{ s.nisn }}</td>
                            <td class="py-3 px-4 font-semibold text-gray-800">{{ s.nama_lengkap }}</td>
                            <td class="py-3 px-4">{{ s.jenis_kelamin }}</td>
                            <td class="py-3 px-4 text-gray-500">{{ s.pivot?.tanggal_masuk || '-' }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 capitalize">
                                    {{ s.pivot?.status || 'aktif' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <Link :href="`/admin/siswa/${s.id}`" class="text-xs text-indigo-600 hover:underline font-semibold">
                                    Lihat Profil &rarr;
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="!(kelas.siswas?.length)">
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                Belum ada siswa yang dimasukkan ke kelas ini. Klik tombol "Tambah Siswa Kolektif".
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL TAMBAH SISWA KOLEKTIF KE KELAS -->
        <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 space-y-4 max-h-[85vh] flex flex-col">
                <div class="flex items-center justify-between border-b pb-3">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Masukkan Siswa ke Rombel Kolektif</h3>
                        <p class="text-xs text-gray-500">Pilih satu, beberapa, atau seluruh siswa sekaligus</p>
                    </div>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <div v-if="availableSiswa && availableSiswa.length > 0" class="flex items-center justify-between bg-slate-50 p-3 rounded-xl text-xs">
                    <label class="flex items-center gap-2 font-bold cursor-pointer text-gray-700">
                        <input type="checkbox" :checked="allSelected" @change="toggleSelectAll" class="rounded text-indigo-600" />
                        <span>Pilih Semua Siswa Angkatan Ini ({{ availableSiswa.length }} Siswa)</span>
                    </label>
                    <span class="font-bold text-indigo-600">{{ selectedSiswaIds.length }} Terpilih</span>
                </div>

                <div class="flex-1 overflow-y-auto border border-gray-100 rounded-2xl divide-y">
                    <label
                        v-for="s in availableSiswa"
                        :key="s.id"
                        class="p-3 flex items-center justify-between hover:bg-gray-50 cursor-pointer transition text-xs"
                    >
                        <div class="flex items-center gap-3">
                            <input type="checkbox" :value="s.id" v-model="selectedSiswaIds" class="rounded text-indigo-600" />
                            <div>
                                <div class="font-bold text-gray-900">{{ s.nama_lengkap }}</div>
                                <div class="font-mono text-gray-400">NISN: {{ s.nisn }}</div>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-gray-100 text-gray-600">{{ s.jenis_kelamin }}</span>
                    </label>

                    <div v-if="!availableSiswa || availableSiswa.length === 0" class="p-8 text-center text-gray-400 text-xs">
                        Seluruh siswa aktif pada angkatan ini sudah masuk ke dalam kelas.
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition">
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="submit"
                        :disabled="form.processing || selectedSiswaIds.length === 0"
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-xl text-sm font-bold shadow-md transition"
                    >
                        {{ form.processing ? 'Menambahkan...' : `Tambahkan (${selectedSiswaIds.length}) Siswa Sekaligus` }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
