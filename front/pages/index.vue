<template>

    <div v-if="loading" class="text-center py-8">
        <Spinner size="xl" color="primary" />
    </div>
    <div v-else>

        <MovieCarousel />

        <!-- Dias de la semana -->
        <div class="mt-8 mb-8">
            <ClientWeekDaysSlider v-model="selectedDate" />
        </div>

        <!-- Lista de películas según fecha seleccionada -->
        <div class="flex flex-col gap-6 items-center">
            <div v-for="screening in dailyScreenings" :key="screening.id"
                class="bg-light-quaternary dark:bg-dark-secondary rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div class="p-2 md:p-6">
                    <!-- Peliculas Desktop -->
                    <div class="hidden md:block">
                        <div class="flex md:max-w-[1100px]">
                            <img :src="screening.movie.poster_url" @click="navigateTo(`/movies/${screening.movie.id}`)"
                                alt="Imagen Pelicula" class="cursor-pointer rounded-md mr-4 md:h-[450px] md:w-[300px]">
                            <div class="ml-4 xl:w-[690px]">
                                <h2 @click="navigateTo(`/movies/${screening.movie.id}`)"
                                    class="cursor-pointer text-4xl font-bold text-dark-main mb-4 text-primary-600 hover:text-primary-700">
                                    {{
                                        screening.movie.title }}</h2>
                                <a class="flex items-center mt-8 mb-6">
                                    <div
                                        class="flex items-center justify-center rounded-lg bg-primary-500 h-[40px] w-[40px]">
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
                                <p class="text-dark-main dark:text-light-main text-lg text-gray-600 mb-4 line-clamp-2">
                                    {{
                                        screening.movie.description }}</p>
                                <div class="mt-2 grid grid-cols-2 items-center mb-4">
                                    <div>
                                        <div class="text-lg font-bold text-dark-main dark:text-light-main"> DURACIÓ
                                        </div>
                                        <p class="text-gray-600 text-lg mt-3 text-dark-main dark:text-light-main">{{
                                            screening.movie.duration }} min.</p>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-dark-main dark:text-light-main"> GÈNERES
                                        </div>
                                        <div class="flex items-center gap-3 mt-3">
                                            <span v-for="genreItem in screening.movie.genre.split(',')" :key="genreItem"
                                                class="px-3 py-1 bg-gray-700 rounded-full text-light-main text-sm dark:bg-light-main dark:text-dark-tertiary">
                                                {{ genreItem.trim() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between mt-10">
                                    <div class="flex items-center mb-4">

                                        <div class="text-4xl font-bold text-dark-main dark:text-light-main"> {{
                                            screening.time }}</div>
                                        <p class="ml-4 text-gray-600 text-3xl text-dark-main dark:text-light-main">
                                            <span class="mr-4">-</span>{{
                                                screening.room.name }}</p>

                                    </div>
                                    <button @click="navigateTo(`/movies/${screening.movie.id}`)" class="font-bold bg-gradient-to-r from-primary-400 to-tertiary-600 
                                    enabled:hover:from-primary-600 enabled:hover:to-tertiary-800 dark:from-purple-600 dark:to-indigo-600 
                                    dark:enabled:hover:from-purple-700 dark:enabled:hover:to-indigo-700 text-dark-main dark:text-light-main 
                                    cursor-pointer px-8 py-3 h-[60px] rounded-lg enabled:transition-opacity enabled:hover:opacity-90 
                                    ">
                                        Més Informació
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Peliculas mobile -->
                    <div class="flex flex-col w-[340px] block md:hidden">
                        <div class="flex mt-2">
                            <img :src="screening.movie.poster_url" @click="navigateTo(`/movies/${screening.movie.id}`)"
                                alt="Imagen Pelicula"
                                class="cursor-pointer ml-2 rounded-md h-[165px] w-[110px] shrink-0">

                            <div class="flex flex-col ml-4">
                                <h2 @click="navigateTo(`/movies/${screening.movie.id}`)"
                                    class="text-2xl font-bold text-primary-600">{{ screening.movie.title }}</h2>
                                <div class="mt-3">
                                    <p class="text-md font-bold text-dark-main dark:text-light-main">Duració: <span
                                            class="font-normal">{{ screening.movie.duration }} min.</span></p>
                                    <div class="flex flex-wrap gap-2 mt-4">
                                        <span v-for="genreItem in screening.movie.genre.split(',')" :key="genreItem"
                                            class="px-3 !text-[15px] py-1 bg-gray-700 rounded-full text-light-main">
                                            {{ genreItem.trim() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a class="flex items-center ml-2 mt-4 mb-6">
                            <div class="flex items-center justify-center rounded-lg bg-primary-500 h-[50px] w-[70px]">
                                <i class="bi bi-gift text-2xl"></i>
                            </div>
                            <div class="ml-4 text-md">
                                <div class="text-dark-main  dark:text-light-main">
                                    ¡Ahorras 1€ por entrada comprando tus entradas en nuestra web!
                                </div>
                                <p class="text-gray-700  dark:text-light-secondary">
                                    Comprar online tiene beneficios.
                                </p>
                            </div>
                        </a>
                        <p class="text-dark-main ml-2 dark:text-light-main text-lg text-gray-600 mb-4 line-clamp-4">{{
                            screening.movie.description }}</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center mb-4 ml-2">

                                <div class="text-xl font-bold text-dark-main dark:text-light-main"> {{
                                    screening.time }}</div>
                                <p class="ml-4 text-gray-600 text-xl text-dark-main dark:text-light-main"> <span
                                        class="mr-4">-</span>{{
                                            screening.room.name }}</p>

                            </div>
                        </div>
                        <div class="flex justify-center mb-2">

                            <button @click="navigateTo(`/movies/${screening.movie.id}`)" class="font-bold bg-gradient-to-r from-primary-400 to-tertiary-600 
                                    enabled:hover:from-primary-600 enabled:hover:to-tertiary-800 dark:from-purple-600 dark:to-indigo-600 
                                    dark:enabled:hover:from-purple-700 dark:enabled:hover:to-indigo-700 text-dark-main dark:text-light-main 
                                    cursor-pointer px-8 py-3 h-[60px] rounded-lg enabled:transition-opacity enabled:hover:opacity-90 w-[200px]
                                    ">
                                Més Informació
                            </button>
                        </div>
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
import Spinner from '@/components/Spinner.vue';

definePageMeta({
    layout: 'default',
});

const loading = ref(true);
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
    loading.value = true;
    await fetchScreenings();
    selectedDate.value = today;
    loading.value=false;
});
</script>

<style scoped>
.v-showtime-button__detail-wrapper {
    grid-area: detail;
    width: 100%;
    min-width: 0;
    box-sizing: border-box;
    padding: 14px 8px 8px;
    flex-flow: column nowrap;
    align-content: center;
}
</style>