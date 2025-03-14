import { createPinia, setActivePinia } from "pinia";

const pinia = createPinia();
setActivePinia(pinia);
const auth = useAuthStore();
const Host = import.meta.env.VITE_API_HOST;

export default defineNuxtPlugin((nuxtApp) => {
  const screeningCommunicationManager = {
    get authStore() {
      return useAuthStore();
    },
    async getNextScreens() {
      try {
        const response = await fetch(`${Host}/nextScreens`, {
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

        return await response.json();
      } catch (error) {
        console.error("Error al obtener sesiones:", error);
        return null;
      }
    },

    async getScreeningById(id) {
      try {
        const response = await fetch(`${Host}/screenings/${id}`, {
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

        return await response.json();
      } catch (error) {
        console.error("Error al obtener la sesión:", error);
        return null;
      }
    },

    async getScreenings(startDate, endDate) {
      try {
        const url = new URL(`${Host}/admin/screenings`);
        url.searchParams.append("start_date", startDate);
        url.searchParams.append("end_date", endDate);

        const response = await fetch(url, {
          headers: {
            Authorization: `Bearer ${this.authStore.token}`,
            "Content-Type": "application/json",
          },
        });

        if (!response.ok)
          throw new Error(`HTTP error! status: ${response.status}`);

        return await response.json();
      } catch (error) {
        console.error("Error fetching screenings:", error);
        throw error;
      }
    },

    async createScreening(screeningData) {
      try {
        const response = await fetch(`${Host}/admin/screenings`, {
          method: "POST",
          headers: {
            Authorization: `Bearer ${this.authStore.token}`,
            "Content-Type": "application/json",
          },
          body: JSON.stringify(screeningData),
        });

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || "Error creating screening");
        }

        return await response.json();
      } catch (error) {
        console.error("Error creating screening:", error);
        throw error;
      }
    },

    async updateScreening(screeningId, updateData) {
      try {
        const response = await fetch(
          `${Host}/admin/screenings/${screeningId}`,
          {
            method: "PUT",
            headers: {
              Authorization: `Bearer ${this.authStore.token}`,
              "Content-Type": "application/json",
            },
            body: JSON.stringify(updateData),
          }
        );

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || "Error updating screening");
        }

        return await response.json();
      } catch (error) {
        console.error("Error updating screening:", error);
        throw error;
      }
    },

    async deleteScreening(screeningId) {
      try {
        const response = await fetch(
          `${Host}/admin/screenings/${screeningId}`,
          {
            method: "DELETE",
            headers: {
              Authorization: `Bearer ${this.authStore.token}`,
            },
          }
        );

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || "Error deleting screening");
        }

        return true;
      } catch (error) {
        console.error("Error deleting screening:", error);
        throw error;
      }
    },

    async movieScreening(startDate, endDate) {
      try {
        const url = new URL(`${Host}/screenings/movies`);
        url.searchParams.append("start_date", startDate);
        url.searchParams.append("end_date", endDate);

        const response = await fetch(url , {
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

        return await response.json();
      } catch (error) {
        console.error("Error al obtener sesiones:", error);
        return null;
      }
    },
    
  };

  nuxtApp.provide(
    "screeningCommunicationManager",
    screeningCommunicationManager
  );
});
