<template>
    <div class="min-h-screen bg-light-main dark:bg-dark-main py-12">
        <div class="max-w-md mx-auto px-4">
            <div class="bg-light-secondary dark:bg-dark-secondary rounded-xl shadow-lg p-6 md:p-8">
                <h1 class="text-2xl font-bold text-dark-main dark:text-light-main mb-6">
                    Accés al teu historial
                </h1>

                <form @submit.prevent="requestAccess" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-dark-main dark:text-light-main">
                            Correu electrònic
                        </label>
                        <input v-model="email" type="email" required
                            class="w-full px-4 py-3 bg-light-quaternary dark:bg-dark-tertiary rounded-lg border border-light-tertiary dark:border-dark-tertiary focus:ring-2 focus:ring-primary-400 text-dark-main dark:text-light-main"
                            placeholder="exemple@correu.cat">
                    </div>

                    <div class="flex justify-center">
                        <button type="submit" :disabled="loading" class="font-bold bg-gradient-to-r from-primary-400 to-tertiary-600 
                                    enabled:hover:from-primary-600 enabled:hover:to-tertiary-800 dark:from-purple-600 dark:to-indigo-600 
                                    dark:enabled:hover:from-purple-700 dark:enabled:hover:to-indigo-700 text-dark-main dark:text-light-main 
                                    cursor-pointer px-8 py-3 h-[60px] rounded-lg enabled:transition-opacity enabled:hover:opacity-90 w-[270px]
                                    ">
                            <span v-if="!loading">Enviar enllaç d'accés</span>
                            <span v-else><Spinner size="md" color="primary" /></span>
                        </button>
                    </div>



                    <div v-if="success" class="p-4 bg-green-100 text-green-700 rounded-lg">
                        ✅ Hem enviat un enllaç al teu correu. Revisa la safata d'entrada o spam.
                    </div>

                    <div v-if="error" class="p-4 bg-red-100 text-red-700 rounded-lg">
                        ❌ Error: {{ error }}
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import Spinner from '@/components/Spinner.vue';
const email = ref('');
const loading = ref(false);
const success = ref(false);
const error = ref('');

const { $reservationCommunicationManager } = useNuxtApp();

const requestAccess = async () => {
    try {
        loading.value = true;
        error.value = '';
        success.value = false;

        await $reservationCommunicationManager.getAccessLink(email.value);

        success.value = true;
        email.value = '';
    } catch (err) {
        error.value = err.data?.message || 'Error en la sol·licitud';
    } finally {
        loading.value = false;
    }
};
</script>