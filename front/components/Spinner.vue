<template>
    <div
      class="inline-flex items-center justify-center animate-spin rounded-full border-solid border-e-transparent motion-reduce:animate-[spin_1.5s_linear_infinite]"
      :class="[sizeClasses, colorClasses]"
      role="status"
      :style="{ borderWidth: borderWidth }"
    >
      <span class="sr-only">Loading...</span>
    </div>
  </template>
  
  <script setup>
  const props = defineProps({
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
    },
    color: {
      type: String,
      default: 'primary',
      validator: (value) => ['primary', 'secondary', 'accent', 'white', 'black'].includes(value)
    }
  });
  
  const sizeClasses = computed(() => {
    return {
      xs: 'h-4 w-4',
      sm: 'h-6 w-6',
      md: 'h-8 w-8',
      lg: 'h-10 w-10',
      xl: 'h-12 w-12'
    }[props.size];
  });
  
  const borderWidth = computed(() => {
    return {
      xs: '2px',
      sm: '3px',
      md: '4px',
      lg: '5px',
      xl: '6px'
    }[props.size];
  });
  
  const colorClasses = computed(() => {
    return {
      primary: 'border-primary-500',
      secondary: 'border-secondary-500',
      accent: 'border-accent-500',
      white: 'border-white',
      black: 'border-black'
    }[props.color];
  });
  </script>