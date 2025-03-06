import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    isAuthenticated: typeof window !== 'undefined' ? localStorage.getItem('isAuthenticated') || false : false,
    token: typeof window !== 'undefined' ? localStorage.getItem('token') || null : null, 
    user: typeof window !== 'undefined' ? JSON.parse(localStorage.getItem('user')) || null : null,
  }),
  getters: {
    userName: (state) => state.user ? state.user.name : '',
    userEmail: (state) => state.user ? state.user.email : '',
    userAuthenticated: (state) => state.isAuthenticated,
  },
  actions: {
    login(userData, userToken,) {
      this.isAuthenticated = true;
      this.user = userData; 
      this.token = userToken; 
      if (typeof window !== 'undefined') {
        localStorage.setItem('isAuthenticated', this.isAuthenticated);
        localStorage.setItem('token', userToken);
        localStorage.setItem('user', JSON.stringify(userData));    
      }
    },
    logout() {
      this.isAuthenticated = false;
      this.user = null;
      this.token = null;
      if (typeof window !== 'undefined') {
        localStorage.removeItem('isAuthenticated');
        localStorage.removeItem('token'); 
        localStorage.removeItem('user'); 
      }
    },
    initialize() {
      if (typeof window !== 'undefined') {
        const isAuthenticated = localStorage.getItem('isAuthenticated');
        const token = localStorage.getItem('token');
        const user = JSON.parse(localStorage.getItem('user'));
        if (token && user && comercio) {
          this.login(user, token);
        }
      }
    },
    setUser(user) {
      this.user = user;
      if (typeof window !== 'undefined') {
        localStorage.setItem('user', JSON.stringify(user));
      }
    }, 
  },
  persist: {
    enabled: true,
    strategies: [
      {
        key: 'clientStorage', 
        storage: typeof window !== 'undefined' ? localStorage : null,
      },
    ],
  },
});

