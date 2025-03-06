const Host = 'http://tr3cine.daw.inspedralbes.cat/public/api';

export default defineNuxtPlugin((nuxtApp) => {
  const screeningCommunicationManager = {
    async getScreenings() {
      try {
        const response = await fetch(`${Host}/screenings`, {
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
  };

  nuxtApp.provide(
    "screeningCommunicationManager",
    screeningCommunicationManager
  );
});
