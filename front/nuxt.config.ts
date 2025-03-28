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
  nitro: {
    routeRules: {
      "/admin/**": { swr: false }, 
    },
  },
  ssr: true, // o false si quieres SPA puro
  nitro: {
    preset: "static", // Genera archivos estáticos
  },
});
