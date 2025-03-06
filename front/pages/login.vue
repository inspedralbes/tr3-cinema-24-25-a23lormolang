<template>
    <div class="flex min-h-screen bg-gray-50">
        <div class="w-1/2 max-w-md mx-auto flex flex-col justify-center p-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">Iniciar Sesión</h2>
            </div>
            <div class="mt-8 space-y-6">
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            v-model="auth.user.email"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            v-model="auth.user.password"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox"
                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                        <label for="remember-me" class="ml-2 block text-sm text-gray-900">Recuerdame</label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-indigo-600 hover:underline">Olvidaste la contraseña?</a>
                    </div>
                </div>

                <div>
                    <!-- <div v-if="loginError" class="mb-4 text-red-500 text-sm">{{ loginError }}</div> -->
                    <button @click="auth.login()"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Iniciar Sesión
                    </button>
                </div>
            </div>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-gray-50 text-gray-500">O continuar con</span>
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-3">
                    <div>
                        <a href="#"
                            class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20px" height="20px">
                                <path fill="#EA4335"
                                    d="M24 9.5c3.54 0 6.77 1.22 9.33 3.57l7.02-7.03C36.01 2.56 30.3 0 24 0 14.55 0 6.47 5.33 2.47 13.09l8.26 6.42C12.7 13.8 17.98 9.5 24 9.5z" />
                                <path fill="#4285F4"
                                    d="M46.5 24c0-1.55-.14-3.05-.41-4.5H24v9.29h12.74c-.94 3.51-3.29 6.49-6.33 8.45l8.17 6.35C44.43 39.33 46.5 32.17 46.5 24z" />
                                <path fill="#FBBC05"
                                    d="M11.03 27.32c-.5-1.47-.79-3.03-.79-4.66s.29-3.19.79-4.66L2.47 13.09C.91 16.16 0 19.51 0 23c0 3.49.91 6.84 2.47 9.91l8.56-6.48z" />
                                <path fill="#34A853"
                                    d="M24 48c6.3 0 11.63-2.07 15.51-5.62l-8.17-6.35c-2.27 1.52-5.19 2.42-8.34 2.42-6.02 0-11.3-4.3-13.27-10.06l-8.26 6.42C6.47 42.67 14.55 48 24 48z" />
                                <path fill="none" d="M0 0h48v48H0z" />
                            </svg>
                            <span class="ml-2">Google</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-1/2 relative flex flex-col justify-center p-8 bg-cover">
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