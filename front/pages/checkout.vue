<template>
    <div class="min-h-screen bg-light-main dark:bg-dark-main py-12">
        <div class="max-w-3xl mx-auto px-4">
            <div class="bg-light-secondary dark:bg-dark-secondary rounded-xl shadow-lg p-6 md:p-8">
                <h1 class="text-3xl font-bold text-dark-main dark:text-light-main mb-6 md:mb-8">
                    Finalitzar Compra
                </h1>

                <!-- Formulario de datos -->
                <form @submit.prevent="submitForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div>
                            <label class="block text-sm font-medium text-dark-main dark:text-light-main mb-2">
                                Nom
                            </label>
                            <input v-model="form.name" required class="w-full px-4 py-3 bg-light-quaternary dark:bg-dark-tertiary 
                                          border border-light-tertiary dark:border-dark-tertiary 
                                          rounded-lg focus:ring-2 focus:ring-primary-600 
                                          text-dark-main dark:text-light-main" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark-main dark:text-light-main mb-2">
                                Correu electrònic
                            </label>
                            <input v-model="form.email" type="email" required class="w-full px-4 py-3 bg-light-quaternary dark:bg-dark-tertiary 
                                          border border-light-tertiary dark:border-dark-tertiary 
                                          rounded-lg focus:ring-2 focus:ring-primary-400  
                                          text-dark-main dark:text-light-main" />
                        </div>
                    </div>

                    <!-- Resumen de compra -->
                    <div class="border-t border-light-tertiary dark:border-dark-tertiary pt-6">
                        <h2 class="text-xl font-semibold text-dark-main dark:text-light-main mb-4">
                            Resum de la teva compra
                        </h2>
                        <div class="space-y-3 text-dark-main dark:text-light-main">
                            <div class="flex justify-between items-center">
                                <span>Pel·lícula:</span>
                                <span class="font-medium">{{ screening?.movie?.title }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Sessió:</span>
                                <span class="font-medium">
                                    {{ formatDate(screening?.screening?.date) }} a les {{ screening?.screening?.time }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Butaques:</span>
                                <span class="font-medium text-primary-500">{{ selectedSeatsCount }}</span>
                            </div>
                            <div class="flex justify-between items-center text-lg pt-4">
                                <span class="font-bold">Total:</span>
                                <span class="font-bold text-primary-500">{{ totalPrice }}€</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" :disabled="processing" class="w-full bg-gradient-to-r from-primary-400 to-tertiary-600 
                               hover:from-primary-500 hover:to-tertiary-700 dark:from-purple-600 dark:to-indigo-600 
                               text-dark-main dark:text-light-main font-semibold py-4 rounded-lg 
                               transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ processing ? 'Processant...' : 'Confirmar Compra' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const route = useRoute()
const router = useRouter()
const { $screeningCommunicationManager } = useNuxtApp()

const form = ref({
    name: '',
    email: '',
})

const { $reservationCommunicationManager } = useNuxtApp()
let screening = reactive({})
const selectedSeats = ref([])
const processing = ref(false)

// Obtener datos de la sesión
const fetchData = async () => {
    try {
        const screeningData = await $screeningCommunicationManager.getScreeningById(route.query.screeningId)

        screeningData.seats = screeningData.seats.map(seat => ({
            ...seat,
            price: calculateSeatPrice(seat, screeningData) // Asignar el precio
        }));

        screening = screeningData
        selectedSeats.value = screeningData.seats.filter(seat =>
            route.query.seats.split(',').includes(String(seat.id))
        )
        console.log(screening)
        console.log(selectedSeats.value)
    } catch (error) {
        console.error('Error fetching data:', error)
    }
}


// Modificar submitForm
const submitForm = async () => {
    processing.value = true
    try {
        await $reservationCommunicationManager.createReservation({
            ...form.value,
            screening_id: route.query.screeningId,
            seats: selectedSeats.value.map(seat => seat.id)
        })
        router.push(`/purchases?email=${encodeURIComponent(form.value.email)}`)
    } catch (error) {
        alert(error.message || 'Error en la compra')
    } finally {
        processing.value = false
    }
}

const totalPrice = computed(() =>
    selectedSeats.value.reduce((sum, seat) => sum + (seat?.price || 0), 0)
)

const calculateSeatPrice = (seat, screeningData) => {
    if (screeningData.is_special) {
        return seat.type === 'vip' ? 6.00 : 4.00; // Precios para día especial
    }
    return seat.type === 'vip' ? 8.00 : 6.00; // Precios para día normal
};

const selectedSeatsCount = computed(() =>
    selectedSeats.value.map(seat => `${seat.row}${seat.number}`).join(', ')
)

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('ca-ES', {
        weekday: 'long', day: 'numeric', month: 'long'
    })
}

onMounted(fetchData)
</script>