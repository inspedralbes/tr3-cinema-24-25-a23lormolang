<template>
    <div class="bg-light-secondary dark:bg-dark-secondary p-6 rounded-xl">
        <div class="flex flex-col md:flex-row justify-between items-start mb-6">
            <h2 class="text-2xl font-bold text-dark-main dark:text-light-main mb-4 md:mb-0">
                Estadístiques de vendes
            </h2>

            <div class="flex gap-2">
                <button v-for="period in periods" :key="period" @click="selectedPeriod = period" :class="[
                    'px-4 py-2 rounded-lg transition-colors cursor-pointer',
                    selectedPeriod === period
                        ? 'bg-primary-500 text-white'
                        : 'bg-light-quaternary dark:bg-dark-tertiary hover:bg-gray-600/50 dark:hover:bg-dark-main text-dark-main dark:text-light-main hover:bg-opacity-50'
                ]">
                    {{ periodLabels[period] }}
                </button>
            </div>
        </div>

        <div v-if="loading" class="text-center py-8">
            <Spinner size="xl" color="primary" />
        </div>

        <div v-else>
            <div class="bg-light-main dark:bg-dark-main p-4 rounded-xl mb-6">
                <div class="flex items-center justify-center h-64">
                    <canvas ref="chartCanvas" :key="`chart-${selectedPeriod}`" class="w-full h-full"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <StatCard title="Mitjana del període" :value="formatCurrency(stats.average)" icon="bi-graph-up" />
                <StatCard title="Vendes totals" :value="formatCurrency(totalSales)" icon="bi-currency-euro" />
                <StatCard title="Període analitzat" :value="periodLabels[selectedPeriod]" icon="bi-calendar" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed, nextTick } from 'vue';
import { Chart } from 'chart.js/auto';
import { useAuthStore } from '@/stores/authStore';
import Spinner from '@/components/Spinner.vue';

const authStore = useAuthStore();
const { $statCommunicationManager } = useNuxtApp();

const chartCanvas = ref(null);
let chartInstance = null;

const periods = ['week', 'month', 'year'];
const periodLabels = {
    week: 'Última setmana',
    month: 'Últim mes',
    year: 'Any actual'
};

const selectedPeriod = ref('week');
const stats = ref({ labels: [], data: [], average: 0 });
const loading = ref(false);

const totalSales = computed(() => stats.value.data.reduce((a, b) => a + b, 0));

const destroyChart = () => {
    if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('ca-ES', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2
    }).format(value);
};

const loadChart = () => {
    try {
        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }

        const ctx = chartCanvas.value.getContext('2d');

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: stats.value.labels,
                datasets: [{
                    label: 'Vendes',
                    data: stats.value.data,
                    borderColor: '#4F46E5',
                    tension: 0.4,
                    fill: false,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true },
                    x: { }
                }
            }
        });

    } catch (error) {
        console.error('Error al cargar el gráfico:', error);
    }
};

const fetchStats = async () => {
    loading.value = true;
    try {
        destroyChart();

        const data = await $statCommunicationManager.getStatsSales(selectedPeriod.value);

        stats.value = {
            labels: data?.labels || [],
            data: data?.data?.map(Number) || [],
            average: Number(data?.average) || 0
        };

        // 1. Desactivar loading ANTES de renderizar
        loading.value = false; 

        // 2. Esperar 2 ciclos de actualización del DOM
        await nextTick();   

        // 3. Renderizar después de que el canvas esté disponible
        loadChart();

    } catch (error) {
        console.error('Error:', error);
        stats.value = { labels: [], data: [], average: 0 };
        loading.value = false;
    }
};
// Iniciar watch
watch(selectedPeriod, fetchStats, { immediate: true });
onMounted(async () => {
    if (!authStore.isAuthenticated) navigateTo('/admin/login');
});
</script>