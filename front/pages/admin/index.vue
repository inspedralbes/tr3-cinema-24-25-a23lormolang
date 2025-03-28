<template>
    <div v-if="loading" class="text-center py-8">
        <Spinner size="xl" color="primary" />
    </div>
    <div v-else>
        <div class="container mx-auto p-4">
            <div class="text-light-main flex items-center justify-center bg-red-500 w-full md:hidden text-lg h-20">
                VISTA NO PENSADA PER SMARTPHONE
            </div>
            <!-- Barra de navegación semanal -->
            <WeekDaysSlider v-model="selectedDate" />

            <div v-if="selectedDate" class="mt-6 p-4 bg-gray-100 dark:bg-dark-tertiary rounded-lg">
                <div class="flex items-center justify-between w-full mb-4">
                    <h3 class="font-semibold text-lg text-dark-main dark:text-light-main">Sessions per {{ new
                        Date(selectedDate).toLocaleDateString('ca-ES', {
                            day: '2-digit', month: '2-digit', year:
                        'numeric' }) }}</h3>
                    <button @click="openScreenDialog(null)"
                        class="cursor-pointer bg-tertiary-500 hover:bg-tertiary-700 px-4 py-2 rounded-lg flex items-center">
                        <i class="bi bi-plus-lg mr-2"></i> Nova Sessió
                    </button>
                </div>
                <div class="flex flex-col gap-2 md:gap-4 items-center">
                    <div v-for="screen in screensForDay(selectedDate)" :key="screen.id"
                        class="mt-2 p-3 bg-gray-300 dark:bg-dark-secondary shadow rounded-lg flex items-center justify-between md:w-[600px]">
                        <!-- Contenido izquierdo (imagen + info) -->
                        <div class="flex items-center flex-1">
                            <img :src="screen.movie.poster_url"
                                class="w-16 h-24 md:w-26 md:h-38 object-cover rounded-lg" />
                            <div class="ml-4 flex-1">
                                <p class="font-semibold md:text-2xl text-dark-main dark:text-light-main mb-3">{{
                                    screen.time }} - {{ screen.movie.title }}</p>
                                <p class="text-sm md:text-lg text-gray-600 dark:text-light-main">Butaques: {{
                                    screen.stats.normal_occupied }}/{{ screen.stats.normal_seats }}</p>
                                <p class="text-sm md:text-lg text-gray-600 dark:text-light-main">Butaques VIP: {{
                                    screen.stats.vip_occupied }}/{{ screen.room.vip_seats }}</p>
                                <p class="text-sm md:text-lg text-gray-600 dark:text-light-main">Recaudació: {{
                                    formatCurrency(screen.stats.revenue) }}</p>
                            </div>
                        </div>

                        <!-- Botones derecha -->
                        <div class="flex gap-2 space-x-2 ml-4">
                            <button @click="openScreenDialog(screen)" class="cursor-pointer text-primary-500">
                                <i class="bi bi-pencil-square text-2xl"></i>
                            </button>
                            <button @click="deleteScreen(screen.id)" class="cursor-pointer text-red-500">
                                <i class="bi bi-trash text-2xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-8 mb-4 font-semibold">
                    <div class="flex space-x-4 mt-2 justify-center">
                        <div class="bg-primary-600 dark:bg-primary-800 text-light-main px-4 py-2 rounded-lg">
                            Normal: {{ formatCurrency(dailyRevenue.normal) }} ({{ dailyRevenue.normalTickets }} tickets)
                        </div>
                        <div class="bg-secondary-600 dark:bg-secondary-800 text-white px-4 py-2 rounded-lg">
                            VIP: {{ formatCurrency(dailyRevenue.vip) }} ({{ dailyRevenue.vipTickets }} tickets)
                        </div>
                        <div class="bg-tertiary-800 dark:bg-tertiary-800 text-white px-4 py-2 rounded-lg">
                            Total: {{ formatCurrency(dailyRevenue.total) }} ({{ dailyRevenue.totalTickets }} tickets)
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para crear/editar sesión -->
            <div v-if="screenDialog" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-light-secondary dark:bg-dark-secondary p-6 rounded-lg w-96">
                    <h3 class="text-lg font-semibold mb-4 text-dark-main dark:text-light-main">{{ editingScreen ?
                        'Editar Sessió' : 'Nova Sessió' }}</h3>
                    <!-- Selector de sala en edición -->
                    <select v-model="selectedRoom"
                        class="w-full p-2 border text-dark-main dark:text-light-main dark:border-light-main rounded-lg mb-2">
                        <option v-for="room in rooms" :key="room.id" :value="room.id"
                            :selected="room?.id === editingScreen?.room?.id"
                            class="bg-light-main text-dark-main dark:bg-dark-main dark:text-light-main ">
                            {{ room.name }} ({{ room.vip_seats }} seients vips)
                        </option>
                    </select>


                    <!-- Sección de creación -->
                    <div v-if="!editingScreen">
                        <input v-model="searchQuery" type="text" placeholder="Buscar pel·lícula a OMDB"
                            class="w-full p-2 border rounded-lg mb-2 text-dark-main dark:text-light-main"
                            @input="searchMovies" />
                        <select v-model="selectedMovie"
                            class="w-full p-2 border rounded-lg mb-2 text-dark-main dark:text-light-main">
                            <option v-for="movie in movieResults" :key="movie.imdbID" :value="movie"
                                class="bg-light-main text-dark-main dark:bg-dark-main dark:text-light-main">
                                {{ movie.Title }}
                            </option>
                        </select>
                        <div v-if="selectedMovie"
                            class="flex space-x-4 items-center mb-2 text-dark-main dark:text-light-main">
                            <img :src="selectedMovie.Poster" class="w-16 h-24 object-cover rounded-lg" />
                            <div>
                                <h4 class="font-semibold">{{ selectedMovie.Title }}</h4>
                                <p class="text-sm text-gray-600">{{ selectedMovie.Year }}</p>
                            </div>
                        </div>
                        <input v-model="newScreen.time" type="time"
                            class="w-full text-dark-main dark:text-light-main p-2 border !dark:border-light-main rounded-lg" />
                    </div>

                    <!-- Sección de edición -->
                    <div v-else class="flex mb-4 text-dark-main dark:text-light-main">
                        <img :src="editingScreen.movie.poster_url" alt="Movie Image" class="h-[150px] w-[100px]">
                        <div class="flex flex-col ml-2">
                            <p class="mb-2 ml-1 font-semibold">Pel·lícula: {{ editingScreen.movie.title }}</p>
                            <p class="mb-4 ml-1">Horari actual: {{ editingScreen.time }}</p>
                            <input v-model="newScreen.time" type="time"
                                class="w-full text-dark-main dark:text-light-main p-2 border !dark:border-light-main rounded-lg" />
                        </div>
                    </div>

                    <div class="mt-2 flex items-center text-dark-main dark:text-light-main space-x-4">
                        <label>
                            <input type="checkbox" v-model="is_special" :true-value="1" :false-value="0" />
                            Sessió especial
                        </label>
                    </div>

                    <div class="mt-4 flex justify-between">
                        <button @click="closeDialog"
                            class="cursor-pointer bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                            Cancelar
                        </button>
                        <button @click="saveScreen"
                            class="cursor-pointer bg-tertiary-500 hover:bg-tertiary-700 text-white px-4 py-2 rounded-lg w-[95px] h-[40px]">
                            <template v-if="saving">
                                <Spinner size="sm" color="primary" />
                            </template>
                            <template v-else>
                                Guardar
                            </template>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Informe de recaudación -->
            <!-- <div class="bg-gray-100 p-4 rounded-lg mt-6">
            <h3 class="font-semibold text-lg">Informe de Recaudació</h3>
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
        </div> -->
        </div>
    </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/authStore';
import { useCalendarRange } from '@/composables/useCalendarRange';
import WeekDaysSlider from '@/components/WeekDaysSlider.vue';
import Spinner from '@/components/Spinner.vue';

const loading = ref(true);
const saving = ref(false)
const authStore = useAuthStore();
const { $screeningCommunicationManager } = useNuxtApp()
const { $movieCommunicationManager } = useNuxtApp()
const { $roomCommunicationManager } = useNuxtApp()
const currentWeek = ref(new Date());
const selectedDate = ref(null);
const screenDialog = ref(false);
const editingScreen = ref(null);
const searchQuery = ref('');
const movieResults = ref([]);
const selectedMovie = ref(null);
const screens = ref([]);
const rooms = ref([]);
const selectedRoom = ref(null);
const is_special = ref(0);
let debounceTimer = null;
const newScreen = ref({ time: '' });
const { startDate, endDate, today } = useCalendarRange();

// Configuración inicial
onMounted(async () => {
    loading.value = true;
    await loadRooms();
    await loadScreens();
    selectedDate.value = today;
    loading.value = false;
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
            formatted: date.toLocaleDateString('ca-CA', { weekday: 'long', day: 'numeric' }),
        };
    });
});

const formattedStartDate = computed(() => {
    const start = new Date(currentWeek.value);
    start.setDate(start.getDate() - start.getDay());
    return start.toLocaleDateString('ca-CA', { day: 'numeric', month: 'short' });
});

const formattedEndDate = computed(() => {
    const end = new Date(currentWeek.value);
    end.setDate(end.getDate() + (6 - end.getDay()));
    return end.toLocaleDateString('ca-CA', { day: 'numeric', month: 'short' });
});

// Seleccionar fecha
const selectDate = (date) => {
    selectedDate.value = date;
};

// Abrir diálogo de creación/edición
const openScreenDialog = (screen) => {
    editingScreen.value = screen ? { ...screen } : null;
    selectedRoom.value = screen?.room?.id || null;
    selectedMovie.value = screen?.movie || null;
    newScreen.value = {
        time: screen?.time || '',
        date: screen?.date || selectedDate.value
    };
    // Load is_special data when editing
    if (screen) {
        is_special.value = screen.is_special;
    } else {
        is_special.value = 0;
    }

    screenDialog.value = true;
    if (!screen) {
        searchQuery.value = '';
        movieResults.value = [];
    }
};

const closeDialog = () => {
    screenDialog.value = false;
    is_special.value = 0;
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
        screens.value = await $screeningCommunicationManager.getAdminScreenings();
    } catch (error) {
        console.error("Error cargando sesiones:", error);
        screens.value = [];
    }
};

const loadRooms = async () => {
    try {
        rooms.value = await $roomCommunicationManager.getAllRooms();
    } catch (error) {
        console.error("Error cargando salas:", error);
        rooms.value = [];
    }
};

const saveScreen = async () => {
    try {
        saving.value = true;
        if (editingScreen.value) {
            // Actualizar sesión existente
            await $screeningCommunicationManager.updateScreening(editingScreen.value.id, {
                room_id: selectedRoom.value,
                date: newScreen.value.date,
                time: newScreen.value.time,
                is_special: is_special.value,
            });
        } else {
            // Crear nueva sesión
            const movieResponse = await $movieCommunicationManager.createMovie(selectedMovie.value.imdbID);
            await $screeningCommunicationManager.createScreening({
                room_id: selectedRoom.value,
                movie_id: movieResponse.id,
                date: selectedDate.value,
                time: newScreen.value.time,
                is_special: is_special.value,
            });
        }
        saving.value = false;
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
        acc.normalTickets += screen.stats.normal_occupied;
        acc.vipTickets += screen.stats.vip_occupied;
        acc.totalTickets += screen.stats.occupied;
        const normalRevenue = screen.stats.normal_occupied * 6;
        const vipRevenue = screen.stats.vip_occupied * 8;
        acc.normal += normalRevenue;
        acc.vip += vipRevenue;
        acc.total += screen.stats.revenue;
        return acc;
    }, { normalTickets: 0, vipTickets: 0, totalTickets: 0, normal: 0, vip: 0, total: 0 });
});

// Helpers
const formatCurrency = (value) => {
    return new Intl.NumberFormat('ca-CA', { style: 'currency', currency: 'EUR' }).format(value);
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
        normalSum += s.stats.normal_occupied * 6;
        vipSum += s.stats.vip_occupied * 8;
        normalTickets += s.stats.normal_occupied;
        vipTickets += s.stats.vip_occupied;
        totalTickets += (s.stats.normal_occupied + s.stats.vip_occupied);
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
</script>