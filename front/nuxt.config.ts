// https://nuxt.com/docs/api/configuration/nuxt-config
import tailwindcss from "@tailwindcss/vite";
import { fileURLToPath, URL } from "url";

export default defineNuxtConfig({
  alias: {
    "@": fileURLToPath(new URL("./", import.meta.url)),
  },
  compatibilityDate: "2024-11-01",
  devtools: { enabled: true },
  css: ["~/assets/css/main.css", "bootstrap-icons/font/bootstrap-icons.css"],
  modules: ["@pinia/nuxt", "pinia-plugin-persistedstate/nuxt"],
  vite: {
    plugins: [tailwindcss()],
  },

  ssr: false, // Cambiar a false para SPA estático

  nitro: {
    preset: "static", // Generación de sitio estático
    routeRules: {
      "/admin/**": { static: true },
    },
    prerender: {
      // Rutas específicas que quieres pre-renderizar
      routes: ["/purchases/ejemplo-token"],
    },
  },

  // Configuración de generación
  app: {
    baseURL: "/",
  },
});
