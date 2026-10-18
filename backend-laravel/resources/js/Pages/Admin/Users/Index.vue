<script setup>
import { ref } from 'vue'
import { Head, router, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    users: Object,
    filters: Object,
    stats: Object,
})

const page = usePage()
const currentUserId = page.props.auth?.user?.id

// Filter State
const search = ref(props.filters?.search || '')
const selectedRole = ref(props.filters?.role || '')
const selectedStatus = ref(props.filters?.status || '')

function applyFilter() {
    router.get('/admin/users', {
        search: search.value,
        role: selectedRole.value,
        status: selectedStatus.value,
    }, { preserveState: true })
}

function resetFilter() {
    search.value = ''
    selectedRole.value = ''
    selectedStatus.value = ''
    router.get('/admin/users')
}

// Modal State: Create User
const showCreateModal = ref(false)
const createRoleType = ref('staff_admin') // 'super_admin' | 'staff_admin' | 'tutor' | 'siswa'
const createForm = ref({
    name: '',
    email: '',
    password: 'password',
    role: 'admin',
    is_super_admin: false,
    phone: '',
    is_active: true,
})
const showPassword = ref(false)

function openCreateModal() {
    createRoleType.value = 'staff_admin'
    createForm.value = {
        name: '',
        email: '',
        password: 'password',
        role: 'admin',
        is_super_admin: false,
        phone: '',
        is_active: true,
    }
    showCreateModal.value = true
}

function generateRandomPassword() {
    const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#'
    let res = ''
    for (let i = 0; i < 8; i++) {
        res += chars.charAt(Math.floor(Math.random() * chars.length))
    }
    createForm.value.password = res
}

function submitCreate() {
    // Map role type to backend structure
    if (createRoleType.value === 'super_admin') {
        createForm.value.role = 'admin'
        createForm.value.is_super_admin = true
    } else if (createRoleType.value === 'staff_admin') {
        createForm.value.role = 'admin'
        createForm.value.is_super_admin = false
    } else if (createRoleType.value === 'tutor') {
        createForm.value.role = 'tutor'
        createForm.value.is_super_admin = false
    } else {
        createForm.value.role = 'siswa'
        createForm.value.is_super_admin = false
    }

    router.post('/admin/users', createForm.value, {
        onSuccess: () => {
            showCreateModal.value = false
        }
    })
}

// Modal State: Edit User
const showEditModal = ref(false)
const editUser = ref(null)
const editRoleType = ref('staff_admin')
const editForm = ref({
    name: '',
    email: '',
    password: '',
    role: 'admin',
    is_super_admin: false,
    phone: '',
    is_active: true,
})

function openEditModal(user) {
    editUser.value = user
    if (user.role === 'admin' && user.is_super_admin) {
        editRoleType.value = 'super_admin'
    } else if (user.role === 'admin' && !user.is_super_admin) {
        editRoleType.value = 'staff_admin'
    } else if (user.role === 'tutor') {
        editRoleType.value = 'tutor'
    } else {
        editRoleType.value = 'siswa'
    }

    editForm.value = {
        name: user.name,
        email: user.email,
        password: '',
        role: user.role,
        is_super_admin: Boolean(user.is_super_admin),
        phone: user.phone || '',
        is_active: Boolean(user.is_active),
    }
    showEditModal.value = true
}

function submitEdit() {
    if (!editUser.value) return

    if (editRoleType.value === 'super_admin') {
        editForm.value.role = 'admin'
        editForm.value.is_super_admin = true
    } else if (editRoleType.value === 'staff_admin') {
        editForm.value.role = 'admin'
        editForm.value.is_super_admin = false
    } else if (editRoleType.value === 'tutor') {
        editForm.value.role = 'tutor'
        editForm.value.is_super_admin = false
    } else {
        editForm.value.role = 'siswa'
        editForm.value.is_super_admin = false
    }

    router.put(`/admin/users/${editUser.value.id}`, editForm.value, {
        onSuccess: () => {
            showEditModal.value = false
            editUser.value = null
        }
    })
}

// Modal State: Reset Password
const showResetModal = ref(false)
const targetUser = ref(null)
const newPassword = ref('password')

function openResetModal(user) {
    targetUser.value = user
    newPassword.value = 'password'
    showResetModal.value = true
}

function submitResetPassword() {
    if (!targetUser.value) return
    router.post(`/admin/users/${targetUser.value.id}/reset-password`, {
        new_password: newPassword.value
    }, {
        onSuccess: () => {
            showResetModal.value = false
            targetUser.value = null
        }
    })
}

// Toggle Status
function toggleStatus(user) {
    if (user.id === currentUserId) {
        alert('Anda tidak dapat menonaktifkan akun sendiri!')
        return
    }
    router.post(`/admin/users/${user.id}/toggle-status`, {}, { preserveScroll: true })
}

// Delete User
function deleteUser(user) {
    if (user.id === currentUserId) {
        alert('Anda tidak dapat menghapus akun sendiri!')
        return
    }
    if (user.is_super_admin) {
        if (!confirm(`PERINGATAN: Pengguna "${user.name}" adalah Administrator Utama! Yakin ingin menghapus?`)) {
            return
        }
    } else {
        if (!confirm(`Yakin ingin menghapus pengguna "${user.name}" (${user.email}) secara permanen?`)) {
            return
        }
    }
    router.delete(`/admin/users/${user.id}`, { preserveScroll: true })
}

// Helpers
function getRoleBadge(user) {
    if (user.role === 'admin' && user.is_super_admin) {
        return { label: 'Admin Utama', icon: '👑', class: 'bg-amber-100 text-amber-900 border-amber-300 font-extrabold' }
    }
    if (user.role === 'admin' && !user.is_super_admin) {
        return { label: 'Staf Admin (Sub)', icon: '🛡️', class: 'bg-blue-100 text-blue-800 border-blue-200 font-bold' }
    }
    if (user.role === 'tutor') {
        return { label: 'Tutor / Guru', icon: '👨‍🏫', class: 'bg-emerald-100 text-emerald-800 border-emerald-200' }
    }
    if (user.role === 'siswa') {
        return { label: 'Siswa / WB', icon: '👨‍🎓', class: 'bg-purple-100 text-purple-800 border-purple-200' }
    }
    return { label: user.role, icon: '👤', class: 'bg-gray-100 text-gray-700 border-gray-200' }
}

function getAvatarInitials(name) {
    if (!name) return 'U'
    const parts = name.trim().split(' ')
    if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase()
    return (parts[0][0] + parts[1][0]).toUpperCase()
}
</script>

<template>
    <Head title="Manajemen Pengguna — Admin PKBM" />

    <AdminLayout>
        <template #breadcrumb>
            <span class="text-gray-400">Administrasi</span>
            <span class="text-gray-300">/</span>
            <span class="text-gray-800 font-semibold">Manajemen Pengguna</span>
        </template>

        <div class="space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-2xl">👥</span>
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Manajemen Akun Pengguna</h1>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">
                        Kelola akun hierarki sistem: <strong>Admin Utama</strong>, <strong>Staf Admin (Sub-Admin)</strong>, <strong>Tutor</strong>, dan <strong>Siswa</strong>.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <Link
                        href="/profile"
                        class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-200 transition-all active:scale-95"
                        title="Ubah Profil dan Kata Sandi Akun Anda Sendiri Secara Mandiri"
                    >
                        <span>👤</span>
                        <span>Profil & Sandi Saya</span>
                    </Link>
                    <button
                        @click="openCreateModal"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs shadow-lg shadow-blue-500/25 transition-all transform active:scale-95"
                    >
                        <span>➕</span>
                        <span>Tambah Pengguna Baru</span>
                    </button>
                </div>
            </div>

            <!-- Stats Overview Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Total User</p>
                        <p class="text-xl font-black text-gray-900 mt-0.5">{{ stats.total }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">👥</div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-amber-200/80 bg-amber-50/20 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-amber-800 uppercase tracking-wider">Admin Utama</p>
                        <p class="text-xl font-black text-amber-700 mt-0.5">{{ stats.total_super_admin }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg">👑</div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-blue-200/80 bg-blue-50/20 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-blue-800 uppercase tracking-wider">Staf Admin</p>
                        <p class="text-xl font-black text-blue-600 mt-0.5">{{ stats.total_staff_admin }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg">🛡️</div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Tutor / Guru</p>
                        <p class="text-xl font-black text-emerald-600 mt-0.5">{{ stats.total_tutor }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">👨‍🏫</div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Siswa / WB</p>
                        <p class="text-xl font-black text-purple-600 mt-0.5">{{ stats.total_siswa }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">👨‍🎓</div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between col-span-2 sm:col-span-1">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Akun Aktif</p>
                        <p class="text-xl font-black text-teal-600 mt-0.5">{{ stats.total_active }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg">⚡</div>
                </div>
            </div>

            <!-- Role Hierarchy Explainer Banner -->
            <div class="p-4 rounded-2xl bg-gradient-to-r from-blue-50 via-indigo-50 to-slate-50 border border-blue-200/60 flex items-start gap-3 text-xs text-slate-700">
                <span class="text-xl flex-shrink-0">💡</span>
                <div class="space-y-1">
                    <p class="font-bold text-slate-900">Perbedaan Hak Akses Administrator:</p>
                    <p>
                        • <strong class="text-amber-800">👑 Administrator Utama (Kepala Lembaga):</strong> Memiliki kontrol penuh ke seluruh sistem, termasuk <em>Manajemen Akun Pengguna</em> dan <em>Pengaturan Lembaga</em>.
                    </p>
                    <p>
                        • <strong class="text-blue-700">🛡️ Staf Admin / Tata Usaha (Sub-Admin):</strong> Memiliki posisi admin operasional (bisa kelola Data Siswa, Tutor, Kelas, Jadwal, Absensi, Nilai, Raport, Sertifikat, dan Pembayaran), namun <strong>akses ke Manajemen Pengguna dan Pengaturan Lembaga otomatis ditutup dan dikunci</strong> demi keamanan.
                    </p>
                </div>
            </div>

            <!-- Filter Toolbar -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
                <div class="flex flex-1 flex-col sm:flex-row items-center gap-3 w-full">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-80">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
                        <input
                            v-model="search"
                            @keyup.enter="applyFilter"
                            type="text"
                            placeholder="Cari nama, email, no. telepon..."
                            class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
                        />
                    </div>

                    <!-- Role Filter -->
                    <select
                        v-model="selectedRole"
                        @change="applyFilter"
                        class="w-full sm:w-56 py-2.5 px-3 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:bg-white focus:border-blue-500 outline-none transition"
                    >
                        <option value="">Semua Role</option>
                        <option value="super_admin">👑 Admin Utama</option>
                        <option value="staff_admin">🛡️ Staf Admin (Sub-Admin)</option>
                        <option value="tutor">👨‍🏫 Tutor / Guru</option>
                        <option value="siswa">👨‍🎓 Siswa / Warga Belajar</option>
                    </select>

                    <!-- Status Filter -->
                    <select
                        v-model="selectedStatus"
                        @change="applyFilter"
                        class="w-full sm:w-40 py-2.5 px-3 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:bg-white focus:border-blue-500 outline-none transition"
                    >
                        <option value="">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>

                    <button
                        @click="applyFilter"
                        class="w-full sm:w-auto px-4 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-bold transition"
                    >
                        Filter
                    </button>

                    <button
                        v-if="search || selectedRole || selectedStatus"
                        @click="resetFilter"
                        class="w-full sm:w-auto px-3 py-2.5 text-xs text-gray-500 hover:text-gray-800 underline transition"
                    >
                        Reset
                    </button>
                </div>
            </div>

            <!-- Users Table Container -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/75 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="py-4 px-6">Pengguna</th>
                                <th class="py-4 px-4">Tingkat & Role Akses</th>
                                <th class="py-4 px-4">Kontak / Telepon</th>
                                <th class="py-4 px-4">Status Akun</th>
                                <th class="py-4 px-4">Terdaftar Sejak</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            <tr
                                v-for="user in users.data"
                                :key="user.id"
                                :class="[
                                    'hover:bg-slate-50/80 transition',
                                    user.id === currentUserId ? 'bg-amber-50/30' : ''
                                ]"
                            >
                                <!-- Name & Email -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div
                                            :class="[
                                                'w-10 h-10 rounded-2xl flex items-center justify-center font-black text-xs text-white shadow-sm flex-shrink-0',
                                                user.role === 'admin' && user.is_super_admin ? 'bg-gradient-to-tr from-amber-500 to-amber-700 shadow-amber-500/20' :
                                                user.role === 'admin' && !user.is_super_admin ? 'bg-gradient-to-tr from-blue-600 to-indigo-600 shadow-blue-500/20' :
                                                user.role === 'tutor' ? 'bg-gradient-to-tr from-emerald-600 to-teal-600' :
                                                'bg-gradient-to-tr from-purple-600 to-pink-600'
                                            ]"
                                        >
                                            {{ getAvatarInitials(user.name) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-900 text-sm">{{ user.name }}</span>
                                                <span
                                                    v-if="user.id === currentUserId"
                                                    class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-extrabold border border-amber-300"
                                                >
                                                    Anda
                                                </span>
                                            </div>
                                            <span class="text-gray-500 font-mono text-[11px]">{{ user.email }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Role Badge -->
                                <td class="py-4 px-4">
                                    <span
                                        :class="[
                                            'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs border shadow-sm',
                                            getRoleBadge(user).class
                                        ]"
                                    >
                                        <span>{{ getRoleBadge(user).icon }}</span>
                                        <span>{{ getRoleBadge(user).label }}</span>
                                    </span>
                                </td>

                                <!-- Phone -->
                                <td class="py-4 px-4 text-gray-600 font-mono">
                                    {{ user.phone || '—' }}
                                </td>

                                <!-- Status Toggle -->
                                <td class="py-4 px-4">
                                    <button
                                        @click="toggleStatus(user)"
                                        :disabled="user.id === currentUserId"
                                        :class="[
                                            'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold transition',
                                            user.is_active
                                                ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100'
                                                : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100',
                                            user.id === currentUserId ? 'cursor-not-allowed opacity-80' : 'cursor-pointer'
                                        ]"
                                        :title="user.id === currentUserId ? 'Akun aktif Anda sendiri' : 'Klik untuk mengubah status'"
                                    >
                                        <span :class="['w-2 h-2 rounded-full', user.is_active ? 'bg-emerald-500' : 'bg-rose-500']"></span>
                                        <span>{{ user.is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </td>

                                <!-- Created At -->
                                <td class="py-4 px-4 text-gray-500 text-[11px]">
                                    {{ new Date(user.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' }) }}
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-6 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Self Profile & Password Link -->
                                        <Link
                                            v-if="user.id === currentUserId"
                                            href="/profile"
                                            class="p-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 transition"
                                            title="Ubah Profil & Kata Sandi Mandiri Anda"
                                        >
                                            ⚙️
                                        </Link>

                                        <!-- Edit Button -->
                                        <button
                                            @click="openEditModal(user)"
                                            class="p-2 rounded-xl bg-gray-100 hover:bg-blue-50 text-gray-600 hover:text-blue-600 border border-gray-200 transition"
                                            title="Ubah Data Pengguna"
                                        >
                                            ✏️
                                        </button>

                                        <!-- Reset Password Button -->
                                        <button
                                            @click="openResetModal(user)"
                                            class="p-2 rounded-xl bg-gray-100 hover:bg-amber-50 text-gray-600 hover:text-amber-600 border border-gray-200 transition"
                                            title="Reset Kata Sandi"
                                        >
                                            🔑
                                        </button>

                                        <!-- Delete Button -->
                                        <button
                                            @click="deleteUser(user)"
                                            :disabled="user.id === currentUserId"
                                            :class="[
                                                'p-2 rounded-xl border transition',
                                                user.id === currentUserId
                                                    ? 'bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed'
                                                    : 'bg-gray-100 hover:bg-rose-50 text-gray-600 hover:text-rose-600 border-gray-200'
                                            ]"
                                            :title="user.id === currentUserId ? 'Tidak dapat menghapus akun Anda sendiri' : 'Hapus Pengguna'"
                                        >
                                            🗑️
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="!users.data || users.data.length === 0">
                                <td colspan="6" class="py-12 text-center text-gray-400">
                                    <div class="text-3xl mb-2">🔍</div>
                                    <p class="font-semibold text-gray-600">Tidak ada pengguna yang sesuai kriteria.</p>
                                    <p class="text-xs text-gray-400 mt-1">Coba sesuaikan pencarian atau bersihkan filter.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div v-if="users.links && users.links.length > 3" class="p-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <span class="text-gray-500">
                        Menampilkan <strong>{{ users.from || 0 }}</strong> - <strong>{{ users.to || 0 }}</strong> dari <strong>{{ users.total }}</strong> pengguna
                    </span>
                    <div class="flex items-center gap-1">
                        <Link
                            v-for="(link, i) in users.links"
                            :key="i"
                            :href="link.url || '#'"
                            :class="[
                                'px-3 py-1.5 rounded-xl font-medium transition',
                                link.active ? 'bg-blue-600 text-white font-bold shadow-sm' :
                                link.url ? 'bg-white hover:bg-gray-100 text-gray-700 border border-gray-200' :
                                'bg-gray-100 text-gray-400 cursor-not-allowed'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================= -->
        <!-- MODAL: TAMBAH PENGGUNA BARU                       -->
        <!-- ================================================= -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-2xl max-w-lg w-full overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center gap-2.5">
                        <span class="text-2xl">👤</span>
                        <div>
                            <h2 class="font-black text-gray-900 text-base">Tambah Pengguna Baru</h2>
                            <p class="text-xs text-gray-500">Buat akun untuk Staf Admin, Tutor, atau Siswa</p>
                        </div>
                    </div>
                    <button @click="showCreateModal = false" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-700 flex items-center justify-center border text-sm font-bold shadow-sm">
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitCreate" class="p-6 space-y-4 text-xs">
                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5 uppercase tracking-wider text-[10px]">Nama Lengkap *</label>
                        <input
                            v-model="createForm.name"
                            required
                            type="text"
                            placeholder="cth: Siti Aminah / Staf Tata Usaha"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none text-xs"
                        />
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5 uppercase tracking-wider text-[10px]">Alamat Email (Login) *</label>
                        <input
                            v-model="createForm.email"
                            required
                            type="email"
                            placeholder="cth: staf@pkbm.sch.id"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none text-xs"
                        />
                    </div>

                    <!-- Tingkat Role Akses -->
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5 uppercase tracking-wider text-[10px]">Tingkat / Peran Akses *</label>
                        <select
                            v-model="createRoleType"
                            required
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 outline-none text-xs font-semibold"
                        >
                            <option value="staff_admin">🛡️ Staf Admin / Tata Usaha (Di bawah Admin Utama)</option>
                            <option value="super_admin">👑 Administrator Utama (Akses Penuh Seluruh Sistem)</option>
                            <option value="tutor">👨‍🏫 Tutor / Guru</option>
                            <option value="siswa">👨‍🎓 Siswa / Warga Belajar</option>
                        </select>
                        <p v-if="createRoleType === 'staff_admin'" class="text-[11px] text-blue-600 bg-blue-50 p-2.5 rounded-xl border border-blue-200 mt-2">
                            ℹ️ <strong>Staf Admin:</strong> Bisa mengelola siswa, tutor, kelas, absensi, nilai, raport, sertifikat & pembayaran, tetapi <strong>tidak bisa</strong> membuka Manajemen Pengguna & Pengaturan Lembaga.
                        </p>
                    </div>

                    <!-- No. Telepon -->
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5 uppercase tracking-wider text-[10px]">No. Telepon / WA</label>
                        <input
                            v-model="createForm.phone"
                            type="text"
                            placeholder="0812xxxxxxxx"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 outline-none text-xs font-mono"
                        />
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="font-bold text-gray-700 uppercase tracking-wider text-[10px]">Kata Sandi *</label>
                            <button
                                type="button"
                                @click="generateRandomPassword"
                                class="text-blue-600 hover:text-blue-800 font-semibold text-[11px]"
                            >
                                ⚡ Acak Sandi
                            </button>
                        </div>
                        <div class="relative">
                            <input
                                v-model="createForm.password"
                                required
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Minimal 6 karakter"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none text-xs pr-12 font-mono"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 text-xs"
                            >
                                {{ showPassword ? '🙈' : '👁️' }}
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">Default: <code class="bg-gray-100 px-1 py-0.5 rounded">password</code></p>
                    </div>

                    <!-- Status Switch -->
                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input
                                type="checkbox"
                                v-model="createForm.is_active"
                                class="w-4 h-4 rounded text-blue-600 focus:ring-0 border-gray-300"
                            />
                            <span class="font-semibold text-gray-700">Akun Langsung Aktif</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-100">
                        <button
                            type="button"
                            @click="showCreateModal = false"
                            class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 font-semibold transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition shadow-sm"
                        >
                            Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================================================= -->
        <!-- MODAL: UBAH DATA PENGGUNA                         -->
        <!-- ================================================= -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-2xl max-w-lg w-full overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center gap-2.5">
                        <span class="text-2xl">✏️</span>
                        <div>
                            <h2 class="font-black text-gray-900 text-base">Ubah Pengguna</h2>
                            <p class="text-xs text-gray-500">{{ editUser?.name }} ({{ editUser?.email }})</p>
                        </div>
                    </div>
                    <button @click="showEditModal = false" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-700 flex items-center justify-center border text-sm font-bold shadow-sm">
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitEdit" class="p-6 space-y-4 text-xs">
                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5 uppercase tracking-wider text-[10px]">Nama Lengkap *</label>
                        <input
                            v-model="editForm.name"
                            required
                            type="text"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none text-xs"
                        />
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5 uppercase tracking-wider text-[10px]">Alamat Email (Login) *</label>
                        <input
                            v-model="editForm.email"
                            required
                            type="email"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none text-xs"
                        />
                    </div>

                    <!-- Tingkat Role Akses -->
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5 uppercase tracking-wider text-[10px]">Tingkat / Peran Akses *</label>
                        <select
                            v-model="editRoleType"
                            required
                            :disabled="editUser?.id === currentUserId"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 outline-none text-xs font-semibold disabled:bg-gray-100 disabled:cursor-not-allowed"
                        >
                            <option value="staff_admin">🛡️ Staf Admin / Tata Usaha (Di bawah Admin Utama)</option>
                            <option value="super_admin">👑 Administrator Utama (Akses Penuh)</option>
                            <option value="tutor">👨‍🏫 Tutor / Guru</option>
                            <option value="siswa">👨‍🎓 Siswa / Warga Belajar</option>
                        </select>
                        <p v-if="editUser?.id === currentUserId" class="text-[10px] text-gray-400 italic mt-1">
                            (Anda tidak dapat mengubah role akun Anda sendiri)
                        </p>
                    </div>

                    <!-- No. Telepon -->
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5 uppercase tracking-wider text-[10px]">No. Telepon / WA</label>
                        <input
                            v-model="editForm.phone"
                            type="text"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 outline-none text-xs font-mono"
                        />
                    </div>

                    <!-- Password Baru (Opsional) -->
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5 uppercase tracking-wider text-[10px]">Ganti Kata Sandi (Opsional)</label>
                        <input
                            v-model="editForm.password"
                            type="password"
                            placeholder="Kosongkan jika tidak ingin mengubah kata sandi"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none text-xs font-mono"
                        />
                    </div>

                    <!-- Status Switch -->
                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input
                                type="checkbox"
                                v-model="editForm.is_active"
                                :disabled="editUser?.id === currentUserId"
                                class="w-4 h-4 rounded text-blue-600 focus:ring-0 border-gray-300 disabled:opacity-50"
                            />
                            <span class="font-semibold text-gray-700">Akun Aktif</span>
                            <span v-if="editUser?.id === currentUserId" class="text-[10px] text-gray-400 italic">(Akun Anda sendiri tidak dapat dinonaktifkan)</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-100">
                        <button
                            type="button"
                            @click="showEditModal = false"
                            class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 font-semibold transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition shadow-sm"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================================================= -->
        <!-- MODAL: RESET PASSWORD CEPAT                       -->
        <!-- ================================================= -->
        <div v-if="showResetModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-2xl max-w-sm w-full overflow-hidden p-6 space-y-4">
                <div class="text-center space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-2xl mx-auto">
                        🔑
                    </div>
                    <h3 class="text-base font-black text-gray-900">Reset Kata Sandi</h3>
                    <p class="text-xs text-gray-500">
                        Reset kata sandi untuk pengguna <strong class="text-gray-800">{{ targetUser?.name }}</strong>.
                    </p>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5 uppercase tracking-wider text-[10px]">Kata Sandi Baru</label>
                    <input
                        v-model="newPassword"
                        type="text"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none text-xs font-mono font-bold text-center"
                    />
                    <p class="text-[10px] text-gray-400 text-center mt-1">Default sandi cepat: <code>password</code></p>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button
                        type="button"
                        @click="showResetModal = false"
                        class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 font-semibold text-xs transition"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="submitResetPassword"
                        class="flex-1 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition shadow-sm"
                    >
                        Set Sandi Baru
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
