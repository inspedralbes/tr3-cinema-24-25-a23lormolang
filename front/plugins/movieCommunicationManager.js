import { createPinia, setActivePinia } from "pinia";

const pinia = createPinia();
setActivePinia(pinia);
const auth = useAuthStore();
const Host = import.meta.env.VITE_API_HOST;

export default defineNuxtPlugin((nuxtApp) => {
  const movieCommunicationManager = {
    get authStore() {
      return useAuthStore();
    },
    async searchMovies(query) {
      try {
        const response = await fetch(
          `${Host}/omdb/search?query=${encodeURIComponent(query)}`,
          {
            headers: {
              Authorization: `Bearer ${this.authStore.token}`,
            },
          }
        );

        if (!response.ok) throw new Error("Error en la búsqueda");
        return await response.json();
      } catch (error) {
        console.error("OMDB search error:", error);
        throw error;
      }
    },
    async createMovie(imdbID) {
      try {
        const response = await fetch(`${Host}/movies`, {
          method: "POST",
          headers: {
            Authorization: `Bearer ${this.authStore.token}`,
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ imdb_id: imdbID }),
        });

        if (!response.ok) throw new Error("Error creating movie");
        return await response.json();
      } catch (error) {
        console.error("Movie creation error:", error);
        throw error;
      }
    },
    async getMovieById(id) {
      try {
        const response = await fetch(`${Host}/movies/${id}`, {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
          },
        });

        if (!response.ok) throw new Error("Error creating movie");
        return await response.json();
      } catch (error) {
        console.error("Movie creation error:", error);
        throw error;
      }
    },
    async getMovies() {
      try {
        const response = await fetch(`${Host}/movies`, {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
          },
        });

        if (!response.ok) throw new Error("Error geting movie");
        return await response.json();
      } catch (error) {
        console.error("Movie creation error:", error);
        throw error;
      }
    },
  };

  nuxtApp.provide(
    "movieCommunicationManager",
    movieCommunicationManager
  );
});
