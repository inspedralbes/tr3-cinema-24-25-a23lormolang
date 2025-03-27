export default defineNuxtRouteMiddleware(async (to) => {
    const authStore = useAuthStore();
    
    // Excluir ruta de login de la verificación
    if (to.path === '/admin/login') {
      // Si está autenticado y trata de acceder a login, redirigir a dashboard
      if (authStore.isAuthenticated) {
        return navigateTo('/admin');
      }
      return; // Permitir acceso sin autenticación
    }
  
    // Verificar autenticación para todas las demás rutas /admin
    if (!authStore.isAuthenticated) {
      return navigateTo('/admin/login');
    }
  
    // Verificación adicional del token con el backend
    try {
      await authStore.checkAuthStatus();
    } catch (error) {
      authStore.logout();
      return navigateTo('/admin/login');
    }
  });