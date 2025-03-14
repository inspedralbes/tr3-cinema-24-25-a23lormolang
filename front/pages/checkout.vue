<template>
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-3xl mx-auto px-4">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Finalitzar Compra</h1>

                <!-- Formulario de datos -->
                <form @submit.prevent="submitForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input v-model="form.name" required class="w-full px-4 py-2 border rounded-lg" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Correu electrònic</label>
                            <input v-model="form.email" type="email" required
                                class="w-full px-4 py-2 border rounded-lg" />
                        </div>
                    </div>

                    <!-- Resumen de compra -->
                    <div class="border-t pt-6">
                        <h2 class="text-xl font-semibold mb-4">Resum de la teva compra</h2>
                        <div class="space-y-2">
                            <p>Pel·lícula: {{ screening?.movie?.title }}</p>
                            <p>Sessió: {{ formatDate(screening?.screening?.date) }} a les {{ screening?.screening?.time
                            }}</p>
                            <p>Butaques seleccionades: {{ selectedSeatsCount }}</p>
                            <p class="text-lg font-semibold">Total: {{ totalPrice }}€</p>
                        </div>
                    </div>

                    <button type="submit" :disabled="processing"
                        class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 disabled:opacity-50">
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