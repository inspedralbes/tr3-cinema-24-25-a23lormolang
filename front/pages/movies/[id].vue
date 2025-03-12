<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 to-gray-800 text-white py-12 px-4">
      <div class="max-w-4xl mx-auto bg-gray-800/50 rounded-xl overflow-hidden shadow-2xl backdrop-blur-sm">
        <div class="flex flex-col md:flex-row">
          <!-- Movie Poster -->
          <div class="md:w-1/3 h-[450px]">
            <img
              src="https://m.media-amazon.com/images/M/MV5BN2FjNWExYzEtY2YzOC00YjNlLTllMTQtNmIwM2Q1YzBhOWM1XkEyXkFqcGc@._V1_SX300.jpg"
              alt="Shutter Island Poster"
              class="w-full h-full object-cover"
            />
          </div>
  
          <!-- Movie Details -->
          <div class="p-8 md:w-2/3">
            <div class="flex justify-between items-start mb-4">
              <h1 class="text-4xl font-bold">Shutter Island</h1>
              <div class="flex items-center gap-2 bg-gray-700/50 px-4 py-2 rounded-lg">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/69/IMDB_Logo_2016.svg" alt="IMDb" class="h-4" />
                <div class="flex items-center text-yellow-400">
                  <i class="bi bi-star-fill text-lg mr-1"></i>
                  <span class="font-semibold">8.2/10</span>
                </div>
              </div>
            </div>
  
            <div class="flex gap-4 mb-6 text-gray-300">
              <div class="flex items-center">
                <i class="bi bi-calendar text-lg mr-1"></i>
                <span>2010</span>
              </div>
              <div class="flex items-center">
                <i class="bi bi-clock text-lg mr-1"></i>
                <span>138 min</span>
              </div>
              <span class="px-2 py-1 bg-gray-700 rounded-full text-sm">R</span>
            </div>
  
            <div class="space-y-4 mb-6">
              <div>
                <div class="text-sm text-gray-400 mb-1">Director</div>
                <div class="font-medium">Martin Scorsese</div>
              </div>
  
              <div>
                <div class="text-sm text-gray-400 mb-1">Cast</div>
                <div class="font-medium">Leonardo DiCaprio, Emily Mortimer, Mark Ruffalo</div>
              </div>
  
              <div>
                <div class="text-sm text-gray-400 mb-1">Plot</div>
                <p class="text-gray-200 leading-relaxed">
                  Two US marshals are sent to a mental institution on an inhospitable island in order to investigate the disappearance of a patient.
                </p>
              </div>
            </div>
  
            <div class="space-y-4 mb-6">
              <div class="flex items-center gap-2">
                <i class="bi bi-trophy text-lg text-yellow-500"></i>
                <span class="text-gray-200">11 wins & 66 nominations</span>
              </div>
  
              <div class="flex items-center gap-2">
                <i class="bi bi-currency-dollar text-lg text-green-500"></i>
                <span class="text-gray-200">Box Office: $128,012,934</span>
              </div>
            </div>
  
            <div class="flex items-center gap-3 mb-8">
              <span class="px-3 py-1 bg-gray-700 rounded-full text-sm">Drama</span>
              <span class="px-3 py-1 bg-gray-700 rounded-full text-sm">Mystery</span>
              <span class="px-3 py-1 bg-gray-700 rounded-full text-sm">Thriller</span>
            </div>
  
            <button class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-4 px-6 rounded-lg font-semibold flex items-center justify-center hover:from-purple-700 hover:to-indigo-700 transition-all">
              <i class="bi bi-ticket-perforated text-xl mr-2"></i>
              Comprar Tickets
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>

  </script>
  