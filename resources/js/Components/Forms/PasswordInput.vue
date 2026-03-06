<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    error: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Enter your password',
    },
    required: {
        type: Boolean,
        default: false,
    },
    autofocus: {
        type: Boolean,
        default: false,
    }
});

defineEmits(['update:modelValue']);

const input = ref(null);
const showPassword = ref(false);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div class="relative flex items-center">
        <input
            ref="input"
            :type="showPassword ? 'text' : 'password'"
            class="form-input block w-full rounded-lg border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-primary focus:ring-primary h-12 px-4 !pr-12 text-base transition-colors"
            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': error }"
            :value="modelValue"
            :placeholder="placeholder"
            :required="required"
            :autofocus="autofocus"
            @input="$emit('update:modelValue', $event.target.value)"
        >
        <!-- Visibility Toggle -->
        <button 
            type="button" 
            @click="showPassword = !showPassword" 
            class="absolute right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none flex items-center"
            tabindex="-1"
        >
            <span class="material-symbols-outlined text-[20px]">{{ showPassword ? 'visibility' : 'visibility_off' }}</span>
        </button>
    </div>
    <div v-if="error" class="mt-1 text-sm text-red-600 dark:text-red-400">
        {{ error }}
    </div>
</template>
