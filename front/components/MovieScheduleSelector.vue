<template>
    <div class="bg-dark-secondary rounded-xl p-4 md:p-6 mt-6 md:mt-8">
        <h2 class="text-xl md:text-2xl font-bold mb-4 md:mb-6">Selecciona día y hora</h2>

        <!-- Selector de fechas -->
        <div class="flex justify-center mt-[20px] md:mt-[40px] mb-6 md:mb-8">
            <div class="flex items-center w-full max-w-4xl">
                <button @click="previousWeek" :class="[
                    'text-light-main p-2 md:p-3',
                    !canGoBack && 'opacity-50 cursor-not-allowed'
                ]" :disabled="!canGoBack">
                    <i class="bi bi-chevron-left text-lg md:text-xl"></i>
                </button>

                <div class="flex space-x-4 flex-1 justify-center overflow-x-auto">
                    <div v-for="day in visibleDays" :key="day.date" :class="[
                        'cursor-pointer px-2 pb-4 md:px-4 lg:px-8 min-w-[100px] text-center',
                        day.date === selectedDate
                            ? 'text-blue-500 border-b-2 border-blue-500'
                            : 'text-light-main',
                        !day.hasScreenings && 'opacity-40 cursor-not-allowed'
                    ]" @click="day.hasScreenings && (selectedDate = day.date)">
                        <div class="text-sm font-semibold">{{ day.formatted }}</div>
                        <div class="text-xs mt-1">{{ day.weekday }}</div>
                    </div>
                </div>

                <button @click="nextWeek" :class="[
                    'text-light-main p-2 md:p-3',
                    !canGoForward && 'opacity-50 cursor-not-allowed'
                ]" :disabled="!canGoForward">
                    <i class="bi bi-chevron-right text-lg md:text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Horarios -->
        <div v-if="selectedDateScreenings.length" class="mt-[30px] md:mt-[60px] grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
            <div v-for="screening in selectedDateScreenings" :key="screening.id"
                class="bg-gray-800 p-4 md:p-8 md:mx-8 rounded-lg transition-colors">
                <div class="flex justify-between items-center mb-4">
                    <span class="font-semibold text-sm md:text-lg">{{ screening.time }}</span>
                    <span class="text-sm md:text-lg text-gray-400">{{ screening.room.name }}</span>
                </div>
                <div class="flex justify-between items-center md:mt-6">
                    <span class="flex items-center text-sm md:text-lg">
                        <i class="bi bi-ticket-detailed mr-1 text-lg hidden md:block"></i>
                        {{ screening.room.availableSeats }} Butaques Lliures
                    </span>
                    <button @click="navigateTo(`/screening/${screening.id}`)"
                        class="bg-primary-600 hover:bg-primary-700 px-4 py-2 rounded-lg text-sm md:text-lg">
                        Seleccionar
                    </button>
                </div>
            </div>
        </div>

        <div v-else class="text-center text-gray-400 py-6">
            No hi ha sessions disponibles per aquest dia
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useCalendarRange } from '@/composables/useCalendarRange';

const props = defineProps({
    screenings: {
        type: Array,
        required: true,
        default: () => []
    }
});

const {
    stepSize,
    maxDate,
    today,
    getFormattedDate
} = useCalendarRange();

const emit = defineEmits(['select']);

// Lógica de estado
const currentStart = ref(0); // Índice inicial en uniqueDates
const selectedDate = ref('');

// Fechas únicas con proyecciones
const uniqueDates = computed(() => {
    const dates = new Set();
    props.screenings?.forEach(s => dates.add(s.date));
    return Array.from(dates).sort();
});

// Días visibles adaptados al dispositivo
const visibleDates = computed(() => {
    return uniqueDates.value.slice(currentStart.value, currentStart.value + stepSize.value);
});

// Control de navegación
const canGoBack = computed(() => currentStart.value > 0);
const canGoForward = computed(() => currentStart.value + stepSize.value < uniqueDates.value.length);

// Navegación por bloques
function previousWeek() {
    currentStart.value = Math.max(0, currentStart.value - stepSize.value);
}

function nextWeek() {
    currentStart.value = Math.min(
        uniqueDates.value.length - stepSize.value,
        currentStart.value + stepSize.value
    );
}

const hasScreenings = (date) => {
    return props.screenings.some(s => s.date === date);
};

const visibleDays = computed(() => {
    return visibleDates.value.map(date => {
        const d = new Date(date);
        return {
            date,
            formatted: d.toLocaleDateString('ca-CA', { day: 'numeric', month: 'short' }),
            weekday: d.toLocaleDateString('ca-CA', { weekday: 'short' }).toUpperCase(),
            hasScreenings: hasScreenings(date)  // <--- Nueva propiedad
        };
    });
});

const selectedDateScreenings = computed(() => {
    return props.screenings.filter(screening => screening.date === selectedDate.value);
});

// Inicialización
watch(uniqueDates, (newVal) => {
    if (newVal.length > 0) {
        selectedDate.value = newVal[0];
        currentStart.value = 0;
    }
}, { immediate: true });

function selectScreening(screening) {
    emit('select', screening);
}
</script>