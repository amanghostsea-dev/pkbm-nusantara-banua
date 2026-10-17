<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    siswa: Object,
    angkatans: Array,
})

const item = props.siswa.data || props.siswa

const form = useForm({
    angkatan_id: item.angkatan_id || '',
    nisn: item.nisn || '',
    nik: item.nik || '',
    nama_lengkap: item.nama_lengkap || '',
    tempat_lahir: item.tempat_lahir || '',
    tanggal_lahir: item.tanggal_lahir || '',
    jenis_kelamin: item.jenis_kelamin || 'L',
    alamat: item.alamat || '',
    no_telepon: item.no_telepon || '',
    nama_orang_tua: item.nama_orang_tua || '',
    status: item.status || 'aktif',
})

const submit = () => {
    form.put(`/admin/siswa/${item.id}`)
}
</script>

<template>
    <Head :title="`Edit Siswa - ${item.nama_lengkap}`" />

    <AdminLayout>
        <div class="p-6 max-w-4xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Edit Data Siswa</h1>
                    <p class="text-gray-500 text-sm">Perbarui informasi data warga belajar</p>
                </div>
                <Link :href="`/admin/siswa/${item.id}`" class="text-sm text-gray-600 hover:text-gray-900">
                    &larr; Kembali ke Detail
                </Link>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Angkatan / Program</label>
                            <select v-model="form.angkatan_id" required class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="" disabled>Pilih Angkatan</option>
                                <option v-for="a in angkatans" :key="a.id" :value="a.id">
                                    {{ a.nama_angkatan }} - {{ a.program?.nama_program }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Lengkap</label>
                            <input v-model="form.nama_lengkap" required type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">NISN</label>
                            <input v-model="form.nisn" required type="text" class="w-full px-3 py-2 border rounded-lg text-sm font-mono" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">NIK</label>
                            <input v-model="form.nik" type="text" class="w-full px-3 py-2 border rounded-lg text-sm font-mono" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tempat Lahir</label>
                            <input v-model="form.tempat_lahir" required type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tanggal Lahir</label>
                            <input v-model="form.tanggal_lahir" required type="date" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jenis Kelamin</label>
                            <select v-model="form.jenis_kelamin" required class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status Keaktifan</label>
                            <select v-model="form.status" required class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="aktif">Aktif</option>
                                <option value="lulus">Lulus</option>
                                <option value="dropout">Dropout</option>
                                <option value="cuti">Cuti</option>
                                <option value="pindah">Pindah</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Orang Tua / Wali</label>
                            <input v-model="form.nama_orang_tua" type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nomor Telepon</label>
                            <input v-model="form.no_telepon" type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Alamat Domisili</label>
                        <textarea v-model="form.alamat" rows="3" required class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <Link :href="`/admin/siswa/${item.id}`" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
                            Batal
                        </Link>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow transition">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
