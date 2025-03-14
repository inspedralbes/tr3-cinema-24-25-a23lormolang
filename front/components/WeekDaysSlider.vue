<template>
  <div class="flex items-center bg-blue-600 text-white p-4 rounded-lg mt-4">
    <!-- Botón semana anterior -->
    <button @click="previousWeek" class="text-white mr-4">
      <i class="bi bi-chevron-left text-2xl"></i>
    </button>

    <h2 class="flex-1 text-lg font-semibold text-center">
      Semana del {{ formattedStartDate }} al {{ formattedEndDate }}
    </h2>

    <!-- Botón semana siguiente -->
    <button @click="nextWeek" class="text-white ml-4">
      <i class="bi bi-chevron-right text-2xl"></i>
    </button>
  </div>

  <!-- Días de la semana -->
  <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 my-6">
    <div
      v-for="day in weekDays"
      :key="day.date"
      class="p-4 border rounded-lg cursor-pointer"
      :class="day.date === selectedDate ? 'bg-blue-500 text-white' : 'bg-gray-100'"
      @click="selectDate(day.date)"
    >
      <p class="font-semibold">{{ day.formatted }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

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
      formatted: date.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric' }),
    };
  });
});

function selectDate(date) {
  selectedDate.value = date;
  emits('update:modelValue', date);
}

function previousWeek() {
  currentWeek.value.setDate(currentWeek.value.getDate() - 7);
}

function nextWeek() {
  currentWeek.value.setDate(currentWeek.value.getDate() + 7);
}

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

onMounted(() => {
  if (!selectedDate.value) {
    const today = new Date().toISOString().split('T')[0];
    selectedDate.value = today;
    emits('update:modelValue', today);
  }
});
</script>