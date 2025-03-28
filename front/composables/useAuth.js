import { useAuthStore } from "@/stores/authStore";
import { storeToRefs } from "pinia";

export function useAuth() {
  const user = reactive({
    email: null,
    password: null,
  });

  const errorMessage = ref(null);
  const authStore = useAuthStore();
  const { isAuthenticated } = storeToRefs(authStore);
  const { $authCommunicationManager } = useNuxtApp();

  const login = async () => {
    try {
      errorMessage.value = null; // Resetear mensajes de error

      // Validaciones
      if (!user.email || !user.password) {
        errorMessage.value = "És necessari completar tots els camps";
        return;
      }

      if (user.password.length < 8) {
        errorMessage.value =
          "La contrasenya ha de tenir com a mínim 8 caràcters";
        return;
      }

      // Llamada al API
      const response = await $authCommunicationManager.login(user);

      if (!response || !response.user || !response.token) {
        errorMessage.value = "Credencials invàlides";
        return;
      }

      // Actualizar store
      authStore.login(
        {
          id: response.user.id,
          name: response.user.name,
          email: response.user.email,
        },
        response.token
      );

      // Redirección
      await navigateTo("/admin");
    } catch (error) {
      console.error("Error en login:", error);
      errorMessage.value = error.response?.data?.message || "Error de connexió";
    }
  };

  const logout = async () => {
    try {
      await $authCommunicationManager.logout();

      // Limpiar store
      authStore.logout();

      // Resetear formulario
      user.email = null;
      user.password = null;

      // Redirección
      await navigateTo("/");
    } catch (error) {
      console.error("Error en logout:", error);
      errorMessage.value = "No s'ha pogut tancar la sessió";
    }
  };

  return {
    user,
    errorMessage,
    isAuthenticated,
    login,
    logout,
  };
}
