<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';
import { computePosition, offset, flip, shift } from '@floating-ui/dom';

const props = defineProps({
    modelValue: String, // Formato YYYY-MM-DD
});

const emit = defineEmits(['update:modelValue']);

// --- ESTADO ---
const isOpen = ref(false);
const view = ref('days');
const viewDate = ref(new Date());
const inputValue = ref('');
const inputRef = ref(null);
const calendarRef = ref(null);
const inputContainerRef = ref(null);

const config = {
    months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    weekDays: ['Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa', 'Do'],
};

// --- SINCRONIZACIÓN ---
const syncFromModel = () => {
    if (props.modelValue) {
        const [y, m, d] = props.modelValue.split('-');
        const formatted = `${d}/${m}/${y}`;
        if (inputValue.value !== formatted) {
            inputValue.value = formatted;
            const date = new Date(y, m - 1, d);
            if (!isNaN(date)) viewDate.value = date;
        }
    } else if (inputValue.value.length === 10) {
        inputValue.value = '';
    }
};

watch(() => props.modelValue, syncFromModel, { immediate: true });

// --- LÓGICA DE MÁSCARA Y VALIDACIÓN ---
const handleInput = (e) => {
    let cursor = e.target.selectionStart;
    let val = e.target.value.replace(/\D/g, ''); // Solo números
    
    if (val.length > 8) val = val.substring(0, 8);

    // Validación de segmentos
    let d = val.substring(0, 2);
    let m = val.substring(2, 4);
    let y = val.substring(4, 8);

    if (d.length === 2 && parseInt(d) > 31) d = '31';
    if (d.length === 2 && parseInt(d) === 0) d = '01';
    if (m.length === 2 && parseInt(m) > 12) m = '12';
    if (m.length === 2 && parseInt(m) === 0) m = '01';

    let masked = d;
    if (val.length > 2) masked += '/' + m;
    if (val.length > 4) masked += '/' + y;

    inputValue.value = masked;

    // Reposicionar cursor si es necesario (manejo básico)
    nextTick(() => {
        if (cursor === 3 || cursor === 6) cursor++;
        e.target.setSelectionRange(cursor, cursor);
    });

    // Emitir si está completo y es válido
    if (masked.length === 10) {
        const iso = `${y}-${m}-${d}`;
        const date = new Date(iso);
        if (!isNaN(date.getTime())) {
            emit('update:modelValue', iso);
            viewDate.value = date;
        }
    } else {
        emit('update:modelValue', ''); // Reset model if incomplete
    }
};

// --- GUÍA VISUAL (DD/MM/YYYY) ---
const guideValue = computed(() => {
    const placeholder = "DD/MM/YYYY";
    return inputValue.value + placeholder.substring(inputValue.value.length);
});

// --- LÓGICA DE CALENDARIO (DÍAS) ---
const days = computed(() => {
    const y = viewDate.value.getFullYear();
    const m = viewDate.value.getMonth();
    const firstDay = new Date(y, m, 1);
    const lastDay = new Date(y, m + 1, 0);
    
    let startOffset = firstDay.getDay() - 1;
    if (startOffset === -1) startOffset = 6;

    const res = [];
    const prevLastDay = new Date(y, m, 0).getDate();
    
    for (let i = startOffset; i > 0; i--) {
        res.push({ day: prevLastDay - i + 1, month: m - 1, year: y, current: false });
    }
    for (let i = 1; i <= lastDay.getDate(); i++) {
        res.push({ day: i, month: m, year: y, current: true });
    }
    const remaining = 42 - res.length;
    for (let i = 1; i <= remaining; i++) {
        res.push({ day: i, month: m + 1, year: y, current: false });
    }
    return res;
});

const years = computed(() => {
    const startYear = Math.floor(viewDate.value.getFullYear() / 10) * 10;
    return Array.from({ length: 12 }, (_, i) => startYear + i - 1);
});

const selectDate = (dateObj) => {
    const y = dateObj.year;
    const m = String(dateObj.month + 1).padStart(2, '0');
    const d = String(dateObj.day).padStart(2, '0');
    const formatted = `${y}-${m}-${d}`;
    emit('update:modelValue', formatted);
    isOpen.value = false;
};

const selectMonth = (idx) => {
    viewDate.value = new Date(viewDate.value.getFullYear(), idx, 1);
    view.value = 'days';
};

const selectYear = (y) => {
    viewDate.value = new Date(y, viewDate.value.getMonth(), 1);
    view.value = 'months';
};

const changeMonth = (offsetVal) => {
    viewDate.value = new Date(viewDate.value.getFullYear(), viewDate.value.getMonth() + offsetVal, 1);
};

// --- POSICIONAMIENTO (ANCLADO) ---
const updatePosition = () => {
    if (!isOpen.value || !inputContainerRef.value || !calendarRef.value) return;
    computePosition(inputContainerRef.value, calendarRef.value, {
        placement: 'bottom-start',
        middleware: [offset(8), flip(), shift({ padding: 5 })],
    }).then(({ x, y }) => {
        Object.assign(calendarRef.value.style, {
            left: `${x}px`,
            top: `${y}px`,
        });
    });
};

watch(isOpen, async (val) => {
    if (val) {
        view.value = 'days';
        await nextTick();
        updatePosition();
    }
});

const close = (e) => {
    if (isOpen.value && !inputContainerRef.value.contains(e.target) && !calendarRef.value?.contains(e.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener('click', close);
    window.addEventListener('resize', updatePosition);
});

onUnmounted(() => {
    window.removeEventListener('click', close);
    window.removeEventListener('resize', updatePosition);
});
</script>

<template>
    <div class="w-full relative" ref="inputContainerRef">
        <!-- Input con Máscara y Guía Visual -->
        <div 
            class="w-full h-[44px] bg-slate-50 dark:bg-midnight/50 rounded-2xl px-4 flex items-center justify-between border border-slate-200 dark:border-slate-700 focus-within:ring-2 focus-within:ring-emerald-vibrant/20 transition-all shadow-sm group"
        >
            <div class="relative flex-1 h-full flex items-center">
                <!-- Capa de Guía (DD/MM/YYYY) -->
                <span class="absolute inset-0 flex items-center text-[10px] font-black uppercase tracking-widest text-slate-300 dark:text-slate-700 pointer-events-none select-none">
                    {{ guideValue }}
                </span>
                <!-- Input Real -->
                <input 
                    ref="inputRef"
                    type="text"
                    :value="inputValue"
                    @input="handleInput"
                    maxlength="10"
                    class="absolute inset-0 bg-transparent border-none p-0 focus:ring-0 text-[10px] font-black uppercase tracking-widest w-full text-slate-900 dark:text-slate-300 z-10"
                />
            </div>
            
            <button 
                type="button"
                @click.stop="isOpen = !isOpen"
                class="relative z-20 shrink-0 ml-2 p-1 hover:bg-emerald-50 dark:hover:bg-emerald-vibrant/10 rounded-lg transition-colors"
            >
                <svg class="w-5 h-5 text-emerald-vibrant" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </button>
        </div>

        <!-- Calendario Popover Anclado (Teleportado a body) -->
        <Teleport to="body">
            <div 
                v-if="isOpen"
                ref="calendarRef"
                class="absolute z-[9999] w-[320px] bg-white dark:bg-midnight-card rounded-[2.5rem] shadow-2xl border border-slate-100 dark:border-slate-800 p-8 animate-in fade-in zoom-in duration-200"
            >
                <!-- CABECERA NAVEGACIÓN -->
                <div class="flex items-center justify-between mb-8">
                    <button @click="changeMonth(-1)" class="p-3 hover:bg-slate-50 dark:hover:bg-midnight rounded-2xl text-slate-400 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    
                    <div class="flex gap-2">
                        <button @click="view = 'months'" class="text-[10px] font-black uppercase tracking-widest text-emerald-vibrant hover:bg-emerald-50 dark:hover:bg-emerald-vibrant/10 px-3 py-1 rounded-xl transition-all">
                            {{ config.months[viewDate.getMonth()] }}
                        </button>
                        <button @click="view = 'years'" class="text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-midnight px-3 py-1 rounded-xl transition-all">
                            {{ viewDate.getFullYear() }}
                        </button>
                    </div>

                    <button @click="changeMonth(1)" class="p-3 hover:bg-slate-50 dark:hover:bg-midnight rounded-2xl text-slate-400 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                <!-- VISTA DÍAS -->
                <div v-if="view === 'days'">
                    <div class="grid grid-cols-7 gap-1 mb-4">
                        <div v-for="d in config.weekDays" :key="d" class="text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase text-center py-2">{{ d }}</div>
                    </div>
                    <div class="grid grid-cols-7 gap-2">
                        <button 
                            v-for="(d, i) in days" :key="i"
                            @click="selectDate(d)"
                            :class="[
                                !d.current ? 'text-slate-200 dark:text-slate-700' : 'text-slate-600 dark:text-slate-300',
                                props.modelValue === `${d.year}-${String(d.month + 1).padStart(2, '0')}-${String(d.day).padStart(2, '0')}` 
                                    ? 'bg-emerald-vibrant text-white shadow-xl shadow-emerald-500/20 dark:shadow-none font-bold scale-110' 
                                    : 'hover:bg-emerald-50 dark:hover:bg-emerald-vibrant/10'
                            ]"
                            class="h-10 w-10 rounded-2xl text-[10px] font-black flex flex-col items-center justify-center transition-all relative"
                        >
                            <span>{{ d.day }}</span>
                            <!-- Punto indicador para el día de hoy -->
                            <span 
                                v-if="d.day === new Date().getDate() && d.month === new Date().getMonth() && d.year === new Date().getFullYear()"
                                class="absolute bottom-1.5 w-1 h-1 rounded-full"
                                :class="props.modelValue === `${d.year}-${String(d.month + 1).padStart(2, '0')}-${String(d.day).padStart(2, '0')}` ? 'bg-white' : 'bg-emerald-vibrant'"
                            ></span>
                        </button>
                    </div>
                </div>

                <!-- VISTA MESES -->
                <div v-if="view === 'months'" class="grid grid-cols-3 gap-4 animate-in slide-in-from-bottom-2 duration-300">
                    <button 
                        v-for="(m, i) in config.months" :key="m"
                        @click="selectMonth(i)"
                        class="py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-vibrant/10 hover:text-emerald-vibrant transition-all"
                        :class="{ 'bg-emerald-vibrant text-white shadow-lg': viewDate.getMonth() === i }"
                    >
                        {{ m.substring(0, 3) }}
                    </button>
                </div>

                <!-- VISTA AÑOS -->
                <div v-if="view === 'years'" class="grid grid-cols-3 gap-4 animate-in slide-in-from-bottom-2 duration-300">
                    <button 
                        v-for="y in years" :key="y"
                        @click="selectYear(y)"
                        class="py-4 rounded-2xl text-[10px] font-black text-slate-600 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-vibrant/10 hover:text-emerald-vibrant transition-all"
                        :class="{ 'bg-emerald-vibrant text-white shadow-lg': viewDate.getFullYear() === y }"
                    >
                        {{ y }}
                    </button>
                </div>

                <!-- FOOTER -->
                <div class="mt-8 pt-6 border-t border-slate-50 dark:border-slate-800 flex justify-center">
                    <button 
                        @click="selectDate({ day: new Date().getDate(), month: new Date().getMonth(), year: new Date().getFullYear() })"
                        class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 hover:text-emerald-vibrant transition-all"
                    >
                        Hoy
                    </button>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style scoped>
/* Evitar que el input nativo muestre el calendario del sistema si se cambia el type */
input::-webkit-calendar-picker-indicator {
    display: none;
}
</style>
