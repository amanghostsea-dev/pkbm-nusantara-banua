<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    pembayarans: Object,
})

const showModal = ref(false)
const form = useForm({
    siswa_id: 1,
    bulan: 'Oktober',
    tahun: 2024,
    nominal: 150000,
    jenis: 'spp',
    status: 'lunas',
    tanggal_bayar: new Date().toISOString().slice(0, 10),
})

const submit = () => {
    form.post('/admin/pembayarans', {
        onSuccess: () => {
            showModal.value = false
            form.reset()
        },
    })
}
</script>

<template>
    <Head title="Manajemen Pembayaran PKBM" />

    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Manajemen Pembayaran & SPP</h1>
                    <p class="text-gray-500 text-sm">Pencatatan iuran SPP, buku, modul kesetaraan, dan seragam warga belajar</p>
                </div>
                <button
                    @click="showModal = true"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm shadow transition flex items-center gap-2"
                >
                    <span>+</span>
                    <span>Catat Pembayaran Baru</span>
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="py-3 px-4">Nama Siswa</th>
                            <th class="py-3 px-4">Periode</th>
                            <th class="py-3 px-4">Jenis</th>
                            <th class="py-3 px-4">Nominal</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Tgl Pembayaran</th>
                            <th class="py-3 px-4">Diterima Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <tr v-for="item in (pembayarans.data || pembayarans)" :key="item.id" class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4 font-semibold text-gray-900">{{ item.siswa?.nama_lengkap || 'Siswa' }}</td>
                            <td class="py-3 px-4 font-medium">{{ item.bulan }} {{ item.tahun }}</td>
                            <td class="py-3 px-4 uppercase text-xs font-bold text-gray-600">{{ item.jenis }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-gray-800">Rp {{ Number(item.nominal).toLocaleString('id-ID') }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold capitalize"
                                    :class="item.status === 'lunas' ? 'bg-emerald-100 text-emerald-800' : (item.status === 'cicil' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')">
                                    {{ item.status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-500">{{ item.tanggal_bayar || '-' }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ item.penerima?.name || 'Petugas' }}</td>
                        </tr>
                        <tr v-if="!(pembayarans.data?.length || pembayarans?.length)">
                            <td colspan="7" class="py-8 text-center text-gray-400">Belum ada riwayat transaksi pembayaran.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Catat Pembayaran -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="font-bold text-gray-900 text-lg">Catat Pembayaran</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">ID Siswa</label>
                        <input v-model.number="form.siswa_id" required type="number" class="w-full px-3 py-2 border rounded-lg text-sm font-mono" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Bulan</label>
                            <input v-model="form.bulan" required type="text" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tahun</label>
                            <input v-model.number="form.tahun" required type="number" class="w-full px-3 py-2 border rounded-lg text-sm" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jenis</label>
                            <select v-model="form.jenis" required class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="spp">SPP Bulanan</option>
                                <option value="seragam">Seragam</option>
                                <option value="buku">Buku & Modul</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status</label>
                            <select v-model="form.status" required class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="lunas">Lunas</option>
                                <option value="cicil">Cicil</option>
                                <option value="belum">Belum Lunas</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nominal (Rp)</label>
                        <input v-model.number="form.nominal" required type="number" min="0" class="w-full px-3 py-2 border rounded-lg text-sm font-mono" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tanggal Bayar</label>
                        <input v-model="form.tanggal_bayar" required type="date" class="w-full px-3 py-2 border rounded-lg text-sm" />
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow transition">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Pembayaran' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
