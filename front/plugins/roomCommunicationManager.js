import { createPinia, setActivePinia } from "pinia";

const pinia = createPinia();
setActivePinia(pinia);
const auth = useAuthStore();
const Host = import.meta.env.VITE_API_HOST;

export default defineNuxtPlugin((nuxtApp) => {
  const roomCommunicationManager = {
    get authStore() {
      return useAuthStore();
    },
    async getAllRooms() {
      try {
        const response = await fetch(`${Host}/rooms`, {
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

    async getRoomById(room_id) {
      try {
        const response = await fetch(`${Host}/rooms/${room_id}`, {
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
        console.error("Error al obtener la sesión:", error);
        return null;
      }
    },
  };

  nuxtApp.provide(
    "roomCommunicationManager",
    roomCommunicationManager
  );
});
