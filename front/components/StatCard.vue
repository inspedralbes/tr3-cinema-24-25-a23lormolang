<template>
    <div class="bg-light-main dark:bg-dark-main p-4 rounded-xl">
        <div class="flex items-center gap-3">
            <div class="p-2 rounded-lg bg-primary-500/20">
                <i class="bi" :class="[icon, 'text-primary-500 text-xl']"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ title }}</p>
                <p class="text-lg font-semibold text-dark-main dark:text-light-main">{{ value }}</p>
            </div>
        </div>
    </div>
</template>
<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  icon: {
    type: String,
    required: true
  }
});
</script>
