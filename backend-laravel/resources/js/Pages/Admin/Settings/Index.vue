<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    settings: Object,
})

const form = useForm({
    nama_pkbm: props.settings?.nama_pkbm || 'PKBM Nusantara Banua',
    npsn: props.settings?.npsn || 'P9908123',
    kepala_pkbm: props.settings?.kepala_pkbm || 'Drs. H. Mulyadi, M.Pd',
    alamat: props.settings?.alamat || 'Jl. Pendidikan Kesetaraan No. 45, Jakarta Pusat',
    no_telepon: props.settings?.no_telepon || '(021) 7890-1234',
    email: props.settings?.email || 'info@pkbm.sch.id',
    tahun_ajaran_aktif: props.settings?.tahun_ajaran_aktif || '2024/2025',
    semester_aktif: props.settings?.semester_aktif || '1',
})

const submit = () => {
    form.post('/admin/settings')
}
</script>

<template>
    <Head title="Pengaturan Lembaga PKBM" />

    <AdminLayout>
        <div class="p-6 max-w-4xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Identitas & Pengaturan PKBM</h1>
                <p class="text-gray-500 text-sm">Konfigurasi data pokok pendidikan, NPSN resmi, dan pejabat penandatangan raport/ijazah</p>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Resmi Lembaga PKBM</label>
                            <input v-model="form.nama_pkbm" required type="text" class="w-full px-4 py-2.5 border rounded-xl text-sm" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nomor Pokok Sekolah Nasional (NPSN)</label>
                            <input v-model="form.npsn" required type="text" class="w-full px-4 py-2.5 border rounded-xl text-sm font-mono" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Kepala PKBM / Penanggung Jawab</label>
                            <input v-model="form.kepala_pkbm" required type="text" class="w-full px-4 py-2.5 border rounded-xl text-sm" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tahun Ajaran Berjalan</label>
                            <input v-model="form.tahun_ajaran_aktif" required type="text" class="w-full px-4 py-2.5 border rounded-xl text-sm" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Semester Aktif</label>
                            <select v-model="form.semester_aktif" required class="w-full px-4 py-2.5 border rounded-xl text-sm">
                                <option value="1">Semester Ganjil (1)</option>
                                <option value="2">Semester Genap (2)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nomor Telepon Kantor</label>
                            <input v-model="form.no_telepon" required type="text" class="w-full px-4 py-2.5 border rounded-xl text-sm" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Email Resmi</label>
                            <input v-model="form.email" required type="email" class="w-full px-4 py-2.5 border rounded-xl text-sm" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Alamat Lengkap Lembaga</label>
                            <textarea v-model="form.alamat" rows="3" required class="w-full px-4 py-2.5 border rounded-xl text-sm"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t">
                        <button type="submit" :disabled="form.processing" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-md transition">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Pengaturan Lembaga' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
