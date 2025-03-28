import { createPinia, setActivePinia } from 'pinia';
const pinia = createPinia();
setActivePinia(pinia);
const Host = import.meta.env.VITE_API_HOST;
const auth = useAuthStore();

export default defineNuxtPlugin((nuxtApp) => {
  const authCommunicationManager = {
    get authStore() {
      return useAuthStore();
    },
    async checkAuth() {
      try {
        const authStore = useAuthStore();
        const response = await fetch(`${Host}/auth/check`, {
          method: "GET",
          headers: {
            "Authorization": `Bearer ${authStore.token}`,
            "Content-Type": "application/json"
          }
        });
    
        if (!response.ok) {
          if (response.status === 401) {
            authStore.logout();
          }
          return { valid: false, error: `Error ${response.status}: ${response.statusText}` };
        }
    
        const data = await response.json();
        return { valid: true, user: data.user };
        
      } catch (error) {
        console.error("Error verifying authentication:", error);
        return { valid: false, error: "Connection error" };
      }
    },
    async login(json) {
      try {
        const response = await fetch(`${Host}/auth/login`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(json),
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

    async logout() {
      try {
        const response = await fetch(Host + "/auth/logout", {
          method: "POST",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            Authorization: this.authStore.token
              ? `Bearer ${this.authStore.token}`
              : "",
          },
        });
        if (response.ok) {
          const json = await response.json();
          return json.message;
        } else {
          console.error(
            `Error en la petición: ${response.status} ${response.statusText}`
          );
          return null;
        }
      } catch (error) {
        console.error("Error al realizar la petición:", error);
        return null;
      }
    },
  };
  nuxtApp.provide("authCommunicationManager", authCommunicationManager);
});
