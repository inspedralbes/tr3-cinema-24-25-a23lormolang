<template>
    <h1>HOLA!</h1>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth';

definePageMeta({
    layout: 'default',
});

const movies = reactive([]);

async function getMovies() {
    const { $moviesCommunicationManager } = useNuxtApp(); // Acceder al communicationManager

    const response = await $moviesCommunicationManager.getMovies();

    if (response) {
        movies = response.movies;
        Object.assign(movies, response.movies);
    } else {
        console.log('Hi ha hagut algun error al obtenir les dades');
    }
}

const auth = useAuth();

onMounted(async () => {
    await getMovies();
})
</script>