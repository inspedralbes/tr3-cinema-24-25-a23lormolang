<template>
    <div class="flex min-h-screen bg-gray-50">
        <div class="w-1/2 max-w-md mx-auto flex flex-col justify-center p-8 sm: w-[90%]">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">Iniciar Sesió</h2>
            </div>
            <div class="mt-8 space-y-6">
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Correu Electrònic</label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            v-model="auth.user.email"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Contrasenya</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            v-model="auth.user.password"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox"
                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                        <label for="remember-me" class="ml-2 block text-sm text-gray-900">Recorda'm</label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-indigo-600 hover:underline">Has oblidat la contrasenya?</a>
                    </div>
                </div>
                <div class="text-red-600 text-sm">
                    {{ auth.errorMessage }}
                </div>
                <div>
                    <!-- <div v-if="loginError" class="mb-4 text-red-500 text-sm">{{ loginError }}</div> -->
                    <button @click="auth.login()"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Iniciar Sesió
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
    layout: 'auth',
});

const auth = useAuth();
</script>

<style scoped></style>