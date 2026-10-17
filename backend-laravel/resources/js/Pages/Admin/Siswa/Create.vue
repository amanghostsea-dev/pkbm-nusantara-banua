<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    angkatans: Array,
})

const form = useForm({
    angkatan_id: '',
    nisn: '',
    nik: '',
    nama_lengkap: '',
    tempat_lahir: '',
    tanggal_lahir: '',
    jenis_kelamin: 'L',
    alamat: '',
    no_telepon: '',
    nama_orang_tua: '',
    status: 'aktif',
})

const submit = () => {
    form.post('/admin/siswa')
}
</script>

<template>
    <Head title="Pendaftaran Siswa Baru" />

    <AdminLayout>
        <div class="p-6 max-w-4xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Tambah Siswa Baru</h1>
                    <p class="text-gray-500 text-sm">Input data identitas dan pendaftaran siswa</p>
                </div>
                <Link href="/admin/siswa" class="text-sm text-gray-600 hover:text-gray-900">
                    &larr; Kembali
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
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">No. Telepon / WhatsApp</label>
                            <input v-model="form.no_telepon" type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Orang Tua / Wali</label>
                            <input v-model="form.nama_orang_tua" required type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status Siswa</label>
                            <select v-model="form.status" required class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="aktif">Aktif</option>
                                <option value="cuti">Cuti</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Alamat Lengkap</label>
                        <textarea v-model="form.alamat" required rows="2" class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <Link href="/admin/siswa" class="px-4 py-2 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow disabled:opacity-50"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Siswa' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
