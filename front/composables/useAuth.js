import { useAuthStore } from "@/stores/authStore";
export function useAuth() {
  let user = reactive({
    email: null,
    password: null,
  });

  const errorMessage = ref(null); // Mensaje de error
  const authStore = useAuthStore();
  const { $authCommunicationManager } = useNuxtApp(); // Acceder al communicationManager

  // Función para hacer login
  const login = async () => {
    try {
      // Verificar si los campos están vacíos
      if (!user.email || !user.password) {
        console.error("És necessari completar tots els camps");
        errorMessage.value = "És necessari completar tots els camps";
        return;
      }

      // Verificar que la contraseña tenga al menos 8 caracteres
      if (user.password.length < 8) {
        console.error("La contrasenya es incorrecta");
        errorMessage.value = "La contrasenya es incorrecta";
        return;
      }
      // Llamar al plugin communicationManager para registrar
      const response = await $authCommunicationManager.login(user);
      if (response) {
        authStore.login(response.user, response.token);
        navigateTo("/admin");
      }
      errorMessage.value = "Error en el login";
      return;
    } catch (error) {
      console.error("Error en login:", error);
      throw error;
    }
  };

  // Función para hacer logout
  const logout = async () => {
    try {
      const message = await $authCommunicationManager.logout();
      console.log("logout", message);
      if (!message) {
        console.error("Error en el logout");
        return;
      }
      authStore.logout();
      user = null;
      navigateTo("/");
    } catch (error) {
      console.error("Error en logout:", error);
    }
  };

  return { user, errorMessage, login, logout };
}
