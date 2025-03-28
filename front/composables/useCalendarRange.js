import { ref, computed, onMounted, onUnmounted } from 'vue';

export function useCalendarRange() {
  const today = new Date();
  const maxDate = new Date();
  maxDate.setDate(today.getDate() + 27);
  
  const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 0);
  const stepSize = ref(7);

  const getFormattedDate = (date) => date.toISOString().split('T')[0];

  const calculateStepSize = (width) => {
    if (width < 768) return 2;
    if (width <= 1024) return 4;
    return 7;
  };

  const updateDimensions = () => {
    if (typeof window !== 'undefined') {
      windowWidth.value = window.innerWidth;
      stepSize.value = calculateStepSize(windowWidth.value);
    }
  };

  // Solo en cliente
  if (typeof window !== 'undefined') {
    onMounted(() => {
      window.addEventListener('resize', updateDimensions);
      updateDimensions();
    });

    onUnmounted(() => {
      window.removeEventListener('resize', updateDimensions);
    });
  }

  return {
    maxDate,
    today: getFormattedDate(today),
    maxDateFormatted: getFormattedDate(maxDate),
    stepSize,
    getFormattedDate
  };
}