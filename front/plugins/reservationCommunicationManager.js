const Host = import.meta.env.VITE_API_HOST;

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

    async getAccessLink(email) {
      try {
        const response = await fetch(`${Host}/reservations/access-link`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          body: JSON.stringify({email}),
        });

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

    async getPurchasesByToken(token) {
      try {
        console.log("hola");
        const response = await fetch(
          `${Host}/reservations/purchases/${token}`,
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
