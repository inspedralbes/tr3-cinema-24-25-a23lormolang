<template>
    <div class="layout-container  dark:bg-gradient-to-br from-gray-900 to-gray-800">
        <!-- Botón para abrir/cerrar la barra lateral -->
        <div class="fixed top-0 left-0 right-0 z-50 md:relative bg-upbar h-[60px] dark:bg-gray-700 p-4">
            <button v-if="!visible" @click="toggleSideBar" :class="[
                'text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 absolute top-2.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white',
                visible ? 'hidden' : 'block md:hidden'
            ]">
                <i class="bi bi-chevron-right text-[18px] text-dark-main dark:text-light-main"></i>
            </button>
            <button @click="theme.toggleTheme()"
                class="absolute top-2 right-4 p-2 rounded-full z-50">
                <i v-if="theme.isDarkMode.value" class="bi bi-sun text-xl"></i>
                <i v-else class="bi bi-moon text-xl"></i>
            </button>
        </div>

        <!-- Barra lateral -->
        <div :class="[
            'fixed top-0 left-0 z-40 h-screen p-4 overflow-y-auto transition-all duration-300 bg-light-quaternary dark:bg-dark-tertiary',
            visible ? 'z-60 md:w-64  xs:w-36' : 'z-40 hidden w-16 overflow-hidden md:block md:z-50'
        ]">
            <!-- Título del menú (solo visible cuando está abierto) -->
            <h5 id="drawer-navigation-label" class="text-base font-semibold text-gray-500 uppercase dark:text-gray-400"
                :class="[visible ? 'visible' : 'opacity-0 ']">
                Menu
            </h5>

            <!-- Botón de cerrar -->
            <button @click="toggleSideBar" :class="[
                'text-dark-main dark:text-light-main bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 absolute top-2.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white ',
                visible ? 'right-2.5' : 'right-4'
            ]">
                <i v-if="visible" class="bi bi-chevron-left text-[18px]"></i>
                <i v-else class="bi bi-chevron-right text-[18px]"></i>
                <span class="sr-only">Close menu</span>
            </button>

            <!-- Contenido del menú -->
            <div class="pt-12 overflow-y-auto">
                <ul class="space-y-2 font-medium overflow-hidden">
                    <li class="cursor-pointer">
                        <a class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group h-12"
                            @click="navigateTo('/')">
                            <i class="bi bi-house-fill shrink-0 text-[18px] text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"></i>
                            <span :class="[ 'ms-3 transition-opacity duration-300 group-hover:text-primary-600', !visible && 'hidden' ]">
                                Menu Principal
                            </span>
                        </a>
                    </li>
                    <li class="cursor-pointer">
                        <a class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group h-12"
                            @click="navigateTo('/movies')">
                            <i class="bi bi-film shrink-0 text-[18px] text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"></i>
                            <span :class="[ 'ms-3 transition-opacity duration-300 group-hover:text-primary-600', !visible && 'hidden' ]">
                                Pel·lícules
                            </span>
                        </a>
                    </li>
                    <li class="cursor-pointer">
                        <a class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group h-12"
                            @click="navigateTo('/purchases')">
                            <i class="bi bi-kanban-fill shrink-0 text-[18px] text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"></i>
                            <span :class="[ 'ms-3 transition-opacity duration-300 group-hover:text-primary-600', !visible && 'hidden' ]">
                                Historal
                            </span>
                        </a>
                    </li>
                    <li v-if="!isAuthenticated" class="cursor-pointer">
                        <a class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group h-12"
                            @click="navigateTo('/admin/login')">
                            <i class="bi bi-box-arrow-right shrink-0 text-[18px] text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"></i>
                            <span :class="[ 'ms-3 transition-opacity duration-300 group-hover:text-primary-600', !visible && 'hidden' ]">
                                Iniciar Sessió
                            </span>
                        </a>
                    </li>
                    <li v-if="isAuthenticated" class="cursor-pointer">
                        <a class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group h-12"
                            @click="navigateTo('/admin')">
                            <i class="bi bi-person-lock shrink-0 text-[18px] text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"></i>
                            <span :class="[ 'ms-3 transition-opacity duration-300 group-hover:text-primary-600', !visible && 'hidden' ]">
                                Panell Administratiu
                            </span>
                        </a>
                    </li>
                    <li v-if="isAuthenticated" class="cursor-pointer">
                        <a class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group h-12"
                            @click="navigateTo('/admin/stats')">
                            <i class="bi bi-graph-up shrink-0 text-[18px] text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"></i>
                            <span :class="[ 'ms-3 transition-opacity duration-300 group-hover:text-primary-600', !visible && 'hidden' ]">
                                Estadistiques
                            </span>
                        </a>
                    </li>
                    <li v-if="isAuthenticated" class="cursor-pointer">
                        <a class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group h-12"
                            @click="auth.logout()">
                            <i class="bi bi-box-arrow-left shrink-0 text-[18px] text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"></i>
                            <span :class="[ 'ms-3 transition-opacity duration-300 group-hover:text-primary-600', !visible && 'hidden' ]">
                                Deslogejarse
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Contenido principal -->
        <main :class="[
            'flex-grow min-h-screen bg-light-main dark:bg-dark-main',
            visible ? 'ml-64 md:ml-16' : 'md:ml-16'
        ]">
            <div class="p-3 mt-14 md:p-6 md:mt-0">
                <slot />
            </div>
        </main>
    </div>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth';
import { useTheme } from '@/composables/useTheme';

const auth = useAuth();
const { isAuthenticated } = storeToRefs(auth);
const search = ref('');
const visible = ref(false);

function toggleSideBar() {
    visible.value = !visible.value;
}

const theme = useTheme();

</script>

<style lang="postcss" scoped>
/* Transición suave para el ancho */
.layout-container {
    transition: margin-left 0.3s;
}

/* Mantiene los íconos centrados cuando está mini */
.group {
    @apply justify-center;
}


.w-16 .py-4 {
    @apply px-0;
}

.main-content {
    transition: margin-left 0.3s;
}

/* Para dispositivos móviles */
@media (max-width: 768px) {
    .w-64 {
        width: 100%;
    }

    .ml-64 {
        margin-left: 0;
    }
}
</style>


<!-- 
Ejemplo con notificaciones y Pro
<li>
    <a href="#"
        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
            viewBox="0 0 18 18">
            <path
                d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10Zm10 0h-4.286A1.857 1.857 0 0 0 10 11.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 18 16.143v-4.286A1.857 1.857 0 0 0 16.143 10Z" />
        </svg>
        <span class="flex-1 ms-3 whitespace-nowrap">Kanban</span>
        <span
            class="inline-flex items-center justify-center px-2 ms-3 text-sm font-medium text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">Pro</span>
    </a>
</li>
<li>
    <a href="#"
        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
            viewBox="0 0 20 20">
            <path
                d="m17.418 3.623-.018-.008a6.713 6.713 0 0 0-2.4-.569V2h1a1 1 0 1 0 0-2h-2a1 1 0 0 0-1 1v2H9.89A6.977 6.977 0 0 1 12 8v5h-2V8A5 5 0 1 0 0 8v6a1 1 0 0 0 1 1h8v4a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-4h6a1 1 0 0 0 1-1V8a5 5 0 0 0-2.582-4.377ZM6 12H4a1 1 0 0 1 0-2h2a1 1 0 0 1 0 2Z" />
        </svg>
        <span class="flex-1 ms-3 whitespace-nowrap">Inbox</span>
        <span
            class="inline-flex items-center justify-center w-3 h-3 p-3 ms-3 text-sm font-medium text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300">3</span>
    </a>
</li> -->
