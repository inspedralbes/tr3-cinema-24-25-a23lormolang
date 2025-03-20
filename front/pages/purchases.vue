<template>
    <div class="min-h-screen bg-light-main dark:bg-dark-main py-12">
        <div class="max-w-4xl mx-auto px-4">
            <div class="bg-light-secondary dark:bg-dark-secondary rounded-xl shadow-lg p-6 md:p-8">
                <h1 class="text-3xl font-bold text-dark-main dark:text-light-main mb-6 md:mb-8">
                    Les meves reserves
                </h1>

                <!-- Buscador por email -->
                <div class="mb-8 space-y-4">
                    <div class="flex flex-col md:flex-row gap-4">
                        <input v-model="email" placeholder="Introdueix el teu correu electrònic" class="flex-1 px-4 py-3 bg-light-quaternary dark:bg-dark-tertiary 
                                   border border-light-tertiary dark:border-dark-tertiary
                                   rounded-lg focus:ring-2 focus:ring-primary-400
                                   text-dark-main dark:text-light-main placeholder-gray-500" />
                        <button @click="fetchPurchases" class="bg-gradient-to-r from-primary-400 to-tertiary-600 
                        enabled:hover:from-primary-600 enabled:hover:to-tertiary-800 dark:from-purple-600 dark:to-indigo-600 
                        dark:enabled:hover:from-purple-700 dark:enabled:hover:to-indigo-700 text-dark-main dark:text-light-main 
                        cursor-pointer px-8 py-3 rounded-lg enabled:transition-opacity enabled:hover:opacity-90 
                        ">
                            Cercar
                        </button>
                    </div>
                </div>

                <!-- Listado de reservas -->
                <div v-if="purchases.length" class="space-y-6">
                    <div v-for="purchase in purchases" :key="purchase.id" class="border border-light-tertiary dark:border-dark-tertiary rounded-xl p-6 
                                bg-light-main dark:bg-dark-main">
                        <div class="flex flex-col md:flex-row gap-6">
                            <img :src="purchase.screening?.movie?.poster_url" alt="Poster pel·lícula"
                                class="w-full md:w-48 h-64 object-cover rounded-xl">

                            <div class="flex-1">
                                <h2 class="text-xl font-bold text-dark-main dark:text-light-main mb-6">
                                    {{ purchase.screening.movie.title }}
                                </h2>

                                <div class="space-y-6 !text-lg text-dark-main dark:text-light-main">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-calendar-event text-2xl text-primary-500"></i>
                                        <p>{{ formatDate(purchase.screening.date) }} - {{ purchase.screening.time }}</p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-ticket-perforated text-2xl text-primary-500"></i>
                                        <p>Butaques:
                                            <span class="font-medium">
                                                {{purchase.tickets.map(t => `${t.seat.row}${t.seat.number}`).join(', ')
                                                }}
                                            </span>
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-cash-coin text-2xl text-primary-500"></i>
                                        <p class="font-bold text-lg">
                                            Total: <span class="text-primary-500">{{ purchase.total }}€</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-8 text-dark-main dark:text-light-main">
                    <i class="bi bi-search text-4xl mb-4 text-gray-500"></i>
                    <p class="text-lg">No s'han trobat reserves per aquest correu</p>
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