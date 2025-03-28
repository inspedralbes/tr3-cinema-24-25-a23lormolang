export default defineNuxtRouteMiddleware(async (to) => {
  if (!to.path.startsWith('/admin')) {
    return;
  }

  const authStore = useAuthStore();
  
  // 2. Manejar ruta de login
  if (to.path === '/admin/login') {
    if (authStore.isAuthenticated) {
      return navigateTo('/admin'); 
    }
    return; 
  }

  if (!authStore.isAuthenticated) {
    return navigateTo('/admin/login');
  }

  try {
    await authStore.checkAuthStatus();
  } catch (error) {
    authStore.logout();
    return navigateTo('/admin/login');
  }
});