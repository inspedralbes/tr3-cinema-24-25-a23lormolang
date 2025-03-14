<template>
    <div class="container mx-auto p-4">
        <!-- Barra de navegación semanal -->
        <WeekDaysSlider v-model="selectedDate" />
        <!-- Quitamos el grid de días y usamos el componente -->

        <div v-if="selectedDate" class="mt-6 p-4 bg-gray-100 rounded-lg">
            <h3 class="font-semibold text-lg mb-2">Sesiones para {{ selectedDate }}</h3>
            <div v-for="screen in screensForDay(selectedDate)" :key="screen.id"
                class="mt-2 p-3 bg-white shadow rounded-lg flex justify-between items-center">
                <div>
                    <p class="font-semibold">{{ screen.time }} - {{ screen.movie.title }}</p>
                    <p class="text-sm text-gray-600">Butacas: {{ screen.occupied_seats }}/{{ screen.total_seats }}</p>
                    <p class="text-sm text-gray-600">Butacas VIP: {{ screen.vip_occupied }}/{{ screen.vip_seats }}</p>
                    <p class="text-sm text-gray-600">Recaudación: {{ formatCurrency(screen.revenue) }}</p>
                </div>
                <div class="flex space-x-2">
                    <button @click="openScreenDialog(screen)" class="text-blue-500">
                        <i class="bi bi-pencil-square text-lg"></i>
                    </button>
                    <button @click="deleteScreen(screen.id)" class="text-red-500">
                        <i class="bi bi-trash text-lg"></i>
                    </button>
                </div>
            </div>
            <div class="mt-4 font-semibold">
                <div class="flex space-x-4 mt-2 justify-center">
                    <div class="bg-blue-800 text-white px-4 py-2 rounded-lg">
                        Normal: {{ formatCurrency(dailyRevenue.normal) }} ({{ dailyRevenue.normalTickets }} tickets)
                    </div>
                    <div class="bg-purple-800 text-white px-4 py-2 rounded-lg">
                        VIP: {{ formatCurrency(dailyRevenue.vip) }} ({{ dailyRevenue.vipTickets }} tickets)
                    </div>
                    <div class="bg-green-800 text-white px-4 py-2 rounded-lg">
                        Total: {{ formatCurrency(dailyRevenue.total) }} ({{ dailyRevenue.totalTickets }} tickets)
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para crear/editar sesión -->
        <div v-if="screenDialog" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-96">
                <h3 class="text-lg font-semibold mb-4">{{ editingScreen ? 'Editar Sesión' : 'Nueva Sesión' }}</h3>

                <!-- Sección de creación -->
                <div v-if="!editingScreen">
                    <input v-model="searchQuery" type="text" placeholder="Buscar película en OMDB"
                        class="w-full p-2 border rounded-lg mb-2" @input="searchMovies" />
                    <select v-model="selectedMovie" class="w-full p-2 border rounded-lg mb-2">
                        <option v-for="movie in movieResults" :key="movie.imdbID" :value="movie">{{ movie.Title }}
                        </option>
                    </select>
                    <div v-if="selectedMovie" class="flex space-x-4 items-center mb-2">
                        <img :src="selectedMovie.Poster" class="w-16 h-24 object-cover rounded-lg" />
                        <div>
                            <h4 class="font-semibold">{{ selectedMovie.Title }}</h4>
                            <p class="text-sm text-gray-600">{{ selectedMovie.Year }} | {{ selectedMovie.Runtime }}</p>
                        </div>
                    </div>
                    <input v-model="newScreen.time" type="time" class="w-full p-2 border rounded-lg" />
                </div>

                <!-- Sección de edición -->
                <div v-else class="flex mb-4">
                    <img :src="editingScreen.movie.poster_url" alt="Movie Image" class="h-[150px] w-[100px]">
                    <div class="flex flex-col ml-2">
                        <p class="mb-2 ml-1 font-semibold">Película: {{ editingScreen.movie.title }}</p>
                        <p class="mb-4 ml-1">Horario actual: {{ editingScreen.time }}</p>
                        <input v-model="newScreen.time" type="time" class="w-full p-2 border rounded-lg" />
                    </div>
                </div>

                <div class="mt-2 flex items-center space-x-4">
                    <label>
                        <input type="checkbox" v-model="infoScreen.is_special" :true-value="1" :false-value="0" />
                        Sesión especial
                    </label>
                    <label>
                        <input type="checkbox" v-model="infoScreen.is_vip_active" :true-value="1" :false-value="0" />
                        Sesión VIP
                    </label>
                </div>

                <div class="mt-4 flex justify-between">
                    <button @click="closeDialog" class="bg-red-500 text-white px-4 py-2 rounded-lg">
                        Cancelar
                    </button>
                    <button @click="saveScreen" class="bg-green-500 text-white px-4 py-2 rounded-lg">
                        Guardar
                    </button>
                </div>
            </div>
        </div>

        <!-- Informe de recaudación -->
        <div class="bg-gray-100 p-4 rounded-lg mt-6">
            <h3 class="font-semibold text-lg">Informe de Recaudación</h3>
            <div class="flex space-x-4 mt-2">
                <div class="bg-blue-500 text-white px-4 py-2 rounded-lg">
                    Normal: {{ formatCurrency(report.normal) }} ({{ report.normalTickets }} tickets)
                </div>
                <div class="bg-purple-500 text-white px-4 py-2 rounded-lg">
                    VIP: {{ formatCurrency(report.vip) }} ({{ report.vipTickets }} tickets)
                </div>
                <div class="bg-green-500 text-white px-4 py-2 rounded-lg">
                    Total: {{ formatCurrency(report.total) }} ({{ report.totalTickets }} tickets)
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/authStore';
import { useCalendarRange } from '@/composables/useCalendarRange';
import WeekDaysSlider from '@/components/WeekDaysSlider.vue';

const authStore = useAuthStore();
const { $screeningCommunicationManager } = useNuxtApp()
const { $movieCommunicationManager } = useNuxtApp()
const currentWeek = ref(new Date());
const selectedDate = ref(null);
const screenDialog = ref(false);
const editingScreen = ref(null);
const searchQuery = ref('');
const movieResults = ref([]);
const selectedMovie = ref(null);
const screens = ref([]);
let infoScreen = reactive({});
let debounceTimer = null;
const newScreen = ref({ time: '' });
const { startDate, endDate, today } = useCalendarRange();

// Configuración inicial
onMounted(async () => {
    if (!authStore.isAuthenticated) navigateTo('/login');
    await loadScreens();
    selectedDate.value = today;
});

const screensForDay = (date) => {
    return screens.value.filter(screen => {
        // Asegurar formato de fecha ISO (YYYY-MM-DD)
        const screenDate = new Date(screen.date).toISOString().split('T')[0];
        const screensForDay = (date) => {
            return screens.value.filter(screen => {
                // Asegurar formato de fecha ISO (YYYY-MM-DD)
                const screenDate = new Date(screen.date).toISOString().split('T')[0];
                return screenDate === date;
            });
        }; return screenDate === date;
    });
};

// Navegación entre semanas
const previousWeek = () => {
    currentWeek.value = new Date(currentWeek.value.setDate(currentWeek.value.getDate() - 7));
};

const nextWeek = () => {
    currentWeek.value = new Date(currentWeek.value.setDate(currentWeek.value.getDate() + 7));
};

// Días de la semana actual
const weekDays = computed(() => {
    const start = new Date(currentWeek.value);
    let dayOfWeek = start.getDay();
    let diff = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
    start.setDate(start.getDate() - diff);

    return Array.from({ length: 7 }).map((_, i) => {
        const date = new Date(start);
        date.setDate(date.getDate() + i);
        return {
            date: date.toISOString().split('T')[0],
            formatted: date.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric' }),
        };
    });
});

const formattedStartDate = computed(() => {
    const start = new Date(currentWeek.value);
    start.setDate(start.getDate() - start.getDay());
    return start.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
});

const formattedEndDate = computed(() => {
    const end = new Date(currentWeek.value);
    end.setDate(end.getDate() + (6 - end.getDay()));
    return end.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
});

// Seleccionar fecha
const selectDate = (date) => {
    selectedDate.value = date;
};

// Abrir diálogo de creación/edición
const openScreenDialog = (screen) => {
    editingScreen.value = screen ? { ...screen } : null;
    selectedMovie.value = screen?.movie || null;
    newScreen.value = {
        time: screen?.time || '',
        date: screen?.date || selectedDate.value
    };
    // Load infoScreen data when editing
    if (screen) {
        infoScreen.is_special = screen.is_special;
        infoScreen.is_vip_active = screen.is_vip_active;
        infoScreen.total_seats = screen.total_seats;
        infoScreen.vip_seats = screen.vip_seats;
    } else {
        infoScreen.is_special = 0;
        infoScreen.is_vip_active = 0;
        infoScreen.total_seats = 120;
        infoScreen.vip_seats = 0;
    }

    screenDialog.value = true;
    if (!screen) {
        searchQuery.value = '';
        movieResults.value = [];
    }
};

const closeDialog = () => {
    screenDialog.value = false;
    infoScreen.is_special = 0;
    infoScreen.is_vip_active = 0;
    infoScreen.total_seats = 120;
    infoScreen.vip_seats = 0;
};

const searchMovies = () => {
    if (searchQuery.value.length < 3) return;
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(async () => {
        try {
            movieResults.value = await $movieCommunicationManager.searchMovies(searchQuery.value);
        } catch (error) {
            console.error("Error buscando películas:", error);
            movieResults.value = [];
        }
    }, 500); // 500 ms = 0.5 segundos
};

// Y corrige la carga de pantallas:
const loadScreens = async () => {
    try {
        const startDate = currentWeek.value.toISOString().split('T')[0];
        const endDate = new Date(currentWeek.value);
        endDate.setDate(endDate.getDate() + 6);

        screens.value = await $screeningCommunicationManager.getScreenings(
            startDate,
            endDate.toISOString().split('T')[0]
        );
    } catch (error) {
        console.error("Error cargando sesiones:", error);
        screens.value = [];
    }
};

const saveScreen = async () => {
    try {
        if (editingScreen.value) {
            // Actualizar sesión existente
            await $screeningCommunicationManager.updateScreening(editingScreen.value.id, {
                date: newScreen.value.date,
                time: newScreen.value.time,
                total_seats: infoScreen.total_seats,
                vip_seats: infoScreen.vip_seats,
                is_special: infoScreen.is_special,
                is_vip_active: infoScreen.is_vip_active
            });
        } else {
            // Crear nueva sesión
            const movieResponse = await $movieCommunicationManager.createMovie(selectedMovie.value.imdbID);
            await $screeningCommunicationManager.createScreening({
                movie_id: movieResponse.id,
                date: selectedDate.value,
                time: newScreen.value.time,
                total_seats: infoScreen.total_seats,
                vip_seats: infoScreen.vip_seats,
                is_special: infoScreen.is_special,
                is_vip_active: infoScreen.is_vip_active
            });
        }
        await loadScreens();
    } catch (error) {
        console.error("Error guardando sesión:", error);
    } finally {
        closeDialog();
    }
};

const deleteScreen = async (id) => {
    if (confirm("¿Estás seguro de eliminar esta sesión?")) {
        try {
            await $screeningCommunicationManager.deleteScreening(id);
            await loadScreens();
        } catch (error) {
            console.error("Error eliminando sesión:", error);
        }
    }
};

// Generar informe
const report = computed(() => {
    return screens.value.reduce((acc, screen) => {
        acc.normalTickets += screen.normal_occupied;
        acc.vipTickets += screen.vip_occupied;
        acc.totalTickets = acc.totalTickets + screen.normal_occupied + screen.vip_occupied;
        const normalRevenue = screen.normal_occupied * 6;
        const vipRevenue = screen.vip_occupied * 8;
        acc.normal += normalRevenue;
        acc.vip += vipRevenue;
        acc.total += (normalRevenue + vipRevenue);
        return acc;
    }, { normalTickets: 0, vipTickets: 0, totalTickets: 0, normal: 0, vip: 0, total: 0 });
});

// Helpers
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
};

const dailyRevenue = computed(() => {
    if (!selectedDate.value) {
        return { normal: 0, vip: 0, total: 0 };
    }
    const dayScreens = screensForDay(selectedDate.value);
    let normalSum = 0;
    let vipSum = 0;
    let normalTickets = 0;
    let vipTickets = 0;
    let totalTickets = 0;
    dayScreens.forEach(s => {
        normalSum += s.normal_occupied * 6;
        vipSum += s.vip_occupied * 8;
        normalTickets += s.normal_occupied;
        vipTickets += s.vip_occupied;
        totalTickets += (s.normal_occupied + s.vip_occupied);
    });
    return {
        normalTickets,
        vipTickets,
        totalTickets,
        normal: normalSum,
        vip: vipSum,
        total: normalSum + vipSum
    };
});

watch(() => infoScreen.is_vip_active, (newValue) => {
    if (newValue) {
        infoScreen.total_seats = 110
        infoScreen.vip_seats = 10
    } else {
        infoScreen.total_seats = 120
        infoScreen.vip_seats = 0
    }
})
</script>