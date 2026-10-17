<script setup>
/**
 * StatCard Component — Dashboard
 * Kartu statistik dengan warna, ikon, dan trend
 */

const props = defineProps({
    title: String,
    value: [Number, String],
    icon: String,
    color: {
        type: String,
        default: 'blue',
        validator: (v) => ['blue', 'green', 'purple', 'orange', 'red', 'teal'].includes(v),
    },
    trend: String,
    trendUp: Boolean,
    subtitle: String,
})
</script>

<template>
    <div :class="['stat-card', `stat-card--${color}`]">
        <div class="stat-header">
            <div class="stat-icon">{{ icon }}</div>
            <div v-if="trend" :class="['stat-trend', { 'trend--up': trendUp, 'trend--down': !trendUp }]">
                {{ trendUp ? '↑' : '↓' }} {{ trend }}
            </div>
        </div>
        <div class="stat-value">
            <span class="value-number">{{ typeof value === 'number' ? value.toLocaleString('id-ID') : value }}</span>
        </div>
        <div class="stat-footer">
            <p class="stat-title">{{ title }}</p>
            <p v-if="subtitle" class="stat-subtitle">{{ subtitle }}</p>
        </div>
    </div>
</template>

<style scoped>
.stat-card {
    background: white;
    border-radius: 1rem;
    padding: 1.25rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.07);
    border: 1px solid #e2e8f0;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    border-radius: 1rem 1rem 0 0;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.stat-card--blue::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
.stat-card--green::before { background: linear-gradient(90deg, #10b981, #34d399); }
.stat-card--purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
.stat-card--orange::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.stat-card--red::before { background: linear-gradient(90deg, #ef4444, #f87171); }
.stat-card--teal::before { background: linear-gradient(90deg, #14b8a6, #2dd4bf); }

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}

.stat-icon {
    font-size: 1.75rem;
    line-height: 1;
}

.stat-trend {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.2rem 0.6rem;
    border-radius: 1rem;
}

.trend--up { background: #dcfce7; color: #15803d; }
.trend--down { background: #fee2e2; color: #dc2626; }

.value-number {
    font-size: 2.25rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1;
    display: block;
    font-variant-numeric: tabular-nums;
}

.stat-footer { margin-top: 0.5rem; }

.stat-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #475569;
    margin: 0;
}

.stat-subtitle {
    font-size: 0.75rem;
    color: #94a3b8;
    margin: 0.2rem 0 0;
}
</style>
