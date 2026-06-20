<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    options: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: 'Select tags...',
    },
    searchPlaceholder: {
        type: String,
        default: 'Search tags...',
    },
    allowCreate: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const search = ref('');
const dropdownRef = ref(null);

const filteredOptions = computed(() => {
    if (!search.value) return props.options;
    return props.options.filter(option =>
        option.name.toLowerCase().includes(search.value.toLowerCase())
    );
});

const selectedOptionsObjects = computed(() => {
    return props.modelValue.map(value => {
        if (typeof value === 'object') return value;
        const existing = props.options.find(opt => opt.slug === value || opt.id === value);
        return existing || { name: value, slug: value };
    });
});

const canCreateNewTag = computed(() => {
    if (!props.allowCreate || !search.value.trim()) return false;
    return !filteredOptions.value.some(opt =>
        opt.name.toLowerCase() === search.value.toLowerCase()
    );
});

const toggle = (option) => {
    const newValue = [...props.modelValue];
    const identifier = option.slug || option.id;
    const index = newValue.findIndex(val =>
        (typeof val === 'object' ? val.slug || val.id : val) === identifier
    );

    if (index === -1) {
        newValue.push(identifier);
    } else {
        newValue.splice(index, 1);
    }
    emit('update:modelValue', newValue);
};

const createNewTag = () => {
    if (search.value.trim()) {
        const newTag = search.value.trim().toLowerCase().replace(/\s+/g, '-');
        const newValue = [...props.modelValue];
        if (!newValue.includes(newTag)) {
            newValue.push(newTag);
            emit('update:modelValue', newValue);
        }
        search.value = '';
    }
};

const remove = (value) => {
    const identifier = typeof value === 'object' ? (value.slug || value.id) : value;
    const newValue = props.modelValue.filter(val =>
        (typeof val === 'object' ? val.slug || val.id : val) !== identifier
    );
    emit('update:modelValue', newValue);
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
        <div class="min-h-12 w-full rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 p-2 transition-colors cursor-text"
            @click="isOpen = true">
            <div class="flex flex-wrap gap-2 items-center">
                <span v-for="tag in selectedOptionsObjects" :key="tag.id"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-primary/10 text-primary text-sm font-medium border border-primary/20">
                    {{ tag.name }}
                    <button type="button" @click.stop="remove(tag.id)"
                        class="hover:text-primary/70 transition-colors focus:outline-none">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                    </button>
                </span>

                <span v-if="selectedOptionsObjects.length === 0" class="text-slate-400 py-1 px-2 text-base">
                    {{ placeholder }}
                </span>
            </div>
        </div>

        <!-- Dropdown Panel -->
        <div v-if="isOpen"
            class="absolute z-50 mt-1 w-full rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-lg max-h-64 flex flex-col">
            <!-- Search -->
            <div class="p-2 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <div class="relative">
                    <span
                        class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                    <input v-model="search" type="text" :placeholder="searchPlaceholder"
                        class="w-full pl-9 pr-3 py-2 text-sm rounded-md border-none bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-primary/50"
                        @click.stop />
                </div>
            </div>

            <!-- Options -->
            <div class="overflow-y-auto overflow-x-hidden p-1 flex-1">
                <button v-for="option in filteredOptions" :key="option.id" type="button"
                    class="flex w-full items-center gap-2 px-3 py-2 text-sm rounded-md transition-colors"
                    :class="modelValue.includes(option.slug || option.id) ? 'bg-primary/5 text-primary font-medium' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'"
                    @click.stop="toggle(option)">
                    <div class="flex items-center justify-center size-4 border rounded shrink-0 transition-colors"
                        :class="modelValue.includes(option.slug || option.id) ? 'bg-primary border-primary text-white' : 'border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800'">
                        <span v-if="modelValue.includes(option.slug || option.id)"
                            class="material-symbols-outlined text-[12px] font-bold">check</span>
                    </div>
                    <span class="truncate">{{ option.name }}</span>
                </button>

                <button v-if="canCreateNewTag" type="button"
                    class="flex w-full items-center gap-2 px-3 py-2 text-sm rounded-md transition-colors text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 border-t border-slate-100 dark:border-slate-700 mt-1"
                    @click.stop="createNewTag">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    <span>Create "<strong>{{ search }}</strong>"</span>
                </button>

                <div v-if="filteredOptions.length === 0 && !canCreateNewTag" class="px-3 py-4 text-sm text-slate-400 text-center">
                    No results found
                </div>
            </div>
        </div>
    </div>
</template>
