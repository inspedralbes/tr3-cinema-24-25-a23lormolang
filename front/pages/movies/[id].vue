<template>
  <div v-if="loading" class="text-center py-8">
    <Spinner size="xl" color="primary" />
  </div>
  <div v-else>
    <div class=" text-light-main py-12 px-4">
      <div
        class="max-w-6xl mx-auto bg-gray-400/50 dark:bg-gray-800/50 rounded-xl overflow-hidden shadow-2xl backdrop-blur-sm">
        <div class="flex flex-col md:flex-row">
          <!-- Movie Poster -->
          <div class="md:w-1/3 h-[600px]">
            <img :src="movie?.poster_url" :alt="movie?.title" class="w-full h-full object-cover" />
          </div>

          <!-- Movie Details -->
          <div class="p-8 md:w-2/3">
            <div class="flex flex-col md:flex-row justify-between items-start mb-4">
              <h1 class="text-4xl font-bold text-primary-600">{{ movie.title }}</h1>
              <div class="flex mt-4 md:mt-0 items-center gap-2 bg-gray-700/50 px-4 py-2 rounded-lg">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/69/IMDB_Logo_2016.svg" alt="IMDb"
                  class="h-4" />
                <div class="flex items-center text-yellow-400 ml-1">
                  <span class="font-semibold"><span>{{ movie.imdb_rating }}</span>/10</span>
                </div>
              </div>
            </div>

            <div class="flex gap-4 mb-6 text-dark-main dark:text-gray-300">
              <div class="flex items-center">
                <i class="bi bi-calendar text-lg mr-1"></i>
                <span>{{ movie.year }}</span>
              </div>
              <div class="flex items-center">
                <i class="bi bi-clock text-lg mr-1"></i>
                <span>{{ movie.duration }} min</span>
              </div>
            </div>

            <div class="space-y-4 mb-6">
              <div>
                <div class="text-sm md:text-md text-dark-main dark:text-gray-400 mb-1">Director</div>
                <div class="font-medium md:text-lg text-dark-secondary dark:text-light-main">{{ movie.director }}</div>
              </div>

              <div>
                <div class="text-sm md:text-md text-dark-main dark:text-gray-400 mb-1">Actors</div>
                <div class="font-medium md:text-lg text-dark-secondary dark:text-light-main">{{ movie.actors }}</div>
              </div>

              <div>
                <div class="text-sm md:text-md  text-dark-main dark:text-gray-400 mb-1">Plot</div>
                <p class="leading-relaxed md:text-lg  line-clamp-4 text-dark-secondary dark:text-light-main">
                  {{ movie.description }}
                </p>
              </div>
            </div>

            <div class="space-y-6 mb-8">
              <div class="flex items-center gap-2">
                <i class="bi bi-trophy text-lg text-yellow-500"></i>
                <span class="md:text-lg text-dark-secondary dark:text-light-main">{{ movie.awards }}</span>
              </div>

              <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-2">
                  <i class="bi bi-currency-dollar text-lg text-green-500"></i>
                  <span class="text-dark-secondary md:text-lg  dark:text-light-main">{{ movie.box_office }}</span>
                </div>

                <div class="flex mt-6 md:mt-0 items-center gap-3 ">
                  <span v-for="genreItem in (movie.genre || '').split(',')" :key="genreItem"
                    class="px-3 py-1 bg-gray-700 rounded-full text-light-main text-sm md:text-lg">
                    {{ genreItem.trim() || 'Género no disponible' }}
                  </span>
                </div>
              </div>
            </div>


            <!-- <button
            class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-4 px-6 rounded-lg font-semibold flex items-center justify-center hover:from-purple-700 hover:to-indigo-700 transition-all">
            <i class="bi bi-ticket-perforated text-xl mr-2"></i>
            Comprar Tickets
          </button> -->
          </div>
        </div>
        <MovieScheduleSelector :screenings="movie.screenings || []" @select="handleScreeningSelect" class="mt-8" />
      </div>
    </div>
  </div>
</template>

<script setup>
import MovieScheduleSelector from '@/components/MovieScheduleSelector.vue';
import Spinner from '@/components/Spinner.vue';

const loading = ref(true);
const route = useRoute()
let movie = reactive({})
const { $movieCommunicationManager } = useNuxtApp()

const fetchMovies = async () => {
  try {
    const data = await $movieCommunicationManager.getMovieById(route.params.id);
    if (data) {
      Object.assign(movie, data);
    }
  } catch (error) {
    console.error('Error fetching screening:', error);
  }
};

onMounted(async () => {
  loading.value = true;
  await fetchMovies();
  loading.value = false;
});
</script>