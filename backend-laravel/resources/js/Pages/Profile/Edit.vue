<script setup>
import { ref, computed } from 'vue'
import { Head, useForm, usePage, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import TutorLayout from '@/Layouts/TutorLayout.vue'
import SiswaLayout from '@/Layouts/SiswaLayout.vue'

const props = defineProps({
    user: Object,
    status: String,
    success: String,
    error: String,
})

const page = usePage()

// Dynamic Layout based on User Role
const currentLayout = computed(() => {
    if (props.user?.role === 'tutor') return TutorLayout
    if (props.user?.role === 'siswa') return SiswaLayout
    return AdminLayout
})

const dashboardRoute = computed(() => {
    if (props.user?.role === 'tutor') return '/tutor/dashboard'
    if (props.user?.role === 'siswa') return '/siswa-portal/dashboard'
    return '/dashboard'
})

// Role styling and metadata
const roleMeta = computed(() => {
    if (props.user?.role === 'admin') {
        return {
            title: props.user?.is_super_admin ? 'Administrator Utama' : 'Staf Administrasi TU',
            badgeClass: props.user?.is_super_admin 
                ? 'bg-amber-100 text-amber-800 border-amber-300' 
                : 'bg-blue-100 text-blue-800 border-blue-300',
            icon: props.user?.is_super_admin ? '👑' : '🛡️',
            themeGradient: 'from-slate-900 via-indigo-950 to-slate-900',
            accentColor: 'indigo',
        }
    }
    if (props.user?.role === 'tutor') {
        return {
            title: 'Tutor Pengajar',
            badgeClass: 'bg-emerald-100 text-emerald-800 border-emerald-300',
            icon: '👨‍🏫',
            themeGradient: 'from-emerald-800 via-teal-800 to-slate-900',
            accentColor: 'emerald',
        }
    }
    return {
        title: 'Warga Belajar (Siswa)',
        badgeClass: 'bg-indigo-100 text-indigo-800 border-indigo-300',
        icon: '👨‍🎓',
        themeGradient: 'from-indigo-800 via-purple-800 to-slate-900',
        accentColor: 'indigo',
    }
})

// Form 1: Ubah Data Profil (User, Email, Telepon)
const profileForm = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    phone: props.user?.phone || '',
})

const profileSuccessMessage = ref('')

function submitProfile() {
    profileSuccessMessage.value = ''
    profileForm.patch('/profile', {
        preserveScroll: true,
        onSuccess: () => {
            profileSuccessMessage.value = 'Data akun dan identitas Anda berhasil diperbarui!'
            setTimeout(() => {
                profileSuccessMessage.value = ''
            }, 5000)
        },
    })
}

// Form 2: Ganti Password Mandiri
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)
const passwordSuccessMessage = ref('')

// Password Strength Evaluation
const passwordStrength = computed(() => {
    const val = passwordForm.password
    if (!val) return { score: 0, text: '', color: 'bg-gray-200' }
    
    let score = 0
    if (val.length >= 6) score += 1
    if (val.length >= 8) score += 1
    if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score += 1
    if (/[0-9]/.test(val)) score += 1
    if (/[^A-Za-z0-9]/.test(val)) score += 1

    if (score <= 2) return { score: 1, text: 'Sangat Lemah', color: 'bg-rose-500' }
    if (score === 3) return { score: 2, text: 'Cukup Aman', color: 'bg-amber-500' }
    if (score === 4) return { score: 3, text: 'Kuat', color: 'bg-teal-500' }
    return { score: 4, text: 'Sangat Kuat', color: 'bg-emerald-500' }
})

const passwordsMatch = computed(() => {
    if (!passwordForm.password_confirmation) return null
    return passwordForm.password === passwordForm.password_confirmation
})

function submitPassword() {
    passwordSuccessMessage.value = ''
    passwordForm.put('/profile/password', {
        preserveScroll: true,
        onSuccess: () => {
            passwordSuccessMessage.value = 'Kata sandi Anda berhasil diperbarui! Silakan gunakan sandi baru untuk login selanjutnya.'
            passwordForm.reset()
            setTimeout(() => {
                passwordSuccessMessage.value = ''
            }, 6000)
        },
    })
}
</script>

<template>
    <Head title="Profil & Pengaturan Akun Saya — PKBM" />

    <component :is="currentLayout">
        <!-- Breadcrumb Slot for AdminLayout -->
        <template #breadcrumb>
            <div class="flex items-center gap-2 text-xs sm:text-sm">
                <Link :href="dashboardRoute" class="text-slate-500 hover:text-indigo-600 transition">Beranda</Link>
                <span class="text-slate-300">/</span>
                <span class="font-bold text-slate-800">Profil & Pengaturan Akun</span>
            </div>
        </template>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-8 space-y-6 sm:space-y-8">
            
            <!-- Global Flash Banner -->
            <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
                <div v-if="$page.props.flash?.success || success || profileSuccessMessage || passwordSuccessMessage" class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm">
                    <span class="text-xl">✅</span>
                    <div class="text-xs sm:text-sm font-semibold flex-1">
                        {{ profileSuccessMessage || passwordSuccessMessage || $page.props.flash?.success || success }}
                    </div>
                </div>
            </transition>

            <!-- ============================================== -->
            <!-- HERO PROFILE CARD                              -->
            <!-- ============================================== -->
            <div :class="['rounded-3xl p-5 sm:p-8 text-white shadow-xl relative overflow-hidden bg-gradient-to-r', roleMeta.themeGradient]">
                <!-- Decorative background elements -->
                <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute right-20 top-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
                    <div class="flex items-center gap-4 sm:gap-5">
                        <!-- Big Avatar with Initials -->
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-white font-black text-2xl sm:text-3xl flex items-center justify-center shadow-lg flex-shrink-0">
                            {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <span :class="['inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] sm:text-xs font-bold border backdrop-blur-md', roleMeta.badgeClass]">
                                    <span>{{ roleMeta.icon }}</span>
                                    <span>{{ roleMeta.title }}</span>
                                </span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] sm:text-xs font-medium bg-white/10 text-emerald-200 border border-emerald-400/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span>Akun Aktif</span>
                                </span>
                            </div>
                            <h1 class="text-xl sm:text-2xl lg:text-3xl font-black tracking-tight leading-tight">
                                {{ user?.name }}
                            </h1>
                            <p class="text-xs sm:text-sm text-slate-300 font-mono mt-0.5 flex items-center gap-1.5">
                                <span>✉️ {{ user?.email }}</span>
                                <span v-if="user?.phone">• 📱 {{ user?.phone }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Return to Dashboard Link -->
                    <Link
                        :href="dashboardRoute"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 border border-white/20 text-xs sm:text-sm font-bold text-white transition backdrop-blur shadow-sm w-full sm:w-auto justify-center"
                    >
                        <span>← Kembali ke Dashboard</span>
                    </Link>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- DUAL CARDS: EDIT PROFILE & EDIT PASSWORD       -->
            <!-- ============================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 items-start">

                <!-- ========================================== -->
                <!-- CARD 1: UBAH INFORMASI USER & IDENTITAS    -->
                <!-- ========================================== -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-7 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-5">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-bold">
                                👤
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-bold text-slate-900">Ubah Identitas Akun</h2>
                                <p class="text-xs text-slate-500">Perbarui nama pengguna, email login, dan nomor kontak Anda.</p>
                            </div>
                        </div>

                        <form @submit.prevent="submitProfile" class="space-y-4 sm:space-y-5">
                            <!-- Field: Nama Lengkap -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Lengkap / Username
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm">
                                        👤
                                    </span>
                                    <input
                                        v-model="profileForm.name"
                                        type="text"
                                        required
                                        placeholder="Masukkan nama lengkap Anda..."
                                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition"
                                        :class="{ 'border-rose-400 bg-rose-50/20': profileForm.errors.name }"
                                    />
                                </div>
                                <p v-if="profileForm.errors.name" class="mt-1 text-xs text-rose-600 font-medium">
                                    {{ profileForm.errors.name }}
                                </p>
                            </div>

                            <!-- Field: Alamat Email -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Alamat Email (Username Login)
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm">
                                        ✉️
                                    </span>
                                    <input
                                        v-model="profileForm.email"
                                        type="email"
                                        required
                                        placeholder="nama@email.com"
                                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition"
                                        :class="{ 'border-rose-400 bg-rose-50/20': profileForm.errors.email }"
                                    />
                                </div>
                                <p v-if="profileForm.errors.email" class="mt-1 text-xs text-rose-600 font-medium">
                                    {{ profileForm.errors.email }}
                                </p>
                                <p class="mt-1.5 text-[11px] text-slate-400 flex items-center gap-1">
                                    ℹ️ Email ini digunakan sebagai identitas akun untuk masuk ke aplikasi.
                                </p>
                            </div>

                            <!-- Field: Nomor Telepon / WA -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nomor Telepon / WhatsApp
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm">
                                        📱
                                    </span>
                                    <input
                                        v-model="profileForm.phone"
                                        type="tel"
                                        placeholder="Contoh: 081234567890"
                                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition"
                                        :class="{ 'border-rose-400 bg-rose-50/20': profileForm.errors.phone }"
                                    />
                                </div>
                                <p v-if="profileForm.errors.phone" class="mt-1 text-xs text-rose-600 font-medium">
                                    {{ profileForm.errors.phone }}
                                </p>
                            </div>

                            <!-- Role Info (Read Only) -->
                            <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-100 flex items-center justify-between">
                                <div>
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Hak Akses Sistem</div>
                                    <div class="text-xs font-bold text-slate-800">{{ roleMeta.title }}</div>
                                </div>
                                <div class="text-xs text-slate-400 font-medium">
                                    Diatur oleh Sistem
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-2">
                                <button
                                    type="submit"
                                    :disabled="profileForm.processing"
                                    class="w-full py-3 px-5 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:scale-[0.99] text-white font-bold text-xs sm:text-sm shadow-md shadow-indigo-600/20 transition flex items-center justify-center gap-2 disabled:opacity-50"
                                >
                                    <span v-if="profileForm.processing" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                    <span>{{ profileForm.processing ? 'Menyimpan Perubahan...' : 'Simpan Perubahan Identitas' }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- CARD 2: GANTI KATA SANDI MANDIRI           -->
                <!-- ========================================== -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-7 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-5">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-bold">
                                🔑
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-bold text-slate-900">Ganti Kata Sandi Mandiri</h2>
                                <p class="text-xs text-slate-500">Perbarui kata sandi akun Anda secara mandiri untuk keamanan.</p>
                            </div>
                        </div>

                        <form @submit.prevent="submitPassword" class="space-y-4 sm:space-y-5">
                            <!-- Field: Kata Sandi Saat Ini -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Kata Sandi Saat Ini
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm">
                                        🔒
                                    </span>
                                    <input
                                        v-model="passwordForm.current_password"
                                        :type="showCurrentPassword ? 'text' : 'password'"
                                        required
                                        placeholder="Masukkan kata sandi lama Anda..."
                                        class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition"
                                        :class="{ 'border-rose-400 bg-rose-50/20': passwordForm.errors.current_password }"
                                    />
                                    <button
                                        type="button"
                                        @click="showCurrentPassword = !showCurrentPassword"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 text-sm"
                                        tabindex="-1"
                                        title="Tampilkan / Sembunyikan"
                                    >
                                        {{ showCurrentPassword ? '👁️' : '🙈' }}
                                    </button>
                                </div>
                                <p v-if="passwordForm.errors.current_password" class="mt-1 text-xs text-rose-600 font-medium">
                                    {{ passwordForm.errors.current_password }}
                                </p>
                            </div>

                            <!-- Field: Kata Sandi Baru -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Kata Sandi Baru (Min. 6 Karakter)
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm">
                                        🆕
                                    </span>
                                    <input
                                        v-model="passwordForm.password"
                                        :type="showNewPassword ? 'text' : 'password'"
                                        required
                                        minlength="6"
                                        placeholder="Ketik kata sandi baru..."
                                        class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition"
                                        :class="{ 'border-rose-400 bg-rose-50/20': passwordForm.errors.password }"
                                    />
                                    <button
                                        type="button"
                                        @click="showNewPassword = !showNewPassword"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 text-sm"
                                        tabindex="-1"
                                        title="Tampilkan / Sembunyikan"
                                    >
                                        {{ showNewPassword ? '👁️' : '🙈' }}
                                    </button>
                                </div>
                                <p v-if="passwordForm.errors.password" class="mt-1 text-xs text-rose-600 font-medium">
                                    {{ passwordForm.errors.password }}
                                </p>

                                <!-- Password Strength Indicator Bar -->
                                <div v-if="passwordForm.password" class="mt-2 space-y-1">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="text-slate-500">Kekuatan Sandi:</span>
                                        <span class="font-bold text-slate-700">{{ passwordStrength.text }}</span>
                                    </div>
                                    <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden flex gap-1">
                                        <div
                                            v-for="step in 4"
                                            :key="step"
                                            :class="[
                                                'h-full flex-1 rounded-full transition-all duration-300',
                                                step <= passwordStrength.score ? passwordStrength.color : 'bg-slate-200'
                                            ]"
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Field: Konfirmasi Kata Sandi Baru -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Ulangi Kata Sandi Baru
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm">
                                        🔁
                                    </span>
                                    <input
                                        v-model="passwordForm.password_confirmation"
                                        :type="showConfirmPassword ? 'text' : 'password'"
                                        required
                                        placeholder="Ketik ulang kata sandi baru..."
                                        class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition"
                                        :class="{ 'border-rose-400 bg-rose-50/20': passwordForm.errors.password_confirmation }"
                                    />
                                    <button
                                        type="button"
                                        @click="showConfirmPassword = !showConfirmPassword"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 text-sm"
                                        tabindex="-1"
                                        title="Tampilkan / Sembunyikan"
                                    >
                                        {{ showConfirmPassword ? '👁️' : '🙈' }}
                                    </button>
                                </div>
                                <p v-if="passwordForm.errors.password_confirmation" class="mt-1 text-xs text-rose-600 font-medium">
                                    {{ passwordForm.errors.password_confirmation }}
                                </p>
                                <!-- Live Match Check -->
                                <div v-if="passwordsMatch !== null" class="mt-1.5 text-[11px] font-semibold flex items-center gap-1">
                                    <span v-if="passwordsMatch" class="text-emerald-600 flex items-center gap-1">
                                        ✓ Konfirmasi sandi cocok
                                    </span>
                                    <span v-else class="text-rose-600 flex items-center gap-1">
                                        ✗ Konfirmasi sandi belum sama
                                    </span>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-2">
                                <button
                                    type="submit"
                                    :disabled="passwordForm.processing || (passwordsMatch === false)"
                                    class="w-full py-3 px-5 rounded-xl bg-amber-600 hover:bg-amber-700 active:scale-[0.99] text-white font-bold text-xs sm:text-sm shadow-md shadow-amber-600/20 transition flex items-center justify-center gap-2 disabled:opacity-50"
                                >
                                    <span v-if="passwordForm.processing" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                    <span>{{ passwordForm.processing ? 'Menyimpan Kata Sandi...' : 'Perbarui Kata Sandi Saya' }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </component>
</template>
