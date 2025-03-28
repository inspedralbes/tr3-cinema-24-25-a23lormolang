<template>
    <div class="min-h-screen bg-light-main dark:bg-dark-main p-6 md:p-8">
        <div v-if="loading" class="text-center py-12">
            <Spinner size="xl" color="primary" />
        </div>

        <div v-else-if="error" class="text-center text-red-500 py-8">
            <i class="bi bi-x-circle text-4xl mb-4"></i>
            <p class="text-lg">{{ error }}</p>
        </div>

        <div v-else class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-dark-main dark:text-light-main mb-8">Catàleg de Pel·lícules</h1>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
                <div v-for="movie in movies" :key="movie.id" @click="navigateTo(`/movies/${movie.id}`)"
                    class="group relative cursor-pointer transform transition-all duration-300 hover:scale-105 hover:z-10">
                    <!-- Poster de la película -->
                    <img :src="movie.poster_url" :alt="movie.title"
                        class="w-full h-full object-cover rounded-lg shadow-xl transition-all duration-300"
                        loading="lazy" format="webp" quality="80" densities="x1 x2" :style="{ aspectRatio: '2/3' }" />

                    <!-- Overlay hover -->
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-dark-main/90 to-transparent rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-4 flex flex-col justify-end">
                        <h2 class="text-light-main font-semibold text-lg mb-2 truncate">{{ movie?.title }}</h2>
                        <div class="flex items-center space-x-2">
                            <i class="bi bi-star-fill text-yellow-400"></i>
                            <span class="text-light-main font-medium">{{ movie?.imdb_rating }}/10</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Spinner from '@/components/Spinner.vue';

const movies = ref([]);
const loading = ref(true);
const error = ref(null);
const { $movieCommunicationManager } = useNuxtApp();


onMounted(async () => {
    try {
        const response = await $movieCommunicationManager.getMovies();
        movies.value = response;
    } catch (err) {
        error.value = 'Error carregant les pel·lícules';
        console.error(err);
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
/* Transiciones suaves para el hover */
.group:hover .overlay-info {
    opacity: 1;
    transform: translateY(0);
}

/* Efecto de elevación al hover */
.shadow-xl {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Optimización para dark mode */
.dark .shadow-xl {
    box-shadow: 0 20px 25px -5px rgba(255, 255, 255, 0.05), 0 10px 10px -5px rgba(255, 255, 255, 0.02);
}
</style>