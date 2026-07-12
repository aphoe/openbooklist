<script setup>
import { computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';

const page = usePage();
const flash = computed(() => page.props.flash || {});

const activeMessage = computed(() => {
    // Order of precedence
    if (flash.value.error || page.props.error) return { type: 'error', msg: flash.value.error || page.props.error };
    if (flash.value.danger) return { type: 'error', msg: flash.value.danger };
    if (flash.value.warning) return { type: 'warning', msg: flash.value.warning };
    if (flash.value.success) return { type: 'success', msg: flash.value.success };
    if (flash.value.info) return { type: 'info', msg: flash.value.info };
    if (flash.value.status || page.props.status) return { type: 'info', msg: flash.value.status || page.props.status };

    return null;
});

watch(activeMessage, (message) => {
    if (!message) return;

    toast(message.msg, {
        type: message.type,
        theme: 'light',
        position: 'top-right',
        hideProgressBar: false,
        clearOnUrlChange: false,
    });
}, { immediate: true });
</script>

<template></template>
