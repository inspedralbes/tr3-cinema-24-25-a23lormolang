<template>

</template>

<script setup>
definePageMeta({
    layout: 'default',
});

const authStore = useAuthStore();

onMounted(async () => {
    if(!authStore.isAuthenticated){
        navigateTo('/login')
    }
});
</script>