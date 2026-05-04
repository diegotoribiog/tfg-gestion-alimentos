<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    alertaCaducidad: { type: Number, default: 0 },
    total: { type: Number, default: 0 },
    conteo: {
        type: Object,
        default: () => ({ bueno: 0, proximo: 0, caducado: 0 }),
    },
    productosUrgentes: { type: Array, default: () => [] },
});

// Porcentajes para la visualización del estado
const porcentajes = computed(() => {
    const b = Number(props.conteo?.bueno) || 0;
    const p = Number(props.conteo?.proximo) || 0;
    const c = Number(props.conteo?.caducado) || 0;
    const sumaTotal = b + p + c;
    if (sumaTotal === 0) return { bueno: 0, proximo: 0, caducado: 0 };
    return {
        bueno: (b / sumaTotal) * 100,
        proximo: (p / sumaTotal) * 100,
        caducado: (c / sumaTotal) * 100,
    };
});

const getRelativo = (fecha) => {
    const hoy = new Date().setHours(0,0,0,0);
    const cad = new Date(fecha).setHours(0,0,0,0);
    const dif = Math.round((cad - hoy) / 86400000);
    if (dif < 0) return "Producto caducado";
    if (dif === 0) return "Caduca hoy";
    if (dif === 1) return "Caduca en 1 día";
    return `Caduca en ${dif} días`;
};

const eliminar = (id) => confirm("¿Deseas eliminar este producto?") && router.delete(route("alimentos.destroy", id));
</script>

<template>
    <Head title="Inicio" />

    <AuthenticatedLayout>
        <div class="space-y-12 pb-24 max-w-7xl mx-auto px-6">
            
            <!-- SECCIÓN DE MÉTRICAS PRINCIPALES -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Inventario -->
                <div class="bg-white dark:bg-slate-900 p-10 lg:p-12 rounded-[2.5rem] border border-slate-50 dark:border-slate-800 flex flex-col items-center justify-center text-center shadow-sm transition-all h-full group">
                    <div class="bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 w-20 h-20 sm:w-24 sm:h-24 rounded-3xl flex items-center justify-center font-black text-3xl sm:text-4xl shadow-inner mb-6">
                        {{ total }}
                    </div>
                    <div class="space-y-1">
                        <p class="text-slate-400 dark:text-slate-500 text-[11px] font-black uppercase tracking-[0.4em]">Inventario</p>
                        <h4 class="text-slate-900 dark:text-white font-black text-xl sm:text-2xl uppercase tracking-tighter leading-none">Alimentos</h4>
                    </div>
                </div>

                <!-- Urgencia -->
                <div class="bg-white dark:bg-slate-900 p-10 lg:p-12 rounded-[2.5rem] border border-slate-50 dark:border-slate-800 flex flex-col items-center justify-center text-center shadow-sm transition-all h-full group">
                    <div class="bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 w-20 h-20 sm:w-24 sm:h-24 rounded-3xl flex items-center justify-center font-black text-3xl sm:text-4xl shadow-inner mb-6">
                        {{ alertaCaducidad }}
                    </div>
                    <div class="space-y-1">
                        <p class="text-slate-400 dark:text-slate-500 text-[11px] font-black uppercase tracking-[0.4em]">Urgencia</p>
                        <h4 class="text-slate-900 dark:text-white font-black text-xl sm:text-2xl uppercase tracking-tighter leading-none">Alertas</h4>
                    </div>
                </div>

                <!-- Navegación Refinada (Sólida/Premium) -->
                <Link :href="route('alimentos.index')" class="bg-slate-950 dark:bg-emerald-600 p-10 lg:p-12 rounded-[2.5rem] flex flex-col items-center justify-center text-center group hover:scale-[1.02] transition-all shadow-2xl shadow-slate-200 dark:shadow-none h-full sm:col-span-2 lg:col-span-1 min-h-[220px]">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-white/10 flex items-center justify-center mb-6 transition-transform group-hover:rotate-12 group-hover:scale-110">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </div>
                    <div class="space-y-1">
                        <p class="text-white/50 text-[11px] font-black uppercase tracking-[0.4em]">Despensa</p>
                        <h4 class="text-white font-black text-xl sm:text-2xl uppercase tracking-tighter leading-none">Gestionar</h4>
                    </div>
                </Link>
            </div>

            <!-- SECCIÓN DE ESTADO Y LISTADO PRIORITARIO -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-stretch">
                
                <!-- GRÁFICA DE CONSERVACIÓN -->
                <div class="bg-white dark:bg-slate-900 p-10 md:p-12 rounded-[2.5rem] shadow-sm border border-slate-50 dark:border-slate-800 flex flex-col h-[490px]">
                    <div class="flex flex-col items-center flex-1 justify-center">
                        <h3 class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.5em] mb-10 text-center">Estado de tus Alimentos</h3>
                        
                        <div v-if="total > 0" class="w-full flex flex-col items-center gap-10">
                            <!-- Gráfico Circular -->
                            <div class="relative w-56 h-56 flex items-center justify-center flex-shrink-0">
                                <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="40" fill="none" class="stroke-slate-100 dark:stroke-slate-800" stroke-width="12" />
                                    <circle cx="50" cy="50" r="40" fill="none" class="stroke-rose-500" stroke-width="12" stroke-linecap="round" :stroke-dasharray="`${porcentajes.caducado * 2.51} 251.2`" pathLength="251.2" />
                                    <circle cx="50" cy="50" r="40" fill="none" class="stroke-orange-400" stroke-width="12" stroke-linecap="round" :stroke-dasharray="`${porcentajes.proximo * 2.51} 251.2`" :stroke-dashoffset="`-${porcentajes.caducado * 2.51}`" pathLength="251.2" />
                                    <circle cx="50" cy="50" r="40" fill="none" class="stroke-emerald-500" stroke-width="12" stroke-linecap="round" :stroke-dasharray="`${porcentajes.bueno * 2.51} 251.2`" :stroke-dashoffset="`-${(porcentajes.caducado + porcentajes.proximo) * 2.51}`" pathLength="251.2" />
                                </svg>
                                <div class="absolute flex flex-col items-center">
                                    <span class="text-4xl font-black text-slate-900 dark:text-white">{{ total }}</span>
                                    <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Total</span>
                                </div>
                            </div>

                            <!-- Leyenda Detallada Mejorada -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full">
                                <div class="flex flex-col items-center p-5 rounded-2xl bg-emerald-50/50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-900/20">
                                    <span class="text-[10px] font-black uppercase text-emerald-700 dark:text-emerald-400 tracking-widest mb-1">En Fecha</span>
                                    <p class="text-3xl font-black text-slate-900 dark:text-white leading-none">{{ conteo.bueno }}</p>
                                </div>

                                <div class="flex flex-col items-center p-5 rounded-2xl bg-orange-50/50 dark:bg-orange-900/10 border border-orange-100 dark:border-orange-900/20">
                                    <span class="text-[10px] font-black uppercase text-orange-700 dark:text-orange-400 tracking-widest mb-1">Próximos</span>
                                    <p class="text-3xl font-black text-slate-900 dark:text-white leading-none">{{ conteo.proximo }}</p>
                                </div>

                                <div class="flex flex-col items-center p-5 rounded-2xl bg-rose-50/50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/20">
                                    <span class="text-[10px] font-black uppercase text-rose-700 dark:text-rose-400 tracking-widest mb-1">Caducados</span>
                                    <p class="text-3xl font-black text-slate-900 dark:text-white leading-none">{{ conteo.caducado }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center text-slate-300 dark:text-slate-700 font-black uppercase tracking-[0.4em] text-xs">
                            No hay registros disponibles
                        </div>
                    </div>
                </div>

                <!-- LISTA DE CONSUMO PRIORITARIO -->
                <div class="bg-white dark:bg-slate-900 p-10 md:p-12 rounded-[2.5rem] shadow-sm border border-slate-50 dark:border-slate-800 flex flex-col h-[490px]">
                    <h3 class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.5em] mb-12 text-center">Consumo Prioritario</h3>
                    
                    <div v-if="productosUrgentes.length > 0" class="space-y-4 overflow-y-auto flex-1 no-scrollbar pr-1">
                        <div v-for="a in productosUrgentes" :key="a.id" class="p-6 rounded-[2rem] bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 hover:bg-white dark:hover:bg-slate-800 transition-all flex items-center justify-between group">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-black text-slate-900 dark:text-white uppercase text-xs tracking-tight mb-2 truncate group-hover:text-emerald-600 transition-colors">
                                    {{ a.nombre }}
                                </h4>
                                <div class="flex items-center gap-2">
                                    <span :class="[
                                        new Date(a.fecha_caducidad).setHours(0,0,0,0) < new Date().setHours(0,0,0,0) ? 'bg-rose-500 animate-pulse' : 
                                        'bg-orange-400 animate-pulse'
                                    ]" class="w-2 h-2 rounded-full shrink-0"></span>
                                    <p :class="[
                                        new Date(a.fecha_caducidad).setHours(0,0,0,0) < new Date().setHours(0,0,0,0) ? 'text-rose-600 dark:text-rose-400 font-black' : 
                                        'text-orange-500 dark:text-orange-400 font-black'
                                    ]" class="text-[11px] tracking-widest uppercase truncate">
                                        {{ getRelativo(a.fecha_caducidad) }}
                                    </p>
                                </div>
                            </div>
                            
                            <button @click="eliminar(a.id)" class="opacity-0 group-hover:opacity-100 p-3 rounded-xl bg-white dark:bg-slate-700 text-slate-400 hover:text-rose-500 transition-all ml-4 shrink-0 shadow-sm border border-slate-100 dark:border-slate-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div v-else class="flex flex-col items-center justify-center py-24 opacity-10">
                        <svg class="w-20 h-20 text-slate-400 mb-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-[11px] font-black uppercase tracking-[0.4em]">Todo en orden</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scroll::-webkit-scrollbar { width: 5px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 20px; }
.dark .custom-scroll::-webkit-scrollbar-thumb { background: #1e293b; }

/* Ocultar barra de scroll pero mantener funcionalidad */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>
