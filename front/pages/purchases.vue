<template>
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-4xl mx-auto px-4">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Les meves reserves</h1>

                <!-- Buscador por email -->
                <div class="mb-8">
                    <input v-model="email" placeholder="Introdueix el teu correu electrònic"
                        class="w-full px-4 py-2 border rounded-lg" />
                    <button @click="fetchPurchases"
                        class="mt-2 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Buscar
                    </button>
                </div>

                <!-- Listado de reservas -->
                <div v-if="purchases.length" class="space-y-6">
                    <div v-for="purchase in purchases" :key="purchase.id" class="border rounded-lg p-6">
                        <div class="flex">
                            <img :src="purchase.screening?.movie?.poster_url" alt="Imagen Pelicula"
                                class="mr-4 h-[250px] w-[150px]">
                            <div class="ml-4">
                                <h2 class="text-xl font-semibold mb-6">
                                    {{ purchase.screening.movie.title }}
                                </h2>
                                <div class="text-gray-600">
                                    <p class="mb-3">{{ formatDate(purchase.screening.date) }} - {{ purchase.screening.time }}</p>
                                    <p class="mb-3">Butaques: {{ purchase.tickets.map(t => `${t.seat.row}${t.seat.number}`).join(', ') }}</p>
                                    <p class="font-medium">Total: {{ purchase.total }}€</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center text-gray-500">
                    No s'han trobat reserves per aquest correu
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const email = ref('');
const purchases = ref([]);
const { $reservationCommunicationManager } = useNuxtApp();

// Obtener reservas por email
const fetchPurchases = async () => {
    try {
        const data = await $reservationCommunicationManager.getReservationsByEmail(email.value);
        if (data) {
            purchases.value = data.map(res => ({
                ...res,
                total: res.tickets.reduce((sum, t) => sum + Number(t.price), 0) // Convertir a número
            }));
        }
    } catch (error) {
        console.error('Error fetching purchases:', error);
    }
};

// Formatear fecha
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('ca-ES', {
        weekday: 'long', day: 'numeric', month: 'long'
    });
};
</script>