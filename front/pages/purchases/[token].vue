<template>
  <div class="min-h-screen bg-light-main dark:bg-dark-main py-12">
    <div class="max-w-4xl mx-auto px-4">
      <div class="bg-light-secondary dark:bg-dark-secondary rounded-xl shadow-lg p-6 md:p-8">
        <!-- Spinner solo cuando loading es true -->
        <div v-if="loading" class="text-center py-8">
          <Spinner size="xl" color="primary" />
        </div>

        <!-- Mensaje de error específico -->
        <div v-else-if="error" class="text-center py-8">
          <i class="bi bi-x-circle text-4xl mb-4 text-red-500"></i>
          <p class="text-lg text-red-500">{{ error }}</p>
          <p v-if="error.includes('Historial')" class="text-gray-600 dark:text-gray-400 mt-4">
            El enlace ha expirado o ya no está disponible
          </p>
        </div>

        <template v-else>
          <h1 class="text-2xl font-bold text-dark-main dark:text-light-main mb-6">
            🎟️ Les teves reserves
          </h1>

          <div v-if="purchases.length" class="space-y-6">
            <div v-for="purchase in purchases" :key="purchase.id"
              class="bg-light-secondary dark:bg-dark-secondary rounded-lg p-6 border border-gray-200 dark:border-gray-700">
              <div class="flex justify-between items-start mb-4">
                <div>
                  <h2 class="text-xl font-bold text-primary-500">
                    {{ purchase.screening.movie.title }}
                  </h2>
                  <p class="text-gray-600 dark:text-gray-400 mt-2">
                    {{ formatDate(purchase.screening.date) }}
                    <span class="mx-2">•</span>
                    {{ purchase.screening.time }}
                  </p>
                </div>
              </div>

              <div class="space-y-3">
                <div v-for="ticket in purchase.tickets" :key="ticket.id"
                  class="flex justify-between items-center p-3 bg-light-main dark:bg-dark-main rounded dark:text-light-main">
                  <div class="flex items-center gap-2">
                    <span class="font-medium w-[80px]">
                      Seient {{ ticket.seat.row }}{{ ticket.seat.number }}
                    </span>
                    <span :class="[
                      'ml-1 text-xs px-2 py-1 rounded-full',
                      ticket.seat.type === 'vip'
                        ? 'bg-amber-100 text-amber-800'
                        : 'bg-blue-100 text-blue-800'
                    ]">
                      {{ ticket.seat.type.toUpperCase() }}
                    </span>
                  </div>
                  <span class="font-mono">
                    {{ parseFloat(ticket.price).toFixed(2) }}€
                  </span>
                </div>
              </div>

              <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                <div class="flex justify-between items-center">
                  <span class="font-bold">Total:</span>
                  <span class="font-bold text-lg">
                    {{ calculateTotal(purchase.tickets) }}€
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-8 text-gray-500">
            No s'han trobat reserves
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import Spinner from '@/components/Spinner.vue';

const route = useRoute();
const purchases = ref([]);
const loading = ref(true);
const error = ref('');
const { $reservationCommunicationManager } = useNuxtApp();

const formatDate = (dateString) => {
  const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
  return new Date(dateString).toLocaleDateString('ca-ES', options);
};

const calculateTotal = (tickets) => {
  return tickets.reduce((total, ticket) => total + parseFloat(ticket.price), 0).toFixed(2);
};

onMounted(async () => {
  try {
    const token = route.params.token;
    if (!token) {
      throw new Error('Falta el token de acceso');
    }
    
    const data = await $reservationCommunicationManager.getPurchasesByToken(token);
    
    if (!data) {
      throw new Error('Historial ya no disponible');
    }
    
    purchases.value = Array.isArray(data) ? data : [data];
    
  } catch (err) {
    error.value = err.message || 'Historial ya no disponible';
    loading.value = false; // Detener spinner inmediatamente en caso de error
    return; // Salir temprano
  }
  
  loading.value = false; // Detener spinner cuando todo sale bien
});
</script>