import { createPinia, setActivePinia } from "pinia";

const pinia = createPinia();
setActivePinia(pinia);
const auth = useAuthStore();
const Host = import.meta.env.VITE_API_HOST;

export default defineNuxtPlugin((nuxtApp) => {
  const statCommunicationManager = {
    get authStore() {
      return useAuthStore();
    },
    async getStatsSales(period) {
      try {
        const response = await fetch(`${Host}/stats/sales?period=${period}`, {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${this.authStore.token}`,
          },
        });

        if (!response.ok) {
          console.error(
            `Error en la petición: ${response.status} ${response.statusText}`
          );
          return null;
        }

        return await response.json();
      } catch (error) {
        console.error("Error al obtener sesiones:", error);
        return null;
      }
    },

  };

  nuxtApp.provide("statCommunicationManager", statCommunicationManager);
});
