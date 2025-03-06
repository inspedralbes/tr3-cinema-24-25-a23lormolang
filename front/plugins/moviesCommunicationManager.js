import { createPinia, setActivePinia } from 'pinia';
const pinia = createPinia();
setActivePinia(pinia);
const Host = import.meta.env.VITE_API_HOST;
const auth = useAuthStore();

export default defineNuxtPlugin((nuxtApp) => {
  const moviesCommunicationManager = {
    get authStore() {
      return useAuthStore();
    },
    async getMovies(json) {
      try {
        const response = await fetch(`${Host}/movies/upcoming`, {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
          },
        });

        if (!response.ok) {
          console.error(
            `Error en la petición: ${response.status} ${response.statusText}`
          );
          return null;
        }

        const jsonResponse = await response.json();
        return jsonResponse;
      } catch (error) {
        console.error("Error al realizar la petición:", error);
        return null;
      }
    },

  };
  nuxtApp.provide("moviesCommunicationManager", moviesCommunicationManager);
});
