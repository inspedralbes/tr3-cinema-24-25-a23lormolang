<template>
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <!-- Info Película -->
                <div class="mb-8">
                    <div class="p-6">
                        <div class="flex">
                            <img :src="screening?.movie?.poster_url" alt="Imagen Pelicula"
                                class="mr-4 h-[300px] w-[180px]">
                            <div class="ml-4">
                                <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ screening?.movie?.title }}</h1>
                                <div class="mt-2 text-gray-600">
                                    <p class="text-lg">{{ screening?.movie?.description }}</p>
                                    <div class="mt-4 flex flex-col">
                                        <div class="flex items-center mb-4">
                                            <i class="bi bi-clock me-2"></i>
                                            <span>{{ screening?.screening?.time }} - {{
                                                formatDate(screening?.screening?.date)
                                            }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="bi bi-ticket-perforated me-2"></i>
                                            <span>{{ screening?.screening?.is_special ? 'Oferta Especial' : 'Preu Regular' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Mapa de butaques -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Selecciona les teves butaques</h2>
                    <div class="inline-block bg-gray-100 p-4 rounded-lg">
                        <div v-for="row in seatRows" :key="row" class="flex justify-center mb-2 items-center">
                            <div class="mr-2"> {{ row }} </div>
                            <div v-for="seat in rowSeats(row)" :key="seat.id" @click="toggleSeat(seat)" :class="[
                                'w-8 h-8 mx-1 rounded flex items-center justify-center cursor-pointer transition-colors',
                                seat.is_occupied ? 'bg-red-500 cursor-not-allowed' :
                                    selectedSeats.has(seat.id) ? 'bg-green-500 text-white' :
                                        seat.type === 'vip' ? 'bg-purple-300 hover:bg-purple-400' : 'bg-gray-300 hover:bg-gray-400'
                            ]">
                                {{ seat.number }}
                            </div>
                        </div>
                        <div class="relative mt-4">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t-[3px] border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="bg-gray-100 px-2 font-bold text-gray-500"> PANTALLA </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center space-x-4">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                            <span>Ocupades</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-gray-300 rounded mr-2"></div>
                            <span>Disponibles</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                            <span>Seleccionades</span>
                        </div>
                    </div>
                </div>

                <!-- Resumen y acciones -->
                <div class="border-t pt-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-lg font-semibold">
                                Butaques seleccionades: {{ selectedSeats.size }} (Màxim 10)
                            </p>
                            <p class="text-gray-600 mt-1">
                                Total: {{ totalPrice }}€
                            </p>
                        </div>
                        <button @click="goToCheckout" :disabled="selectedSeats.size === 0"
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            Continuar amb la compra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
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

onMounted(fetchScreening)
</script>