<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import { computePosition, offset, flip, shift } from '@floating-ui/dom';

const props = defineProps({
    modelValue: [String, Number],
    options: {
        type: Array,
        required: true,
    },
    placeholder: {
        type: String,
        default: 'Seleccionar...',
    },
    labelKey: {
        type: String,
        default: 'nombre',
    },
    valueKey: {
        type: String,
        default: 'id',
    },
    class: {
        type: String,
        default: '',
    }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const containerRef = ref(null);
const menuRef = ref(null);

const toggle = () => isOpen.value = !isOpen.value;
const close = () => isOpen.value = false;

const selectOption = (option) => {
    const val = typeof option === 'object' ? option[props.valueKey] : option;
    emit('update:modelValue', val);
    close();
};

const selectedLabel = computed(() => {
    if (props.modelValue === undefined || props.modelValue === null || props.modelValue === '') {
        return props.placeholder;
    }
    const option = props.options.find(opt => {
        const val = typeof opt === 'object' ? opt[props.valueKey] : opt;
        return String(val) === String(props.modelValue);
    });
    if (!option) return props.placeholder;
    return typeof option === 'object' ? option[props.labelKey] : option;
});

// --- POSICIONAMIENTO (COPIADO DE DATEPICKER) ---
const updatePosition = () => {
    if (!isOpen.value || !containerRef.value || !menuRef.value) return;
    computePosition(containerRef.value, menuRef.value, {
        placement: 'bottom-start',
        middleware: [offset(8), flip(), shift({ padding: 5 })],
    }).then(({ x, y }) => {
        Object.assign(menuRef.value.style, {
            left: `${x}px`,
            top: `${y}px`,
        });
    });
};

watch(isOpen, async (val) => {
    if (val) {
        await nextTick();
        updatePosition();
    }
});

const handleClickOutside = (e) => {
    if (isOpen.value && !containerRef.value.contains(e.target) && !menuRef.value?.contains(e.target)) {
        close();
    }
};

onMounted(() => {
    window.addEventListener('click', handleClickOutside);
    window.addEventListener('resize', updatePosition);
});

onUnmounted(() => {
    window.removeEventListener('click', handleClickOutside);
    window.removeEventListener('resize', updatePosition);
});
</script>

<template>
    <div class="w-full relative" ref="containerRef">
        <!-- Trigger -->
        <button
            type="button"
            @click.stop="toggle"
            class="w-full h-[44px] bg-white dark:bg-midnight/50 border border-slate-200 dark:border-slate-700 rounded-2xl px-4 flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-slate-300 transition-all shadow-sm group"
            :class="{ 'border-emerald-vibrant ring-2 ring-emerald-vibrant/20': isOpen }"
        >
            <span :class="{ 'text-slate-400 dark:text-slate-700': !modelValue && modelValue !== 0 }">{{ selectedLabel }}</span>
            <svg class="w-4 h-4 text-slate-400 transition-transform duration-300" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Menu Popover Anclado (Teleportado a body, igual que DatePicker) -->
        <Teleport to="body">
            <div
                v-if="isOpen"
                ref="menuRef"
                class="absolute z-[9999] bg-white dark:bg-midnight-card rounded-[2.5rem] shadow-2xl border border-slate-100 dark:border-slate-800 p-4 animate-in fade-in zoom-in duration-200"
                :style="{ width: containerRef?.offsetWidth + 'px' }"
                @click.stop
            >
                <div class="max-h-60 overflow-y-auto custom-scrollbar space-y-1">
                    <button
                        v-for="opt in options"
                        :key="typeof opt === 'object' ? opt[valueKey] : opt"
                        type="button"
                        @click="selectOption(opt)"
                        class="w-full px-5 py-3 text-left text-[10px] font-black uppercase tracking-widest transition-all rounded-xl"
                        :class="[
                            (typeof opt === 'object' ? opt[valueKey] : opt) === modelValue
                                ? 'bg-emerald-vibrant text-white shadow-lg'
                                : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-50 dark:hover:bg-emerald-vibrant/10 hover:text-emerald-vibrant'
                        ]"
                    >
                        {{ typeof opt === 'object' ? opt[labelKey] : opt }}
                    </button>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style scoped>
</style>
