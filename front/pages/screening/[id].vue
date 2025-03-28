<template>
    <div v-if="loading" class="text-center py-8">
        <Spinner size="xl" color="primary" />
    </div>
    <div v-else>
        <div class="min-h-screen bg-light-main dark:bg-dark-main py-12">
            <div class="max-w-7xl mx-auto px-4">
                <!-- Encabezado simplificado -->
                <div class="bg-light-secondary dark:bg-dark-secondary rounded-xl shadow-lg p-6 mb-8">
                    <div
                        class="flex flex-col md:flex-row justify-center md:justify-between items-center gap-4 text-center md:text-left">
                        <!-- Bloque izquierdo -->
                        <div class="md:text-left">
                            <h1 class="cursor-pointer text-2xl font-bold text-primary-600 hover:text-primary-700"
                                @click="navigateTo(`/movies/${screening.movie.id}`)">
                                {{ screening?.movie?.title }}
                            </h1>
                            <p class="text-dark-main dark:text-light-main mt-1">
                                {{ screening?.room?.name }}
                            </p>
                        </div>

                        <!-- Bloque derecho -->
                        <div class="md:text-right">
                            <p class="text-dark-main dark:text-light-main">
                                {{ formatDate(screening?.screening?.date) }}
                            </p>
                            <p class="text-dark-main dark:text-light-main mt-1">
                                {{ screening?.screening?.time }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Mapa de butaques mejorado -->
                <div class="bg-light-secondary dark:bg-dark-secondary rounded-xl shadow-lg p-8">
                    <h2 class="text-xl font-semibold text-dark-main dark:text-light-main mb-6">
                        Selecciona les teves butaques
                    </h2>

                    <div class="overflow-x-auto pb-4">
                        <div class="mx-auto w-fit">
                            <!-- Filas de butacas -->
                            <div v-for="row in seatRows" :key="row" class="flex items-center mb-4">
                                <span class="text-dark-main dark:text-light-main mr-3 w-6 text-right">{{ row }}</span>
                                <div class="flex gap-2">
                                    <div v-for="seat in rowSeats(row)" :key="seat.id" @click="toggleSeat(seat)" :class="[
                                        'relative cursor-pointer transition-transform hover:scale-110',
                                        seat.is_occupied && 'opacity-50 !cursor-not-allowed'
                                    ]">
                                        <!-- SVG de la butaca -->
                                        <svg class="w-12 h-14 hidden md:block" viewBox="0 0 24 24">
                                            <rect :class="[selectedSeats.has(seat.id)
                                                ? 'fill-green-400'
                                                : seat.type === 'vip'
                                                    ? 'fill-purple-400'
                                                    : 'fill-dark-tertiary dark:fill-light-tertiary',
                                            seat.is_occupied && '!fill-red-400'
                                            ]" x="4" y="22" width="16" height="4" rx="1" />
                                            <rect :class="[selectedSeats.has(seat.id)
                                                ? 'fill-green-400'
                                                : seat.type === 'vip'
                                                    ? 'fill-purple-400'
                                                    : 'fill-dark-tertiary dark:fill-light-tertiary',
                                            seat.is_occupied && '!fill-red-400'
                                            ]" x="6" y="8" width="12" height="18" rx="1" />
                                        </svg>
                                        <svg class="md:hidden w-8 h-10" viewBox="0 0 24 24">
                                            <rect :class="[selectedSeats.has(seat.id)
                                                ? 'fill-green-400'
                                                : seat.type === 'vip'
                                                    ? 'fill-purple-400'
                                                    : 'fill-dark-tertiary dark:fill-light-tertiary',
                                            seat.is_occupied && '!fill-red-400'
                                            ]" x="4" y="16" width="16" height="4" rx="1" />
                                            <rect :class="[selectedSeats.has(seat.id)
                                                ? 'fill-green-400'
                                                : seat.type === 'vip'
                                                    ? 'fill-purple-400'
                                                    : 'fill-dark-tertiary dark:fill-light-tertiary',
                                            seat.is_occupied && '!fill-red-400'
                                            ]" x="6" y="8" width="12" height="8" rx="1" />
                                        </svg>
                                        <span
                                            class="absolute top-1/2 md:top-2/3 left-1/2 -translate-x-1/2 -translate-y-1/2 text-xs font-medium select-none"
                                            :class="[
                                                seat.is_occupied ? 'text-dark-main dark:text-light-main' :
                                                    selectedSeats.has(seat.id) ? 'text-dark-main dark:text-light-main' :
                                                        seat.type === 'vip' ? 'text-dark-main dark:text-light-main' : 'text-light-main dark:text-dark-main'
                                            ]">
                                            {{ seat.number }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Leyenda -->
                    <div class="mt-8 flex flex-wrap gap-4 justify-center">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-red-400 rounded"></div>
                            <span class="text-dark-main dark:text-light-main">Ocupades</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-dark-tertiary dark:bg-light-tertiary rounded"></div>
                            <span class="text-dark-main dark:text-light-main">Disponibles</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-green-400 rounded"></div>
                            <span class="text-dark-main dark:text-light-main">Seleccionades</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-purple-400 rounded"></div>
                            <span class="text-dark-main dark:text-light-main">VIP</span>
                        </div>
                    </div>
                </div>

                <!-- Resumen y acciones -->
                <div class="bg-light-secondary dark:bg-dark-secondary rounded-xl shadow-lg p-6 mt-8">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="text-center md:text-left">
                            <p class="text-lg font-semibold text-dark-main dark:text-light-main">
                                Butaques seleccionades: {{ selectedSeats.size }}
                            </p>
                            <p class="text-dark-main dark:text-light-main mt-1">
                                Total: {{ totalPrice }}€
                            </p>
                        </div>
                        <button @click="goToCheckout" :disabled="selectedSeats.size === 0" class="bg-gradient-to-r from-primary-400 to-tertiary-600 
                        enabled:hover:from-primary-600 enabled:hover:to-tertiary-800 dark:from-purple-600 dark:to-indigo-600 
                        dark:enabled:hover:from-purple-700 dark:enabled:hover:to-indigo-700 text-dark-main dark:text-light-main 
                        cursor-pointer px-8 py-3 rounded-lg enabled:transition-opacity enabled:hover:opacity-90 
                        disabled:opacity-60 dark:disabled:opacity-50 disabled:cursor-not-allowed">
                            Continuar amb la compra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Spinner from '@/components/Spinner.vue';

const loading = ref(true);
const route = useRoute()
const { $api } = useNuxtApp()

// Estado de las butacas
let screening = reactive({})
const selectedSeats = ref(new Set())
const seatRows = ref([])
const { $screeningCommunicationManager } = useNuxtApp()

const fetchScreening = async () => {
    try {
        const data = await $screeningCommunicationManager.getScreeningById(route.params.id);
        if (data) {
            // Calcular el precio de cada butaca
            data.seats = data.seats.map(seat => ({
                ...seat,
                price: calculateSeatPrice(seat, data.screening) // Asignar el precio
            }));

            screening = data;
            seatRows.value = [...new Set(data.seats.map(seat => seat.row))];
        }
    } catch (error) {
        console.error('Error fetching screening:', error);
    }
};

const calculateSeatPrice = (seat, screening) => {
    if (screening.is_special) {
        return seat.type === 'vip' ? 6.00 : 4.00; // Precios para día especial
    }
    return seat.type === 'vip' ? 8.00 : 6.00; // Precios para día normal
};

// Filtrar butacas por fila
const rowSeats = (row) => {
    return screening.seats?.filter(seat => seat.row === row)
}

// Selección de butacas
const toggleSeat = (seat) => {
    if (seat.is_occupied) return

    if (selectedSeats.value.has(seat.id)) {
        selectedSeats.value.delete(seat.id)
    } else {
        if (selectedSeats.value.size < 10) {
            selectedSeats.value.add(seat.id)
        }
    }
}

// Calcular precio total
const totalPrice = computed(() => {
    return [...selectedSeats.value].reduce((total, seatId) => {
        const seat = screening.seats?.find(s => s.id === seatId)
        return total + (seat?.price || 0)
    }, 0)
})

// Navegación a checkout
const goToCheckout = () => {
    navigateTo({
        path: '/checkout',
        query: {
            screeningId: route.params.id,
            seats: [...selectedSeats.value].join(',')
        }
    })
}

// Formatear fecha
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('ca-ES', {
        weekday: 'long', day: 'numeric', month: 'long'
    })
}

onMounted(async () => {
    loading.value = true;
    await fetchScreening()
    loading.value = false;
})
</script>