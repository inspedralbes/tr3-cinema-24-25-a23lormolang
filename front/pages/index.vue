<template>
    <div class="flex flex-col items-center">
        <div class="flex flex-col mt-4 sm:flex-row gap-2 mb-6 px-4">
            <button @click="showSection('today')" :class="[
                'w-full md:w-auto px-4 py-2 text-md font-medium rounded-lg transition-colors duration-200',
                currentSection === 'today' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-blue-600 hover:text-white'
            ]">
                Avui
            </button>
            <button @click="showSection('tomorrow')" :class="[
                'w-full md:w-auto px-4 py-2 text-md font-medium rounded-lg transition-colors duration-200',
                currentSection === 'tomorrow' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-blue-600 hover:text-white'
            ]">
                Demà
            </button>
        </div>

        <div class="w-full grid gap-6 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3">
            <div v-for="screening in currentScreenings" :key="screening.id"
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
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';

definePageMeta({
    layout: 'default',
});

const screenings = reactive({
    today: [],
    tomorrow: []
});
const currentSection = ref('today');
const { $screeningCommunicationManager } = useNuxtApp();

// Formateador de fecha
const formatDate = (dateString) => {
    const options = { weekday: 'long', day: 'numeric', month: 'long' };
    return new Date(dateString).toLocaleDateString('ca-ES', options);
};

// Obtener las sesiones
const fetchScreenings = async () => {
    try {
        const response = await $screeningCommunicationManager.getScreenings();
        if (response) {
            screenings.today = response.today;
            screenings.tomorrow = response.tomorrow;
        }
    } catch (error) {
        console.error('Error fetching screenings:', error);
    }
};

// Pantalla actual
const currentScreenings = computed(() => {
    return currentSection.value === 'today'
        ? screenings.today
        : screenings.tomorrow;
});

function showSection(section) {
    currentSection.value = section;
}

onMounted(async () => {
    await fetchScreenings();
});
</script>