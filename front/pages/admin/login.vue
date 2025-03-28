<template>
    <div class="flex min-h-screen bg-light-main dark:bg-dark-main">
        <div class="w-1/2 max-w-md mx-auto flex flex-col justify-center p-8 sm: w-[90%]">
            <div>
                <h2 class="text-2xl font-semibold text-dark-main dark:text-light-main">Iniciar Sessió</h2>
            </div>
            <div class="mt-8 space-y-6">
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-light-main">Correu Electrònic</label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            v-model="auth.user.email"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-light-main" />
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-light-main">Contrasenya</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            v-model="auth.user.password"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-light-main" />
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox"
                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                        <label for="remember-me" class="ml-2 block text-sm text-gray-900 dark:text-light-main">Recorda'm</label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-indigo-600 hover:underline dark:text-indigo-400">Has oblidat la contrasenya?</a>
                    </div>
                </div>
                <div class="text-red-600 text-sm">
                    {{ auth.errorMessage }}
                </div>
                <div>
                    <!-- <div v-if="loginError" class="mb-4 text-red-500 text-sm">{{ loginError }}</div> -->
                    <button @click="auth.login()"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Iniciar Sessió
                    </button>
                </div>
            </div>
        </div>
        <div class="w-1/2 relative flex flex-col justify-center p-8 bg-cover hidden sm:block">
            <img src="@/public/loginImagen.png" alt="Login Image"
                class="absolute top-0 left-0 w-full h-full object-cover" />
        </div>
    </div>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth';

definePageMeta({
    layout: 'default',
});

const auth = useAuth();
</script>

<style scoped></style>