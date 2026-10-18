<script setup>
/**
 * Admin Layout — Sistem PKBM
 * Layout utama dengan sidebar navigasi modern & responsive mobile drawer
 */

import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth?.user)

// Responsive state: Default false so mobile never loads stuck with open backdrop
const isMobile = ref(false)
const sidebarOpen = ref(false)

const handleResize = () => {
    if (typeof window !== 'undefined') {
        const mobile = window.innerWidth <= 1024
        if (mobile !== isMobile.value) {
            isMobile.value = mobile
            sidebarOpen.value = !mobile
        }
    }
}

let removeNavListener = null

onMounted(() => {
    if (typeof window !== 'undefined') {
        isMobile.value = window.innerWidth <= 1024
        sidebarOpen.value = window.innerWidth > 1024
        window.addEventListener('resize', handleResize)
    }

    // Auto-close drawer on mobile when navigating to another page
    removeNavListener = router.on('navigate', () => {
        if (typeof window !== 'undefined' && window.innerWidth <= 1024) {
            sidebarOpen.value = false
        }
    })
})

onUnmounted(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('resize', handleResize)
    }
    if (typeof removeNavListener === 'function') {
        removeNavListener()
    }
})

const closeSidebarOnMobile = () => {
    if (typeof window !== 'undefined' && window.innerWidth <= 1024) {
        sidebarOpen.value = false
    }
}

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
}

const currentRoute = computed(() => {
    if (typeof window !== 'undefined' && typeof window.route === 'function') {
        return window.route().current()
    }
    return ''
})

const navGroups = [
    {
        label: 'Utama',
        items: [
            { name: 'Dashboard', href: '/dashboard', icon: '🏠', route: 'dashboard' },
            { name: 'Pengumuman', href: '/admin/pengumumans', icon: '📢', route: 'admin.pengumumans.*' },
        ],
    },
    {
        label: 'Akademik',
        items: [
            { name: 'Data Siswa', href: '/admin/siswa', icon: '👨‍🎓', route: 'admin.siswa.*' },
            { name: 'Data Tutor', href: '/admin/tutors', icon: '👨‍🏫', route: 'admin.tutors.*' },
            { name: 'Program', href: '/admin/programs', icon: '📋', route: 'admin.programs.*' },
            { name: 'Angkatan', href: '/admin/angkatans', icon: '📅', route: 'admin.angkatans.*' },
            { name: 'Kelas', href: '/admin/kelas', icon: '🏫', route: 'admin.kelas.*' },
            { name: 'Mata Pelajaran', href: '/admin/mata-pelajarans', icon: '📚', route: 'admin.mata-pelajarans.*' },
            { name: 'Jadwal', href: '/admin/jadwals', icon: '🗓️', route: 'admin.jadwals.*' },
        ],
    },
    {
        label: 'Pembelajaran (LMS)',
        items: [
            { name: 'Modul Belajar', href: '/admin/materi', icon: '📖', route: 'admin.materi.*' },
            { name: 'Tugas Siswa', href: '/admin/tugas', icon: '📝', route: 'admin.tugas.*' },
        ],
    },
    {
        label: 'Penilaian & Ijazah',
        items: [
            { name: 'Absensi', href: '/admin/absensi', icon: '✅', route: 'admin.absensi.*' },
            { name: 'Nilai', href: '/admin/nilais', icon: '📊', route: 'admin.nilais.*' },
            { name: 'Raport', href: '/admin/raports', icon: '📄', route: 'admin.raports.*' },
            { name: 'Sertifikat', href: '/admin/sertifikats', icon: '🎓', route: 'admin.sertifikats.*' },
            { name: 'Portal Verifikasi', href: '/verifikasi', icon: '🔍', route: 'verifikasi.*' },
        ],
    },
    {
        label: 'Administrasi',
        items: [
            { name: 'Manajemen Pengguna', href: '/admin/users', icon: '👥', route: 'admin.users.*', superAdminOnly: true },
            { name: 'Pembayaran', href: '/admin/pembayarans', icon: '💰', route: 'admin.pembayarans.*' },
            { name: 'Laporan', href: '/admin/laporan', icon: '📈', route: 'admin.laporan.*' },
            { name: 'Pengaturan', href: '/admin/settings', icon: '⚙️', route: 'admin.settings.*', superAdminOnly: true },
        ],
    },
]

function isActive(routePattern) {
    if (typeof window !== 'undefined' && typeof window.route === 'function') {
        return window.route().current(routePattern)
    }
    return false
}
</script>

<template>
    <div class="layout-wrapper">
        <!-- Backdrop Overlay on Mobile -->
        <transition
            enter-active-class="transition-opacity ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isMobile && sidebarOpen"
                class="sidebar-backdrop"
                @click="sidebarOpen = false"
                @touchstart.passive="sidebarOpen = false"
                aria-hidden="true"
            />
        </transition>

        <!-- Sidebar Navigation Drawer -->
        <aside
            :class="[
                'sidebar',
                {
                    'sidebar--collapsed': !sidebarOpen && !isMobile,
                    'sidebar--open': sidebarOpen,
                }
            ]"
        >
            <!-- Logo & Header -->
            <div class="sidebar-logo">
                <div class="logo-icon">🏫</div>
                <div v-if="sidebarOpen || isMobile" class="logo-text">
                    <span class="logo-title">PKBM BASARA</span>
                    <span class="logo-subtitle">Sistem Informasi</span>
                </div>
                <!-- Close Button on Mobile -->
                <button
                    v-if="isMobile"
                    type="button"
                    class="mobile-close-btn"
                    @click="sidebarOpen = false"
                    aria-label="Tutup menu navigasi"
                >
                    ✕
                </button>
            </div>

            <!-- Toggle Button (Desktop only) -->
            <button v-if="!isMobile" class="sidebar-toggle" @click="toggleSidebar" aria-label="Toggle sidebar">
                {{ sidebarOpen ? '◀' : '▶' }}
            </button>

            <!-- Navigation Links -->
            <nav class="sidebar-nav">
                <div v-for="group in navGroups" :key="group.label" class="nav-group">
                    <span v-if="sidebarOpen || isMobile" class="nav-group-label">{{ group.label }}</span>
                    <template v-for="item in group.items" :key="item.name">
                        <Link
                            v-if="!item.superAdminOnly || user?.is_super_admin"
                            :href="item.href"
                            :class="['nav-item', { 'nav-item--active': isActive(item.route) }]"
                            :title="!sidebarOpen && !isMobile ? item.name : ''"
                            @click="closeSidebarOnMobile"
                        >
                            <span class="nav-icon">{{ item.icon }}</span>
                            <span v-if="sidebarOpen || isMobile" class="nav-label">{{ item.name }}</span>
                            <span v-if="(sidebarOpen || isMobile) && isActive(item.route)" class="nav-indicator" />
                        </Link>
                    </template>
                </div>
            </nav>

            <!-- User Profile & Logout -->
            <div class="sidebar-user">
                <div class="user-avatar">
                    {{ user?.name?.charAt(0)?.toUpperCase() }}
                </div>
                <div v-if="sidebarOpen || isMobile" class="user-info">
                    <p class="user-name">{{ user?.name }}</p>
                    <p class="user-role flex items-center gap-1">
                        <span v-if="user?.is_super_admin" class="text-amber-300 font-bold">👑 Admin Utama</span>
                        <span v-else class="text-blue-200 font-semibold">🛡️ Staf Admin</span>
                    </p>
                </div>
                <Link
                    v-if="sidebarOpen || isMobile"
                    href="/logout"
                    method="post"
                    as="button"
                    class="logout-btn"
                    title="Logout"
                    @click="closeSidebarOnMobile"
                >
                    🚪
                </Link>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="main-wrapper">
            <!-- Top Bar -->
            <header class="topbar">
                <div class="topbar-left">
                    <button
                        type="button"
                        class="mobile-menu-btn"
                        @click.stop="toggleSidebar"
                        aria-label="Buka menu navigasi"
                    >
                        <span>☰</span>
                        <span class="mobile-menu-label">Menu</span>
                    </button>
                    <div class="breadcrumb truncate max-w-[140px] xs:max-w-[200px] sm:max-w-none">
                        <slot name="breadcrumb">
                            <span class="font-semibold text-slate-800">Dashboard</span>
                        </slot>
                    </div>
                </div>
                <div class="topbar-right">
                    <!-- Link Cepat Portal Verifikasi -->
                    <a
                        href="/verifikasi"
                        target="_blank"
                        class="px-2.5 sm:px-3 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 text-xs font-bold transition flex items-center gap-1.5"
                        title="Buka Portal Verifikasi Ijazah & Raport"
                    >
                        <span>🔍</span>
                        <span class="hidden md:inline">Verifikasi Ijazah</span>
                    </a>

                    <!-- Notifikasi -->
                    <button class="notif-btn" title="Notifikasi" aria-label="Notifikasi">
                        🔔 <span class="notif-badge">3</span>
                    </button>
                    <!-- Python API Status -->
                    <div class="api-status" title="Status Python API">
                        <span class="status-dot status-dot--online" />
                        <span class="status-label hidden xs:inline">API</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="main-content">
                <slot />
            </main>

            <!-- Mobile Bottom Quick Navigation Bar (Touch Friendly App Bar) -->
            <nav v-if="isMobile" class="admin-bottom-nav">
                <Link
                    href="/dashboard"
                    :class="['bottom-nav-item', { 'bottom-nav-item--active': isActive('dashboard') }]"
                >
                    <span class="bottom-nav-icon">🏠</span>
                    <span class="bottom-nav-text">Beranda</span>
                </Link>
                <Link
                    href="/admin/siswa"
                    :class="['bottom-nav-item', { 'bottom-nav-item--active': isActive('admin.siswa.*') }]"
                >
                    <span class="bottom-nav-icon">👨‍🎓</span>
                    <span class="bottom-nav-text">Siswa</span>
                </Link>
                <Link
                    href="/admin/absensi"
                    :class="['bottom-nav-item', { 'bottom-nav-item--active': isActive('admin.absensi.*') }]"
                >
                    <span class="bottom-nav-icon">✅</span>
                    <span class="bottom-nav-text">Presensi</span>
                </Link>
                <Link
                    href="/admin/nilais"
                    :class="['bottom-nav-item', { 'bottom-nav-item--active': isActive('admin.nilais.*') }]"
                >
                    <span class="bottom-nav-icon">📊</span>
                    <span class="bottom-nav-text">Nilai</span>
                </Link>
                <button
                    type="button"
                    @click.stop="toggleSidebar"
                    class="bottom-nav-item bottom-nav-item--menu"
                    aria-label="Buka semua menu navigasi"
                >
                    <span class="bottom-nav-icon">☰</span>
                    <span class="bottom-nav-text font-bold">Semua Menu</span>
                </button>
            </nav>
        </div>
    </div>
</template>

<style scoped>
*, *::before, *::after { box-sizing: border-box; }

.layout-wrapper {
    display: flex;
    min-height: 100vh;
    background: #f8fafc;
    font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
}

/* ================================
   SIDEBAR
================================ */
.sidebar {
    width: 260px;
    min-height: 100vh;
    background: linear-gradient(180deg, #1a2332 0%, #0f1923 100%);
    display: flex;
    flex-direction: column;
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    z-index: 100;
    transition: width 0.25s ease;
    overflow: hidden;
}

.sidebar--collapsed {
    width: 72px;
}

.sidebar-logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1.25rem 1.25rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

.logo-icon {
    font-size: 1.75rem;
    flex-shrink: 0;
    width: 40px;
    text-align: center;
}

.logo-title {
    display: block;
    color: white;
    font-size: 1.1rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    line-height: 1;
}

.logo-subtitle {
    display: block;
    color: rgba(255,255,255,0.4);
    font-size: 0.7rem;
    margin-top: 0.2rem;
}

.sidebar-toggle {
    background: none;
    border: none;
    color: rgba(255,255,255,0.3);
    font-size: 0.75rem;
    cursor: pointer;
    padding: 0.5rem;
    width: 100%;
    text-align: right;
    padding-right: 1.25rem;
}

.sidebar-nav {
    flex: 1;
    overflow-y: auto;
    padding: 0.75rem 0;
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.1) transparent;
}

.nav-group {
    margin-bottom: 0.25rem;
}

.nav-group-label {
    display: block;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.25);
    padding: 0.75rem 1.25rem 0.35rem;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.65rem 1.25rem;
    color: rgba(255,255,255,0.6);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.15s ease;
    position: relative;
    border-radius: 0;
    margin: 0 0.5rem;
    border-radius: 0.5rem;
}

.nav-item:hover {
    background: rgba(255,255,255,0.06);
    color: white;
}

.nav-item--active {
    background: rgba(59, 130, 246, 0.2);
    color: #60a5fa;
}

.nav-item--active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 60%;
    background: #3b82f6;
    border-radius: 0 2px 2px 0;
}

.nav-icon {
    font-size: 1rem;
    width: 20px;
    text-align: center;
    flex-shrink: 0;
}

.nav-label { flex: 1; white-space: nowrap; overflow: hidden; }
.nav-indicator { width: 6px; height: 6px; border-radius: 50%; background: #3b82f6; flex-shrink: 0; }

.sidebar-user {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    border-top: 1px solid rgba(255,255,255,0.08);
    margin-top: auto;
}

.user-avatar {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.user-name { color: white; font-size: 0.8rem; font-weight: 600; margin: 0; white-space: nowrap; overflow: hidden; }
.user-role { color: rgba(255,255,255,0.35); font-size: 0.7rem; margin: 0; }
.user-info { flex: 1; min-width: 0; }

.logout-btn {
    background: none;
    border: none;
    font-size: 1rem;
    cursor: pointer;
    opacity: 0.5;
    transition: opacity 0.15s;
    flex-shrink: 0;
}
.logout-btn:hover { opacity: 1; }

/* ================================
   MAIN CONTENT
================================ */
.main-wrapper {
    flex: 1;
    margin-left: 260px;
    transition: margin-left 0.25s ease;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.sidebar--collapsed + .main-wrapper,
.layout-wrapper:has(.sidebar--collapsed) .main-wrapper {
    margin-left: 72px;
}

.topbar {
    height: 60px;
    background: white;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.5rem;
    position: sticky;
    top: 0;
    z-index: 50;
}

.topbar-left { display: flex; align-items: center; gap: 1rem; }
.topbar-right { display: flex; align-items: center; gap: 1rem; }

.mobile-menu-btn {
    display: none;
    background: none;
    border: none;
    font-size: 1.25rem;
    cursor: pointer;
}

.breadcrumb {
    font-size: 0.875rem;
    color: #64748b;
}

.notif-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.1rem;
    position: relative;
}

.notif-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #ef4444;
    color: white;
    font-size: 0.6rem;
    font-weight: 700;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.api-status {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.75rem;
    color: #64748b;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-dot--online { background: #10b981; animation: pulse 2s infinite; }
.status-dot--offline { background: #ef4444; }

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.main-content {
    flex: 1;
    padding: 0;
    min-width: 0;
}

/* Mobile Close Button inside Sidebar Header */
.mobile-close-btn {
    margin-left: auto;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.15s;
    flex-shrink: 0;
}
.mobile-close-btn:hover {
    background: rgba(255, 255, 255, 0.25);
}

/* Mobile Backdrop Overlay */
.sidebar-backdrop {
    display: none;
}

@media (max-width: 1024px) {
    .sidebar-backdrop {
        display: block;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 90;
        touch-action: manipulation;
    }

    .sidebar {
        position: fixed !important;
        top: 0;
        bottom: 0;
        left: 0;
        width: 280px !important;
        max-width: 85vw;
        height: 100vh;
        height: 100dvh;
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.3s;
        box-shadow: none;
        z-index: 100;
        visibility: hidden;
        pointer-events: none;
    }
    .sidebar.sidebar--open {
        transform: translateX(0) !important;
        visibility: visible !important;
        pointer-events: auto !important;
        box-shadow: 0 0 50px rgba(0, 0, 0, 0.65);
    }
    .main-wrapper {
        margin-left: 0 !important;
        width: 100%;
        max-width: 100vw;
        min-width: 0;
        overflow-x: hidden;
    }
    .topbar {
        padding: 0 0.85rem;
    }
    .mobile-menu-btn {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0 0.75rem;
        height: 38px;
        border-radius: 10px;
        background: #0f172a;
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: white;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.25);
    }
    .mobile-menu-btn:hover {
        background: #1e293b;
    }
    .mobile-menu-btn:active {
        transform: scale(0.95);
    }
    .mobile-menu-label {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .main-content {
        padding-bottom: 72px;
    }

    .admin-bottom-nav {
        display: flex;
        align-items: center;
        justify-content: space-around;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-top: 1px solid #e2e8f0;
        z-index: 40;
        padding-bottom: env(safe-area-inset-bottom, 0px);
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.06);
    }
    .bottom-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
        color: #64748b;
        text-decoration: none;
        background: none;
        border: none;
        cursor: pointer;
        flex: 1;
        height: 100%;
        transition: all 0.15s ease;
    }
    .bottom-nav-item:active {
        transform: scale(0.92);
    }
    .bottom-nav-item--active {
        color: #2563eb;
    }
    .bottom-nav-item--active .bottom-nav-text {
        font-weight: 800;
        color: #2563eb;
    }
    .bottom-nav-item--menu {
        color: #0f172a;
    }
    .bottom-nav-icon {
        font-size: 1.15rem;
        line-height: 1;
    }
    .bottom-nav-text {
        font-size: 0.65rem;
        line-height: 1;
        letter-spacing: -0.01em;
    }
}
</style>
