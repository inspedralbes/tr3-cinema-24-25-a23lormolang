const Host = 'http://tr3cine.daw.inspedralbes.cat/public/api';

export default defineNuxtPlugin((nuxtApp) => {
  const reservationCommunicationManager = {
    async createReservation(reservationData) {
      try {
        const response = await fetch(`${Host}/reservations`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(reservationData),
        });

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.error || "Error en la reserva");
        }

        return await response.json();
      } catch (error) {
        console.error("Error al crear reserva:", error);
        throw error;
      }
    },

    async getReservationsByEmail(email) {
      try {
        const response = await fetch(
          `${Host}/reservations?email=${encodeURIComponent(email)}`,
          {
            method: "GET",
            headers: {
              "Content-Type": "application/json",
            },
          }
        );

        if (!response.ok) {
          console.error(
            `Error en la petición: ${response.status} ${response.statusText}`
          );
          return null;
        }

        return await response.json();
      } catch (error) {
        console.error("Error al obtener reservas:", error);
        return null;
      }
    },
  };

  nuxtApp.provide(
    "reservationCommunicationManager",
    reservationCommunicationManager
  );
});
