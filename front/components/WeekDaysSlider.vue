<template>
    <div class="flex items-center bg-primary-600 dark:bg-primary-800 text-white p-4 rounded-lg mt-4">
        <!-- Botón semana anterior -->
        <button @click="previousWeek" class="text-white mr-4">
            <i class="bi bi-chevron-left text-2xl"></i>
        </button>

        <h2 class="flex-1 text-lg text-light-main font-semibold text-center">
            Setmana del {{ formattedStartDate }} al {{ formattedEndDate }}
        </h2>

        <!-- Botón semana siguiente -->
        <button @click="nextWeek" class="text-white ml-4">
            <i class="bi bi-chevron-right text-2xl"></i>
        </button>
    </div>

    <!-- Días de la semana -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 my-6">
        <div v-for="day in weekDays" :key="day.date" class="p-4 rounded-lg cursor-pointer"
            :class="day.date === selectedDate ? 'bg-primary-500 dark:bg-primary-700 hover:bg-primary-400 dark:hover:bg-primary-900 text-dark-main dark:text-light-main' : 'dark:text-light-main bg-gray-300 hover:bg-gray-200 dark:bg-dark-tertiary dark:hover:bg-dark-secondary'" @click="selectDate(day.date)">
            <p class="font-semibold">{{ day.formatted }}</p>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: {
        type: String,
        default: null
    }
});
const emits = defineEmits(['update:modelValue']);

const selectedDate = ref(props.modelValue || null);
const currentWeek = ref(new Date());

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
            formatted: formatAbbrevDate(date),
        };
    });
});

function formatAbbrevDate(dateObj) {
    const options = { weekday: 'short', day: 'numeric', month: 'short' };
    let formatted = dateObj.toLocaleDateString('ca-CA', options);
    return formatted
        .split(' ')
        .map((word) => {
            word = word.replace('.', '');
            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        })
        .join(' ');
}

function selectDate(date) {
    selectedDate.value = date;
    emits('update:modelValue', date);
}

function previousWeek() {
    const newDate = new Date(currentWeek.value);
    newDate.setDate(newDate.getDate() - 7);
    currentWeek.value = newDate;
}

function nextWeek() {
    const newDate = new Date(currentWeek.value);
    newDate.setDate(newDate.getDate() + 7);
    currentWeek.value = newDate;
}

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

onMounted(() => {
    if (!selectedDate.value) {
        selectedDate.value = currentWeek.value.toISOString().split('T')[0];
        emits('update:modelValue', selectedDate.value);
    }
});
</script>