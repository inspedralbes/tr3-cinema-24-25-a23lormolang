<template>
    <div class="relative">
        <div class="absolute bottom-0 left-0 right-0 z-[2] mx-[15%] mb-4 flex list-none justify-center p-0">
            <button v-for="(item, index) in slides" :key="index"
                class="mx-[3px] box-content h-[3px] w-[30px] flex-initial cursor-pointer border-0 border-y-[10px] border-solid border-transparent bg-white bg-clip-padding p-0"
                :class="currentSlide === index ? 'opacity-100' : 'opacity-50'" @click="goToSlide(index)">
            </button>
        </div>
        <!-- Version Sobremesa Carrousel-->
        <div class="relative h-[400px] hidden transition-transform duration-500 ease-in-out md:block">
            <div v-for="(item, index) in slides" :key="index" class="absolute inset-0 flex"
                :class="{ 'opacity-0': currentSlide !== index }" v-show="currentSlide === index">
                <div @click="navigateTo(`/movies/${item.id}`)">

                </div>
                <div class="flex-1 relative bg-gray-900">
                    <img class="w-full h-full object-cover object-center opacity-70" :src="item.source"
                        :alt="item.title" loading="lazy" @click="navigateTo(`/movies/${item.id}`)" />
                </div>

                <div class="flex-1 flex items-center justify-center p-12 bg-gradient-to-r from-gray-900 to-gray-800">
                    <div class="max-w-2xl text-white">
                        <h2 class="text-4xl font-bold mb-6">{{ item.title }}</h2>
                        <p class="text-xl mb-8 line-clamp-4 h-[118px]">{{ item.text }}</p>
                        <div class="flex items-center space-x-4">
                            <span class="text-2xl font-bold text-blue-400">
                                IMDb {{ item.imdb_rating }}
                            </span>
                            <span class="text-lg">|</span>
                            <span class="text-lg">{{ item.duration }} min</span>
                            <span class="text-lg">|</span>
                            <span class="text-lg">{{ item.genre }}</span>
                        </div>
                        <!-- Aqui a futuro talvez se  -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Version Mobil Carrousel -->
        <div class="relative w-full overflow-hidden block md:hidden">
            <div v-for="(item, index) in slides" :key="index" class="relative float-left w-full h-[400px]"
                :class="index === currentSlide ? 'block' : 'hidden'">
                <img class="w-full" :src="item.source" :alt="item.title" />
            </div>
        </div>
        <button class="absolute bottom-0 left-0 top-0 z-[1] w-[5%] text-white opacity-50 hover:opacity-90" type="button"
            @click="prevSlide">
            <span class="inline-block h-8 w-8">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </span>
            <span
                class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">Previous</span>
        </button>
        <button class="absolute bottom-0 right-0 top-0 z-[1] w-[5%] text-white opacity-50 hover:opacity-90"
            type="button" @click="nextSlide">
            <span class="inline-block h-8 w-8">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </span>
            <span
                class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 !p-0 ![clip:rect(0,0,0,0)]">Next</span>
        </button>
    </div>

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
import { useCalendarRange } from '@/composables/useCalendarRange';

definePageMeta({
    layout: 'default',
});

const screenings = reactive({
    today: [],
    tomorrow: []
});
const currentSection = ref('today');
const { $screeningCommunicationManager } = useNuxtApp();
const { startDate, endDate } = useCalendarRange();

// Formateador de fecha
const formatDate = (dateString) => {
    const options = { weekday: 'long', day: 'numeric', month: 'long' };
    return new Date(dateString).toLocaleDateString('ca-ES', options);
};

// Obtener las sesiones
const fetchScreenings = async () => {
    try {
        const response = await $screeningCommunicationManager.getNextScreens();
        if (response) {
            screenings.today = response.today;
            screenings.tomorrow = response.tomorrow;
        }
    } catch (error) {
        console.error('Error fetching screenings:', error);
    }
};

const fetchMovies = async () => {
    try {
        const start = startDate.value.toISOString().split('T')[0];
        const end = endDate.value.toISOString().split('T')[0];
        const response = await $screeningCommunicationManager.movieScreening(start, end);
        if (response) {
            slides.length = 0;
            response.forEach((movie) => {
                slides.push({
                    id: movie.id,
                    source: movie.poster_url,
                    title: movie.title,
                    text: movie.description,
                    imdb_rating: movie.imdb_rating,
                    duration: movie.duration,
                    genre: movie.genre,
                });
            });
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

const currentSlide = ref(0);
let slides = reactive([]);

function goToSlide(index) {
    currentSlide.value = index;
}

function prevSlide() {
    currentSlide.value = (currentSlide.value - 1 + slides.length) % slides.length;
}

function nextSlide() {
    currentSlide.value = (currentSlide.value + 1) % slides.length;
}

onMounted(async () => {
    await fetchMovies();
    await fetchScreenings();
});
</script>