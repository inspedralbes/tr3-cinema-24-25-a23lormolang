<template>
  <div class="flex justify-center">
    <div class="flex items-center justify-center py-2 w-full text-md md:text-lg lg:max-w-[1550px]">
      <button 
        @click="previousWeek" 
        class="text-dark-main cursor-pointer mx-2 px-2 py-1 pb-4 dark:text-light-main" 
        :disabled="!canGoBack"
      >
        <i class="bi bi-chevron-left"></i>
      </button>
  
      <div class="flex space-x-4 flex-1 justify-center overflow-x-auto">
        <div
          v-for="day in visibleDays"
          :key="day.date"
          :class="[
            'cursor-pointer px-2 pb-4 md:px-4 lg:px-8 min-w-[100px] text-center',
            day.date === selectedDate ? 'text-blue-500 border-b-2 border-blue-500' : 'text-dark-main opacity-70 dark:text-light-main'
          ]"
          @click="selectDate(day.date)"
        >
          {{ day.formatted }}
        </div>
      </div>
  
      <button 
        @click="nextWeek" 
        class="text-dark-main cursor-pointer mx-2 px-2 py-1 pb-4 dark:text-light-main" 
        :disabled="!canGoForward"
      >
        <i class="bi bi-chevron-right"></i>
      </button>
    </div>
  </div>
</template>

<script setup>
import { useCalendarRange } from '@/composables/useCalendarRange';

const props = defineProps({
  modelValue: String
});
const emits = defineEmits(['update:modelValue']);

const { 
  startDate, 
  maxDate, 
  today,
  stepSize,
  getFormattedDate
} = useCalendarRange();

const selectedDate = ref(props.modelValue || today);
const currentStart = ref(new Date());

function selectDate(date) {
  selectedDate.value = date;
  emits('update:modelValue', date);
}

const weekDays = computed(() => {
  const result = [];
  const start = new Date(currentStart.value);
  
  // Generar 7 días desde currentStart
  for (let i = 0; i < 7; i++) {
    const day = new Date(start);
    day.setDate(start.getDate() + i);
    
    if (day > maxDate) break;
    const isoDate = day.toISOString().split('T')[0];
    result.push({
      date: getFormattedDate(day),
      formatted: isoDate === today ? 'HOY' : formatAbbrevDate(day)
    });
  }
  
  return result;
});

const visibleDays = computed(() => {
  return weekDays.value.slice(0, stepSize.value);
});

// Navegación
function previousWeek() {
  const newStart = new Date(currentStart.value);
  newStart.setDate(newStart.getDate() - stepSize.value);
  
  if (newStart >= new Date(today)) {
    currentStart.value = newStart;
  }
}

function nextWeek() {
  const newStart = new Date(currentStart.value);
  newStart.setDate(newStart.getDate() + stepSize.value);
  
  if (newStart <= maxDate) {
    currentStart.value = newStart;
  }
}

// Control de botones
const canGoBack = computed(() => 
  new Date(currentStart.value) > new Date(today)
);

const canGoForward = computed(() => {
  const nextStart = new Date(currentStart.value);
  nextStart.setDate(nextStart.getDate() + stepSize.value);
  return nextStart <= maxDate;
});

// Formateo de fechas
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
</script>