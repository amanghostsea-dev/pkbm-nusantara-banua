<script setup>
/**
 * Siswa Layout — Portal Warga Belajar PKBM
 * Desain modern & responsif di semua perangkat (smartphone, tablet, desktop)
 * Dilengkapi Mobile Bottom Navigation Bar & Mobile Off-canvas Drawer
 */

import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const mobileDrawerOpen = ref(false)

let removeNavListener = null
onMounted(() => {
    removeNavListener = router.on('navigate', () => {
        mobileDrawerOpen.value = false
    })
})

onUnmounted(() => {
    if (typeof removeNavListener === 'function') {
        removeNavListener()
    }
})

const currentUrl = computed(() => {
    return page.url || ''
})

const isCurrent = (path) => {
    return currentUrl.value.startsWith(path)
}

const navItems = [
    { name: 'Dashboard', href: '/siswa-portal/dashboard', icon: '🏠', highlight: true },
    { name: 'Jadwal', href: '/siswa-portal/jadwal', icon: '🗓️', highlight: true },
    { name: 'Modul Belajar', href: '/siswa-portal/materi', icon: '📖', highlight: true },
    { name: 'Tugas Siswa', href: '/siswa-portal/tugas', icon: '📝', highlight: true },
    { name: 'Nilai & Rapor', href: '/siswa-portal/nilai', icon: '📊', highlight: false },
    { name: 'Presensi QR', href: '/siswa-portal/absensi', icon: '📲', highlight: false },
    { name: 'Pembayaran SPP', href: '/siswa-portal/pembayaran', icon: '💰', highlight: false },
    { name: 'Raport Siswa', href: '/siswa-portal/raport', icon: '📄', highlight: false },
    { name: 'Profil & Sandi', href: '/profile', icon: '👤', highlight: false },
]

const toggleMobileDrawer = () => {
    mobileDrawerOpen.value = !mobileDrawerOpen.value
}

const closeMobileDrawer = () => {
    mobileDrawerOpen.value = false
}
</script>

<template>
    <div class="min-h-screen bg-slate-50 font-sans flex flex-col antialiased">
        <!-- ========================================== -->
        <!-- TOP NAVIGATION HEADER                      -->
        <!-- ========================================== -->
        <header class="bg-white/90 backdrop-blur-md border-b border-gray-200 sticky top-0 z-30 transition-all">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <!-- Brand / Logo -->
                <Link href="/siswa-portal/dashboard" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-600 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                        👨‍🎓
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <h1 class="text-sm sm:text-base font-extrabold text-gray-900 leading-tight">Portal Siswa</h1>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/60 hidden xs:inline">WB</span>
                        </div>
                        <p class="text-[11px] text-gray-500 leading-none mt-0.5 hidden sm:block">PKBM BASARA Banua</p>
                    </div>
                </Link>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <Link
                        v-for="item in navItems.slice(0, 5)"
                        :key="item.name"
                        :href="item.href"
                        :class="[
                            'px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5',
                            isCurrent(item.href)
                                ? 'bg-indigo-50 text-indigo-700 shadow-sm'
                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                        ]"
                    >
                        <span>{{ item.icon }}</span>
                        <span>{{ item.name }}</span>
                    </Link>
                </nav>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- User Profile Card (Tablet & Desktop) -->
                    <Link
                        href="/profile"
                        class="text-right hidden sm:block group hover:opacity-80 transition cursor-pointer"
                        title="Buka Pengaturan Profil & Ganti Kata Sandi"
                    >
                        <div class="text-xs font-bold text-gray-800 truncate max-w-[150px] group-hover:text-indigo-600 transition">{{ user?.name || 'Warga Belajar' }}</div>
                        <div class="text-[10px] text-indigo-600 font-semibold flex items-center justify-end gap-1">
                            <span>Paket Kesetaraan</span>
                            <span class="text-[9px]">⚙️</span>
                        </div>
                    </Link>

                    <!-- Profile Quick Button -->
                    <Link
                        href="/profile"
                        class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-xs font-bold text-indigo-700 transition"
                        title="Profil & Ganti Kata Sandi Mandiri"
                    >
                        <span>👤</span>
                        <span>Profil</span>
                    </Link>

                    <!-- Direct QR Scan Shortcut on mobile -->
                    <Link
                        href="/siswa-portal/absensi"
                        class="md:hidden p-2 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200/60 text-sm font-bold flex items-center justify-center"
                        title="Scan Presensi QR"
                    >
                        📲
                    </Link>

                    <!-- Logout Button (Desktop) -->
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="hidden sm:inline-flex px-3.5 py-1.5 rounded-xl border border-gray-200 hover:bg-gray-100 text-xs font-semibold text-gray-700 transition"
                    >
                        Keluar
                    </Link>

                    <!-- Mobile Drawer Hamburger Toggle Button -->
                    <button
                        type="button"
                        class="md:hidden w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200/80 text-slate-800 flex items-center justify-center text-lg active:scale-95 transition"
                        @click.stop="toggleMobileDrawer"
                        aria-label="Buka menu navigasi portal siswa"
                    >
                        <span>☰</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- ========================================== -->
        <!-- MOBILE OFF-CANVAS DRAWER                   -->
        <!-- ========================================== -->
        <transition
            enter-active-class="transition-opacity ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="mobileDrawerOpen"
                class="drawer-backdrop"
                @click="closeMobileDrawer"
                @touchstart.passive="closeMobileDrawer"
                aria-hidden="true"
            />
        </transition>

        <aside
            :class="[
                'fixed inset-y-0 left-0 w-72 max-w-[85vw] bg-white shadow-2xl z-50 flex flex-col transition-transform duration-300 ease-out md:hidden',
                mobileDrawerOpen ? 'translate-x-0 visible pointer-events-auto' : '-translate-x-full invisible pointer-events-none'
            ]"
        >
            <!-- Drawer Header -->
            <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-slate-900 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-xl font-bold">
                        👨‍🎓
                    </div>
                    <div>
                        <div class="text-sm font-bold truncate max-w-[160px]">{{ user?.name || 'Warga Belajar' }}</div>
                        <div class="text-[11px] text-indigo-300">Portal Siswa Mandiri</div>
                    </div>
                </div>
                <button
                    @click="closeMobileDrawer"
                    class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-sm font-bold transition"
                    aria-label="Tutup menu"
                >
                    ✕
                </button>
            </div>

            <!-- Drawer Navigation Items -->
            <div class="flex-1 overflow-y-auto p-4 space-y-1.5">
                <!-- User Profile Quick Card -->
                <Link
                    href="/profile"
                    class="flex items-center gap-3 p-3 rounded-2xl bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200/60 mb-3 group hover:border-indigo-300 transition"
                    @click="closeMobileDrawer"
                >
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">
                        👤
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold text-slate-800 truncate">{{ user?.name || 'Warga Belajar' }}</div>
                        <div class="text-[10px] text-indigo-700 font-semibold">Ubah Profil & Sandi Mandiri →</div>
                    </div>
                </Link>

                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-400 px-3 py-1">Menu Utama Belajar</div>
                <Link
                    v-for="item in navItems"
                    :key="item.name"
                    :href="item.href"
                    :class="[
                        'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition',
                        isCurrent(item.href)
                            ? 'bg-indigo-50 text-indigo-700 font-bold border border-indigo-200/50'
                            : 'text-gray-700 hover:bg-gray-50'
                    ]"
                    @click="closeMobileDrawer"
                >
                    <span class="text-lg w-6 text-center">{{ item.icon }}</span>
                    <span>{{ item.name }}</span>
                    <span v-if="isCurrent(item.href)" class="ml-auto w-2 h-2 rounded-full bg-indigo-600"></span>
                </Link>

                <div class="pt-3 pb-1 border-t border-gray-100 mt-3">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-gray-400 px-3 py-1">Layanan Publik</div>
                    <a
                        href="/verifikasi"
                        target="_blank"
                        class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold text-teal-700 bg-teal-50 hover:bg-teal-100 transition"
                        @click="closeMobileDrawer"
                    >
                        <span class="text-base w-6 text-center">🔍</span>
                        <span>Verifikasi Ijazah Digital</span>
                    </a>
                </div>
            </div>

            <!-- Drawer Footer / Logout -->
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="w-full py-2.5 px-4 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition flex items-center justify-center gap-2"
                    @click="closeMobileDrawer"
                >
                    <span>🚪</span>
                    <span>Keluar dari Akun</span>
                </Link>
            </div>
        </aside>

        <!-- ========================================== -->
        <!-- MAIN PAGE CONTENT                          -->
        <!-- ========================================== -->
        <main class="flex-1 pb-24 md:pb-10">
            <slot />
        </main>

        <!-- ========================================== -->
        <!-- NATIVE-STYLE MOBILE BOTTOM NAVIGATION BAR  -->
        <!-- ========================================== -->
        <nav class="md:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-xl border-t border-slate-200/90 shadow-2xl z-40 pb-safe">
            <div class="grid grid-cols-5 h-16">
                <!-- 1. Dashboard -->
                <Link
                    href="/siswa-portal/dashboard"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 transition-all',
                        isCurrent('/siswa-portal/dashboard') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-800'
                    ]"
                >
                    <span class="text-xl leading-none">🏠</span>
                    <span class="text-[10px] tracking-tight">Beranda</span>
                </Link>

                <!-- 2. Jadwal -->
                <Link
                    href="/siswa-portal/jadwal"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 transition-all',
                        isCurrent('/siswa-portal/jadwal') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-800'
                    ]"
                >
                    <span class="text-xl leading-none">🗓️</span>
                    <span class="text-[10px] tracking-tight">Jadwal</span>
                </Link>

                <!-- 3. Modul Belajar -->
                <Link
                    href="/siswa-portal/materi"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 transition-all',
                        isCurrent('/siswa-portal/materi') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-800'
                    ]"
                >
                    <span class="text-xl leading-none">📖</span>
                    <span class="text-[10px] tracking-tight">Modul</span>
                </Link>

                <!-- 4. Tugas -->
                <Link
                    href="/siswa-portal/tugas"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 transition-all',
                        isCurrent('/siswa-portal/tugas') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-800'
                    ]"
                >
                    <span class="text-xl leading-none">📝</span>
                    <span class="text-[10px] tracking-tight">Tugas</span>
                </Link>

                <!-- 5. Menu Lainnya -->
                <button
                    type="button"
                    @click="toggleMobileDrawer"
                    class="flex flex-col items-center justify-center gap-1 text-slate-500 hover:text-slate-800 transition-all active:scale-95"
                    aria-label="Buka menu lengkap"
                >
                    <span class="text-xl leading-none">☰</span>
                    <span class="text-[10px] tracking-tight">Lainnya</span>
                </button>
            </div>
        </nav>
    </div>
</template>
