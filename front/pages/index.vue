<template>
    <div class="flex flex-col items-center">
        <div class="flex flex-col mt-4 sm:flex-row gap-2 mb-6 px-4">
            <button @click="showSection('today')" :class="[
                'w-full md:w-auto px-4 py-2 text-md font-medium rounded-lg transition-colors duration-200',
                currentSection === 'personal' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-blue-600 hover:text-white'
            ]">
                Avui
            </button>
            <button @click="showSection('tomorrow')" :class="[
                'w-full md:w-auto px-4 py-2 text-md font-medium rounded-lg transition-colors duration-200',
                currentSection === 'password' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-blue-600 hover:text-white'
            ]">
                Dema
            </button>
        </div>
        
        <div class="relative">
            <div v-if="currentSection === 'today'" class="mx-3">
                <div>HOla</div>
            </div>
            <div v-else>
                <div>Adeu</div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth';

definePageMeta({
    layout: 'default',
});

const movies = reactive([]);
const auth = useAuth();
const currentSection = ref('today');

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


function showSection(section) {
    currentSection.value = section;
}

onMounted(async () => {
    await getMovies();
})
</script>