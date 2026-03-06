<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const flash = computed(() => page.props.flash || {});

const show = ref(true);

watch(flash, () => {
    show.value = true;
});

const activeMessage = computed(() => {
    if (!show.value) return null;
    
    // Order of precedence
    if (flash.value.error || page.props.error) return { type: 'danger', icon: 'error', bg: 'bg-red-600 text-white border-red-700 dark:bg-red-900 dark:text-red-100 dark:border-red-800', msg: flash.value.error || page.props.error };
    if (flash.value.danger) return { type: 'danger', icon: 'error', bg: 'bg-red-600 text-white border-red-700 dark:bg-red-900 dark:text-red-100 dark:border-red-800', msg: flash.value.danger };
    if (flash.value.warning) return { type: 'warning', icon: 'warning', bg: 'bg-amber-600 text-white border-amber-700 dark:bg-amber-900 dark:text-amber-100 dark:border-amber-800', msg: flash.value.warning };
    if (flash.value.success) return { type: 'success', icon: 'check_circle', bg: 'bg-green-600 text-white border-green-700 dark:bg-green-900 dark:text-green-100 dark:border-green-800', msg: flash.value.success };
    if (flash.value.info) return { type: 'info', icon: 'info', bg: 'bg-blue-600 text-white border-blue-700 dark:bg-blue-900 dark:text-blue-100 dark:border-blue-800', msg: flash.value.info };
    if (flash.value.status || page.props.status) return { type: 'status', icon: 'info', bg: 'bg-blue-600 text-white border-blue-700 dark:bg-blue-900 dark:text-blue-100 dark:border-blue-800', msg: flash.value.status || page.props.status };

    return null;
});

const close = () => {
    show.value = false;
};
</script>

<template>
    <div v-if="activeMessage" class="max-w-[1400px] mx-auto mb-4 px-0 container">
        <div :class="['rounded-lg shadow flex items-center gap-3 px-3 py-2.5 transition-all duration-300', activeMessage.bg]">
            <div class="flex-shrink-0 flex items-center">
                <span class="material-symbols-outlined">{{ activeMessage.icon }}</span>
            </div>
            <div class="flex-1 w-full">
                <p class="text-sm font-medium leading-tight">{{ activeMessage.msg }}</p>
            </div>
            <div class="flex-shrink-0 ml-2">
                <button @click="close" class="inline-flex rounded-md p-1 focus:outline-none transition-colors opacity-70 hover:opacity-100 hover:bg-black/10">
                    <span class="sr-only">Dismiss</span>
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>
        </div>
    </div>
</template>
