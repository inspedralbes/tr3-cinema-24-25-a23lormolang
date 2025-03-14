import { ref } from 'vue';

export function useCalendarRange() {
  const startDate = ref(new Date());
  const endDate = ref(new Date());
  endDate.value.setDate(startDate.value.getDate() + 7);

  return {
    startDate,
    endDate,
  };
}
