<script setup>
/**
 * Admin Layout — Sistem PKBM BASARA
 * Layout utama dengan sidebar navigasi modern, collapsible dropdown menu canggih,
 * dan responsive mobile drawer & quick app bar.
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

// Collapsible dropdown groups state
const expandedGroups = ref({
    'Utama': true,
    'Akademik': true,
    'Pembelajaran (LMS)': false,
    'Penilaian & Ijazah': false,
    'Administrasi': false,
})

const navGroups = [
    {
        label: 'Utama',
        icon: '⚡',
        badge: 'Core',
        items: [
            { name: 'Dashboard', href: '/dashboard', icon: '🏠', route: 'dashboard' },
            { name: 'Pengumuman', href: '/admin/pengumumans', icon: '📢', route: 'admin.pengumumans.*' },
        ],
    },
    {
        label: 'Akademik',
        icon: '🎓',
        badge: 'Akademik',
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
        icon: '💻',
        badge: 'LMS',
        items: [
            { name: 'Modul Belajar', href: '/admin/materi', icon: '📖', route: 'admin.materi.*' },
            { name: 'Tugas Siswa', href: '/admin/tugas', icon: '📝', route: 'admin.tugas.*' },
        ],
    },
    {
        label: 'Penilaian & Ijazah',
        icon: '📜',
        badge: 'Legalitas',
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
        icon: '⚙️',
        badge: 'Sistem',
        items: [
            { name: 'Manajemen Pengguna', href: '/admin/users', icon: '👥', route: 'admin.users.*', superAdminOnly: true },
            { name: 'Pembayaran', href: '/admin/pembayarans', icon: '💰', route: 'admin.pembayarans.*' },
            { name: 'Laporan', href: '/admin/laporan', icon: '📈', route: 'admin.laporan.*' },
            { name: 'Profil & Akun Saya', href: '/profile', icon: '👤', route: 'profile.*' },
            { name: 'Pengaturan', href: '/admin/settings', icon: '⚙️', route: 'admin.settings.*', superAdminOnly: true },
        ],
    },
]

function isActive(routePattern, href) {
    if (typeof window !== 'undefined' && typeof window.route === 'function') {
        try {
            if (window.route().current(routePattern)) return true
        } catch (e) {}
    }
    if (href && page.url) {
        if (href === '/dashboard' && page.url === '/dashboard') return true
        if (href !== '/dashboard' && page.url.startsWith(href)) return true
    }
    return false
}

function isGroupActive(group) {
    return group.items.some(item => {
        if (item.superAdminOnly && !user.value?.is_super_admin) return false
        return isActive(item.route, item.href)
    })
}

function getVisibleItems(group) {
    return group.items.filter(item => !item.superAdminOnly || user.value?.is_super_admin)
}

function toggleGroup(groupLabel) {
    // If sidebar is collapsed on desktop, clicking a group expands the sidebar first
    if (!sidebarOpen.value && !isMobile.value) {
        sidebarOpen.value = true
        expandedGroups.value[groupLabel] = true
        return
    }
    expandedGroups.value[groupLabel] = !expandedGroups.value[groupLabel]
}

function autoExpandActiveGroup() {
    navGroups.forEach(group => {
        if (isGroupActive(group)) {
            expandedGroups.value[group.label] = true
        }
    })
}

let removeNavListener = null

onMounted(() => {
    if (typeof window !== 'undefined') {
        isMobile.value = window.innerWidth <= 1024
        sidebarOpen.value = window.innerWidth > 1024
        window.addEventListener('resize', handleResize)
    }

    autoExpandActiveGroup()

    // Auto-close drawer on mobile when navigating to another page and keep active group expanded
    removeNavListener = router.on('navigate', () => {
        if (typeof window !== 'undefined' && window.innerWidth <= 1024) {
            sidebarOpen.value = false
        }
        autoExpandActiveGroup()
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
                <div class="logo-icon-wrapper">
                    <span class="logo-icon">🏫</span>
                    <span class="logo-glow"></span>
                </div>
                <div v-if="sidebarOpen || isMobile" class="logo-text">
                    <div class="flex items-center gap-1.5">
                        <span class="logo-title">PKBM BASARA</span>
                        <span class="logo-chip">PRO</span>
                    </div>
                    <span class="logo-subtitle">Sistem Informasi Akademik</span>
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
            <button
                v-if="!isMobile"
                class="sidebar-toggle"
                @click="toggleSidebar"
                :title="sidebarOpen ? 'Perkecil Menu' : 'Perluas Menu'"
                aria-label="Toggle sidebar"
            >
                <span class="toggle-icon">{{ sidebarOpen ? '◀' : '▶' }}</span>
                <span v-if="sidebarOpen" class="toggle-label">Perkecil Tampilan</span>
            </button>

            <!-- Navigation Links with Collapsible Dropdown Accordions -->
            <nav class="sidebar-nav">
                <div
                    v-for="group in navGroups"
                    :key="group.label"
                    :class="[
                        'nav-group',
                        {
                            'nav-group--active': isGroupActive(group),
                            'nav-group--open': expandedGroups[group.label],
                        }
                    ]"
                >
                    <!-- Group Header Button (Accordion Trigger) -->
                    <button
                        type="button"
                        class="nav-group-trigger"
                        @click="toggleGroup(group.label)"
                        :title="!sidebarOpen && !isMobile ? `${group.label} (${getVisibleItems(group).length} menu)` : ''"
                        :aria-expanded="expandedGroups[group.label]"
                    >
                        <div class="trigger-left">
                            <span class="group-icon-badge">{{ group.icon }}</span>
                            <span v-if="sidebarOpen || isMobile" class="group-title truncate">{{ group.label }}</span>
                        </div>

                        <div v-if="sidebarOpen || isMobile" class="trigger-right">
                            <span class="group-count-badge">
                                {{ getVisibleItems(group).length }}
                            </span>
                            <svg
                                class="chevron-icon"
                                :class="{ 'chevron-icon--rotated': expandedGroups[group.label] }"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                width="14"
                                height="14"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </button>

                    <!-- Collapsible Dropdown Menu Items Container -->
                    <div
                        v-if="sidebarOpen || isMobile"
                        :class="[
                            'nav-group-items',
                            { 'nav-group-items--expanded': expandedGroups[group.label] }
                        ]"
                    >
                        <div class="nav-group-items-inner">
                            <div class="nav-tree-line">
                                <template v-for="item in group.items" :key="item.name">
                                    <Link
                                        v-if="!item.superAdminOnly || user?.is_super_admin"
                                        :href="item.href"
                                        :class="['nav-item', { 'nav-item--active': isActive(item.route, item.href) }]"
                                        @click="closeSidebarOnMobile"
                                    >
                                        <div class="nav-item-content">
                                            <span class="nav-icon">{{ item.icon }}</span>
                                            <span class="nav-label">{{ item.name }}</span>
                                        </div>
                                        <div v-if="isActive(item.route, item.href)" class="nav-active-pill">
                                            <span class="nav-active-dot"></span>
                                        </div>
                                    </Link>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- User Profile & Logout (Executive Glass Card) -->
            <div class="sidebar-user">
                <Link
                    href="/profile"
                    class="user-profile-link group"
                    title="Buka Pengaturan Profil & Kata Sandi Mandiri"
                    @click="closeSidebarOnMobile"
                >
                    <div class="user-avatar-wrapper">
                        <div class="user-avatar">
                            {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                        </div>
                        <span class="user-online-badge"></span>
                    </div>

                    <div v-if="sidebarOpen || isMobile" class="user-info">
                        <p class="user-name group-hover:text-blue-300 transition truncate">{{ user?.name }}</p>
                        <p class="user-role flex items-center gap-1">
                            <span v-if="user?.is_super_admin" class="role-badge role-badge--admin">👑 Admin Utama</span>
                            <span v-else class="role-badge role-badge--staff">🛡️ Staf Admin</span>
                        </p>
                    </div>
                </Link>

                <div v-if="sidebarOpen || isMobile" class="sidebar-user-actions">
                    <Link
                        href="/profile"
                        class="user-action-btn"
                        title="Ubah Profil & Kata Sandi Mandiri"
                        @click="closeSidebarOnMobile"
                    >
                        ⚙️
                    </Link>
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="user-action-btn user-action-btn--logout"
                        title="Keluar dari Sistem"
                        @click="closeSidebarOnMobile"
                    >
                        🚪
                    </Link>
                </div>
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
                        class="px-2.5 sm:px-3 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 text-xs font-bold transition flex items-center gap-1.5 shadow-sm"
                        title="Buka Portal Verifikasi Ijazah & Raport"
                    >
                        <span>🔍</span>
                        <span class="hidden md:inline">Verifikasi Ijazah</span>
                    </a>

                    <!-- Link Cepat Profil & Sandi Mandiri -->
                    <Link
                        href="/profile"
                        class="px-2.5 sm:px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold transition flex items-center gap-1.5 shadow-sm"
                        title="Profil & Ganti Kata Sandi Mandiri"
                    >
                        <span>👤</span>
                        <span class="hidden sm:inline">Profil & Sandi</span>
                    </Link>

                    <!-- Notifikasi -->
                    <button class="notif-btn" title="Notifikasi" aria-label="Notifikasi">
                        🔔 <span class="notif-badge">3</span>
                    </button>
                    <!-- Python API Status -->
                    <div class="api-status" title="Status Python ML API Engine">
                        <span class="status-dot status-dot--online" />
                        <span class="status-label hidden xs:inline font-mono text-[11px] font-semibold text-slate-600">ML API</span>
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
                    :class="['bottom-nav-item', { 'bottom-nav-item--active': isActive('dashboard', '/dashboard') }]"
                >
                    <span class="bottom-nav-icon">🏠</span>
                    <span class="bottom-nav-text">Beranda</span>
                </Link>
                <Link
                    href="/admin/siswa"
                    :class="['bottom-nav-item', { 'bottom-nav-item--active': isActive('admin.siswa.*', '/admin/siswa') }]"
                >
                    <span class="bottom-nav-icon">👨‍🎓</span>
                    <span class="bottom-nav-text">Siswa</span>
                </Link>
                <Link
                    href="/admin/absensi"
                    :class="['bottom-nav-item', { 'bottom-nav-item--active': isActive('admin.absensi.*', '/admin/absensi') }]"
                >
                    <span class="bottom-nav-icon">✅</span>
                    <span class="bottom-nav-text">Presensi</span>
                </Link>
                <Link
                    href="/admin/nilais"
                    :class="['bottom-nav-item', { 'bottom-nav-item--active': isActive('admin.nilais.*', '/admin/nilais') }]"
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

/* ===================================================================
   SIDEBAR (FUTURISTIC CYBER-EXECUTIVE DARK GLASS)
=================================================================== */
.sidebar {
    width: 275px;
    min-height: 100vh;
    background: linear-gradient(180deg, #0d1527 0%, #080d19 45%, #040711 100%);
    border-right: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    flex-direction: column;
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    z-index: 100;
    transition: width 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    overflow: hidden;
    box-shadow: 4px 0 24px rgba(0, 0, 0, 0.35);
}

.sidebar--collapsed {
    width: 76px;
}

/* Header & Brand */
.sidebar-logo {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 1.25rem 1.15rem 1.1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    background: rgba(255, 255, 255, 0.02);
}

.logo-icon-wrapper {
    position: relative;
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(99, 102, 241, 0.15));
    border: 1px solid rgba(96, 165, 250, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.2);
}

.logo-icon {
    font-size: 1.4rem;
    line-height: 1;
}

.logo-text {
    flex: 1;
    min-width: 0;
}

.logo-title {
    color: #ffffff;
    font-size: 1.05rem;
    font-weight: 900;
    letter-spacing: 0.04em;
    line-height: 1.1;
}

.logo-chip {
    font-size: 0.6rem;
    font-weight: 800;
    padding: 0.1rem 0.35rem;
    border-radius: 4px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #0f172a;
    letter-spacing: 0.05em;
    line-height: 1;
}

.logo-subtitle {
    display: block;
    color: rgba(255, 255, 255, 0.45);
    font-size: 0.68rem;
    font-weight: 500;
    margin-top: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Toggle Desktop Button */
.sidebar-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(255, 255, 255, 0.02);
    border: none;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    color: rgba(255, 255, 255, 0.4);
    font-size: 0.72rem;
    cursor: pointer;
    padding: 0.55rem 1rem;
    transition: all 0.2s ease;
}
.sidebar-toggle:hover {
    background: rgba(255, 255, 255, 0.06);
    color: #ffffff;
}
.toggle-icon {
    font-size: 0.7rem;
}
.toggle-label {
    font-size: 0.68rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

/* ===================================================================
   NAVIGATION & COLLAPSIBLE DROPDOWNS
=================================================================== */
.sidebar-nav {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 0.85rem 0.65rem;
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.12) transparent;
}
.sidebar-nav::-webkit-scrollbar {
    width: 4px;
}
.sidebar-nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 4px;
}

.nav-group {
    margin-bottom: 0.45rem;
    border-radius: 12px;
    transition: background 0.2s ease;
}

/* Group Trigger Button (Dropdown Header) */
.nav-group-trigger {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.6rem 0.65rem;
    background: transparent;
    border: 1px solid transparent;
    border-radius: 10px;
    color: rgba(255, 255, 255, 0.75);
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    user-select: none;
}

.nav-group-trigger:hover {
    background: rgba(255, 255, 255, 0.05);
    color: #ffffff;
    border-color: rgba(255, 255, 255, 0.08);
}

.trigger-left {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    min-width: 0;
}

.group-icon-badge {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    flex-shrink: 0;
    transition: all 0.2s ease;
}

.nav-group-trigger:hover .group-icon-badge {
    background: rgba(59, 130, 246, 0.15);
    border-color: rgba(96, 165, 250, 0.3);
    transform: scale(1.05);
}

.group-title {
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: rgba(255, 255, 255, 0.65);
    transition: color 0.2s ease;
}

.nav-group-trigger:hover .group-title {
    color: #ffffff;
}

.trigger-right {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    flex-shrink: 0;
}

.group-count-badge {
    font-size: 0.65rem;
    font-weight: 700;
    font-family: monospace;
    padding: 0.15rem 0.4rem;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.05);
    color: rgba(255, 255, 255, 0.4);
    border: 1px solid rgba(255, 255, 255, 0.06);
    line-height: 1;
    transition: all 0.2s ease;
}

.chevron-icon {
    color: rgba(255, 255, 255, 0.4);
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), color 0.2s ease;
}

.chevron-icon--rotated {
    transform: rotate(180deg);
    color: #60a5fa;
}

/* Active State on Group Header */
.nav-group--active .nav-group-trigger {
    background: rgba(30, 58, 138, 0.2);
    border-color: rgba(59, 130, 246, 0.25);
}
.nav-group--active .group-title {
    color: #93c5fd;
}
.nav-group--active .group-icon-badge {
    background: rgba(59, 130, 246, 0.2);
    border-color: rgba(96, 165, 250, 0.4);
    box-shadow: 0 0 10px rgba(59, 130, 246, 0.2);
}
.nav-group--active .group-count-badge {
    background: rgba(59, 130, 246, 0.15);
    color: #93c5fd;
    border-color: rgba(59, 130, 246, 0.3);
}

/* GPU-Accelerated CSS Grid Accordion Slider */
.nav-group-items {
    display: grid;
    grid-template-rows: 0fr;
    transition: grid-template-rows 0.24s cubic-bezier(0.16, 1, 0.3, 1);
}

.nav-group-items--expanded {
    grid-template-rows: 1fr;
}

.nav-group-items-inner {
    overflow: hidden;
    min-height: 0;
}

.nav-tree-line {
    position: relative;
    margin-left: 1.15rem;
    padding-left: 0.5rem;
    border-left: 1.5px solid rgba(59, 130, 246, 0.15);
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
}

/* Individual Nav Items */
.nav-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0.65rem;
    margin: 0.18rem 0;
    color: rgba(255, 255, 255, 0.65);
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
}

.nav-item:hover {
    background: rgba(255, 255, 255, 0.06);
    color: #ffffff;
    transform: translateX(3px);
}

.nav-item-content {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    min-width: 0;
}

.nav-icon {
    font-size: 0.95rem;
    width: 20px;
    text-align: center;
    flex-shrink: 0;
}

.nav-label {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Active Item Style (Illuminated Pill) */
.nav-item--active {
    background: linear-gradient(90deg, rgba(37, 99, 235, 0.28) 0%, rgba(79, 70, 229, 0.15) 100%);
    border: 1px solid rgba(96, 165, 250, 0.35);
    color: #ffffff !important;
    font-weight: 700;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.2);
}

.nav-item--active .nav-label {
    color: #93c5fd;
}

.nav-active-pill {
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-active-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #38bdf8;
    box-shadow: 0 0 10px #38bdf8, 0 0 4px #60a5fa;
}

/* ===================================================================
   SIDEBAR USER FOOTER (EXECUTIVE LUXURY)
=================================================================== */
.sidebar-user {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.85rem 0.85rem;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(2, 6, 23, 0.6);
    backdrop-filter: blur(8px);
    margin-top: auto;
}

.user-profile-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
    min-width: 0;
    text-decoration: none;
}

.user-avatar-wrapper {
    position: relative;
    flex-shrink: 0;
}

.user-avatar {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #2563eb, #7c3aed);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 800;
    font-size: 0.875rem;
    border: 2px solid rgba(255, 255, 255, 0.15);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
}

.user-online-badge {
    position: absolute;
    bottom: -1px;
    right: -1px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #10b981;
    border: 2px solid #0f172a;
}

.user-info {
    flex: 1;
    min-width: 0;
}

.user-name {
    color: #ffffff;
    font-size: 0.82rem;
    font-weight: 700;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
}

.user-role {
    margin: 0.15rem 0 0;
    font-size: 0.68rem;
}

.role-badge {
    display: inline-block;
    font-size: 0.65rem;
    font-weight: 700;
}
.role-badge--admin {
    color: #fbbf24;
}
.role-badge--staff {
    color: #93c5fd;
}

.sidebar-user-actions {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    flex-shrink: 0;
}

.user-action-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.85rem;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
}
.user-action-btn:hover {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    border-color: rgba(255, 255, 255, 0.2);
}
.user-action-btn--logout:hover {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.4);
    color: #fca5a5;
}

/* ===================================================================
   MAIN CONTENT & TOPBAR
=================================================================== */
.main-wrapper {
    flex: 1;
    margin-left: 275px;
    transition: margin-left 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.sidebar--collapsed + .main-wrapper,
.layout-wrapper:has(.sidebar--collapsed) .main-wrapper {
    margin-left: 76px;
}

.topbar {
    height: 60px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.5rem;
    position: sticky;
    top: 0;
    z-index: 50;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

.topbar-left { display: flex; align-items: center; gap: 1rem; }
.topbar-right { display: flex; align-items: center; gap: 0.85rem; }

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
    padding: 0.25rem;
}

.notif-badge {
    position: absolute;
    top: -3px;
    right: -3px;
    background: #ef4444;
    color: white;
    font-size: 0.6rem;
    font-weight: 800;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 6px rgba(239, 68, 68, 0.5);
}

.api-status {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.75rem;
    color: #64748b;
    padding: 0.25rem 0.6rem;
    border-radius: 8px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}
.status-dot--online { background: #10b981; animation: pulse 2s infinite; }
.status-dot--offline { background: #ef4444; }

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(0.9); }
}

.main-content {
    flex: 1;
    padding: 0;
    min-width: 0;
}

/* Mobile Close Button */
.mobile-close-btn {
    margin-left: auto;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
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
    background: rgba(255, 255, 255, 0.2);
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
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        z-index: 90;
        touch-action: manipulation;
    }

    .sidebar {
        position: fixed !important;
        top: 0;
        bottom: 0;
        left: 0;
        width: 290px !important;
        max-width: 88vw;
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
        box-shadow: 0 0 50px rgba(0, 0, 0, 0.75);
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
