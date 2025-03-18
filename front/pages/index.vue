<template>

    <MovieCarousel />

    <!-- Dias de la semana -->
    <div class="mt-8 mb-8">
        <ClientWeekDaysSlider v-model="selectedDate" />
    </div>

    <!-- Lista de películas según fecha seleccionada -->
    <div class="flex flex-col gap-6 items-center">
        <div v-for="screening in dailyScreenings" :key="screening.id"
            class="bg-light-quaternary dark:bg-dark-secondary rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex md:max-w-[1100px]">
                    <img :src="screening.movie.poster_url" alt="Imagen Pelicula"
                        class="mr-4 h-[300px] w-[180px] md:h-[450px] md:w-[300px]">
                    <div class="ml-4">
                        <h2 class="text-4xl font-bold text-dark-main mb-4 dark:text-primary-600">{{
                            screening.movie.title }}</h2>
                        <a class="flex items-center mt-8 mb-6">
                            <div class="flex items-center justify-center rounded-lg bg-primary-500 h-[40px] w-[40px]">
                                <i class="bi bi-gift text-2xl"></i>
                            </div>
                            <div class="ml-6">
                                <div class="text-lg text-dark-main  dark:text-light-main">
                                    ¡Ahorras 1€ por entrada comprando tus entradas en nuestra web!
                                </div>
                                <p class="text-lg text-gray-700  dark:text-light-secondary">
                                    Comprar online tiene beneficios.
                                </p>
                            </div>
                        </a>
                        <p class="text-dark-main dark:text-light-main text-lg text-gray-600 mb-4 line-clamp-2">{{
                            screening.movie.description }}</p>
                        <div class="grid grid-cols-2 items-center mb-4">
                            <div>
                                <div class="text-lg font-bold text-dark-main dark:text-light-main"> DURACIÓ</div>
                                <p class="text-gray-600 text-lg mt-2 text-dark-main dark:text-light-main">{{
                                    screening.movie.duration }} min.</p>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-dark-main dark:text-light-main"> GÈNERES</div>
                                <div class="flex items-center gap-3 mt-2">
                                    <span class="px-3 py-1 bg-gray-700 rounded-full text-sm dark:bg-light-main dark:text-dark-tertiary">Drama</span>
                                    <span class="px-3 py-1 bg-gray-700 rounded-full text-sm dark:bg-light-main dark:text-dark-tertiary">Mystery</span>
                                    <span class="px-3 py-1 bg-gray-700 rounded-full text-sm dark:bg-light-main dark:text-dark-tertiary">Thriller</span>
                                </div>
                            </div>
                        </div>

                        <!-- AÑADIR INFORMACIÓN DE LAS SESSIONES -->

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