import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    isAuthenticated:
      typeof window !== "undefined"
        ? localStorage.getItem("isAuthenticated") === "true"
        : false,
    token: typeof window !== "undefined" ? localStorage.getItem("token") : null,
    user:
      typeof window !== "undefined"
        ? JSON.parse(localStorage.getItem("user"))
        : null,
  }),
  getters: {
    userName: (state) => (state.user ? state.user.name : ""),
    userEmail: (state) => (state.user ? state.user.email : ""),
    userAuthenticated: (state) => state.isAuthenticated,
  },
  actions: {
    async checkAuthStatus() {
      try {
        const { $authCommunicationManager } = useNuxtApp();
        const response = await $authCommunicationManager.checkAuth();
        return response.valid;
      } catch (error) {
        throw new Error('Error verifying authentication');
      }
    },
    login(userData, userToken) {
      this.isAuthenticated = true;
      this.user = userData;
      this.token = userToken;
      if (typeof window !== "undefined") {
        localStorage.setItem("isAuthenticated", this.isAuthenticated);
        localStorage.setItem("token", userToken);
        localStorage.setItem("user", JSON.stringify(userData));
      }
    },
    logout() {
      this.isAuthenticated = false;
      this.user = null;
      this.token = null;
      if (typeof window !== "undefined") {
        localStorage.removeItem("isAuthenticated");
        localStorage.removeItem("token");
        localStorage.removeItem("user");
      }
    },
    initialize() {
      if (typeof window !== "undefined") {
        const storedAuth = localStorage.getItem("isAuthenticated");
        const storedToken = localStorage.getItem("token");
        const storedUser = localStorage.getItem("user");

        if (storedAuth && storedToken && storedUser) {
          this.isAuthenticated = storedAuth === "true";
          this.token = storedToken;
          this.user = JSON.parse(storedUser);
        }
      }
    },

    setUser(user) {
      this.user = user;
      if (typeof window !== "undefined") {
        localStorage.setItem("user", JSON.stringify(user));
      }
    },
  },
  persist: {
    enabled: true,
    strategies: [
      {
        key: "clientStorage",
        storage: typeof window !== "undefined" ? localStorage : null,
      },
    ],
  },
});
