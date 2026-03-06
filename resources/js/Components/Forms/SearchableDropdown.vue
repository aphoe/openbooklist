<script setup>
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: {
        type: [Number, String, null],
        default: null,
    },
    options: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: 'Select...',
    },
    searchPlaceholder: {
        type: String,
        default: 'Search...',
    },
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const search = ref('');
const dropdownRef = ref(null);

const filteredOptions = computed(() => {
    if (!search.value) {
        return props.options;
    }

    return props.options.filter(option =>
        option.name.toLowerCase().includes(search.value.toLowerCase())
    );
});

const selectedOption = computed(() => {
    return props.options.find(option => option.id === props.modelValue);
});

const select = (option) => {
    emit('update:modelValue', option.id);
    isOpen.value = false;
    search.value = '';
};

const clear = () => {
    emit('update:modelValue', null);
    isOpen.value = false;
    search.value = '';
};

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isOpen.value = false;
        search.value = '';
    }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>

<template>
    <div ref="dropdownRef" class="relative">
        <!-- Trigger -->
        <button
            type="button"
            class="form-input flex w-full items-center justify-between rounded-lg border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white h-12 px-4 text-base transition-colors text-left"
            @click="isOpen = !isOpen"
        >
            <span :class="selectedOption ? 'text-slate-900 dark:text-white' : 'text-slate-400'">
                {{ selectedOption ? selectedOption.name : placeholder }}
            </span>
            <span class="material-symbols-outlined text-slate-400 text-xl transition-transform" :class="{ 'rotate-180': isOpen }">
                expand_more
            </span>
        </button>

        <!-- Dropdown Panel -->
        <div
            v-if="isOpen"
            class="absolute z-50 mt-1 w-full rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-lg max-h-64 overflow-hidden"
        >
            <!-- Search -->
            <div class="p-2 border-b border-slate-100 dark:border-slate-700">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                    <input
                        v-model="search"
                        type="text"
                        :placeholder="searchPlaceholder"
                        class="w-full pl-9 pr-3 py-2 text-sm rounded-md border-none bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-primary/50"
                        @click.stop
                    />
                </div>
            </div>

            <!-- Options -->
            <div class="overflow-y-auto max-h-48 p-1">
                <!-- Clear option -->
                <button
                    v-if="modelValue"
                    type="button"
                    class="flex w-full items-center gap-2 px-3 py-2 text-sm text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-md transition-colors"
                    @click="clear"
                >
                    <span class="material-symbols-outlined text-base">close</span>
                    Clear selection
                </button>

                <button
                    v-for="option in filteredOptions"
                    :key="option.id"
                    type="button"
                    class="flex w-full items-center gap-2 px-3 py-2 text-sm rounded-md transition-colors"
                    :class="option.id === modelValue
                        ? 'bg-primary/10 text-primary font-medium'
                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'"
                    @click="select(option)"
                >
                    {{ option.name }}
                    <span v-if="option.id === modelValue" class="material-symbols-outlined text-primary text-base ml-auto">check</span>
                </button>

                <div v-if="filteredOptions.length === 0" class="px-3 py-4 text-sm text-slate-400 text-center">
                    No results found
                </div>
            </div>
        </div>
    </div>
</template>
