<template>
    <div v-if="loading" class="relative text-center py-8">
        <Spinner size="xl" color="primary" />
    </div>
    <div v-else class="relative">
        <!-- CARROUSEL -->
        <div class="relative">
            <div class="absolute bottom-0 left-0 right-0 z-[2] mx-[15%] mb-4 flex list-none justify-center p-0">
                <button v-for="(item, index) in slides" :key="index"
                    class="mx-[3px] box-content h-[3px] w-[30px] flex-initial cursor-pointer border-0 border-y-[10px] border-solid border-transparent bg-black dark:bg-white bg-clip-padding p-0"
                    :class="currentSlide === index ? 'opacity-100' : 'opacity-50'" @click="goToSlide(index)">
                </button>
            </div>
            <!-- Version Sobremesa Carrousel-->
            <div class="relative h-[400px] hidden transition-transform duration-500 ease-in-out md:block">
                <div v-for="(item, index) in slides" :key="index" class="absolute inset-0 flex"
                    :class="{ 'opacity-0': currentSlide !== index }" v-show="currentSlide === index">
                    <div @click="navigateTo(`/movies/${item.id}`)">

                    </div>
                    <div class="flex-1 relative">
                        <img class="w-full h-full object-cover object-center opacity-95 dark:opacity-70" :src="item.source"
                            :alt="item.title" loading="lazy" @click="navigateTo(`/movies/${item.id}`)" />
                    </div>

                    <div
                        class="flex-1 flex items-center justify-center p-12 bg-gray-100 dark:bg-gray-800">
                        <div class="max-w-2xl text-dark-main dark:text-light-main">
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
                <div v-for="(item, index) in slides" :key="index" class="relative float-left w-full h-[550px]"
                    :class="index === currentSlide ? 'block' : 'hidden'">
                    <img class="w-full" :src="item.source" :alt="item.title" />
                </div>
            </div>
            <button class="absolute bottom-0 left-0 top-0 z-[1] w-[5%] text-white opacity-50 hover:opacity-90"
                type="button" @click="prevSlide">
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
    </div>
</template>

<script setup>
import { useRouter, useNuxtApp } from '#imports';
import { useCalendarRange } from '@/composables/useCalendarRange';
import Spinner from '@/components/Spinner.vue';

const { maxDateFormatted, today } = useCalendarRange(); // Use these here
const { $screeningCommunicationManager } = useNuxtApp(); // Retrieve from Nuxt

const loading = ref(true);
let slides = reactive([]);
const currentSlide = ref(0);
const router = useRouter();
let autoSlideInterval; // For auto-advancing slides

function navigateTo(path) {
    router.push(path);
}

onMounted(async () => {
    loading.value = true;
    await fetchMovies();
    autoSlideInterval = setInterval(() => {
        nextSlide();
    }, 7000);
    loading.value=false;
});

onUnmounted(() => {
    clearInterval(autoSlideInterval);
});

async function fetchMovies() {
    try {
        const response = await $screeningCommunicationManager.movieScreening(today, maxDateFormatted);
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
        console.error('Error fetching movies:', error);
    }
}

function goToSlide(index) {
    currentSlide.value = index;
}
function prevSlide() {
    currentSlide.value = (currentSlide.value - 1 + slides.length) % slides.length;
}
function nextSlide() {
    currentSlide.value = (currentSlide.value + 1) % slides.length;
}
</script>
