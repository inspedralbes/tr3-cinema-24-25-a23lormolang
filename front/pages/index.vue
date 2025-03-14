<template>

    <MovieCarousel />

    <!-- Dias de la semana -->
    <div class="mt-4 mb-4">
        <ClientWeekDaysSlider v-model="selectedDate" />
    </div>

    <!-- Lista de películas según fecha seleccionada -->
    <div class="w-full grid gap-6 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3">
        <div v-for="screening in dailyScreenings" :key="screening.id"
            class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex">
                    <img :src="screening.movie.poster_url" alt="Imagen Pelicula" class="mr-4 h-[300px] w-[180px]">
                    <div class="ml-4">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ screening.movie.title }}</h2>
                        <p class="text-gray-600 mb-4">Durada: {{ screening.movie.duration }}min.</p>
                        <div class="flex items-center text-gray-600 mb-4">
                            <i class="bi bi-clock me-2"></i>
                            <span>{{ screening.time }} - {{ formatDate(screening.date) }}</span>
                        </div>
                        <div class="flex items-center text-gray-600 mb-4">
                            <i class="bi bi-ticket-perforated me-2"></i>
                            <span>{{ screening.is_special ? 'Dia de l\'Espectador' : 'Sessió Regular' }}</span>
                        </div>
                        <NuxtLink :to="`/screening/${screening.id}`"
                            class="inline-block text-center bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                            Reservar
                        </NuxtLink>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useCalendarRange } from '@/composables/useCalendarRange';
import ClientWeekDaysSlider from '@/components/ClientWeekDaysSlider.vue';
import MovieCarousel from '@/components/MovieCarousel.vue';

definePageMeta({
    layout: 'default',
});

let screenings = reactive([]);
const { $screeningCommunicationManager } = useNuxtApp();
const { maxDateFormatted, today } = useCalendarRange();
const selectedDate = ref(null);

// Formateador de fecha
const formatDate = (dateString) => {
    const options = { weekday: 'long', day: 'numeric', month: 'long' };
    return new Date(dateString).toLocaleDateString('ca-ES', options);
};

// Obtener las sesiones
const fetchScreenings = async () => {
    try {
        const response = await $screeningCommunicationManager.getScreenings(today, maxDateFormatted);
        if (response) {
            screenings = response; // as a single array of all sessions
        }
    } catch (error) {
        console.error('Error fetching screenings:', error);
    }
};

// Crear una propiedad computada para filtrar por fecha seleccionada
const dailyScreenings = computed(() => {
    if (!selectedDate.value) return [];
    return screenings.filter(screen => {
        const isoDate = new Date(screen.date).toISOString().split('T')[0];
        return isoDate === selectedDate.value;
    });
});

onMounted(async () => {
    await fetchScreenings();
    selectedDate.value = today;
});
</script>