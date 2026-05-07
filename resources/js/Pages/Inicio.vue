<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import axios from "axios";

const props = defineProps({
    alertaCaducidad: { type: Number, default: 0 },
    total: { type: Number, default: 0 },
    conteo: {
        type: Object,
        default: () => ({ bueno: 0, proximo: 0, caducado: 0 }),
    },
    productosUrgentes: { type: Array, default: () => [] },
});

// Estado para la receta IA
const loading = ref(false);
const showModal = ref(false);
const receta = ref(null);
const error = ref(null);

const generarReceta = async (alimentoId = null) => {
    loading.value = true;
    error.value = null;
    try {
        const response = await axios.post(route('recetas.ia'), {
            alimento_id: alimentoId
        });
        receta.value = response.data;
        showModal.value = true;
    } catch (e) {
        error.value = e.response?.data?.error || "Error al conectar con el chef virtual.";
        alert(error.value);
    } finally {
        loading.value = false;
    }
};

const generarRecetaCon = (alimento) => {
    generarReceta(alimento.id);
};

const toggleFavorito = async () => {
    if (!receta.value) return;
    try {
        const response = await axios.post(route('recetas.favorito', receta.value.id));
        receta.value.es_favorito = response.data.es_favorito;
    } catch (e) {
        alert("Error al actualizar favorito");
    }
};

const cocinarYDescontar = async () => {
    if (!receta.value) return;
    if (!confirm("¿Descontar ingredientes del stock actual?")) return;
    
    try {
        // Solo enviamos ingredientes que tienen ID (que existen en nuestro stock)
        const ingredientesParaDescontar = receta.value.cuerpo.ingredientes_usados
            .filter(ing => ing.id && !ing.es_basico)
            .map(ing => ({
                id: ing.id,
                cantidad_valor: parseFloat(ing.cantidad_valor)
            }));

        await axios.post(route('recetas.cocinar'), {
            ingredientes: ingredientesParaDescontar
        });
        
        alert("¡Buen provecho! Stock actualizado.");
        showModal.value = false;
        router.reload({ preserveScroll: true });
    } catch (e) {
        alert(e.response?.data?.error || "Error técnico al actualizar stock.");
    }
};

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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            
            <!-- FILA 1: MÉTRICAS COMPACTAS -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-midnight-card p-6 rounded-[2rem] border border-slate-200/60 dark:border-slate-700/50 flex items-center gap-6 shadow-sm group">
                    <div class="bg-emerald-vibrant/10 text-emerald-vibrant w-16 h-16 rounded-2xl flex items-center justify-center font-black text-3xl shadow-inner group-hover:scale-110 transition-transform">
                        {{ total }}
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400 dark:text-slate-500">Total</p>
                        <h4 class="text-slate-900 dark:text-white font-black text-xl uppercase tracking-tighter">Alimentos</h4>
                    </div>
                </div>

                <div class="bg-white dark:bg-midnight-card p-6 rounded-[2rem] border border-slate-200/60 dark:border-slate-700/50 flex items-center gap-6 shadow-sm group">
                    <div class="bg-rose-500/10 text-rose-600 dark:text-rose-400 w-16 h-16 rounded-2xl flex items-center justify-center font-black text-3xl shadow-inner group-hover:scale-110 transition-transform">
                        {{ alertaCaducidad }}
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400 dark:text-slate-500">Urgente</p>
                        <h4 class="text-slate-900 dark:text-white font-black text-xl uppercase tracking-tighter">Alertas</h4>
                    </div>
                </div>

                <Link :href="route('alimentos.index')" class="bg-slate-900 dark:bg-emerald-vibrant p-6 rounded-[2rem] flex flex-col items-center justify-center text-center group hover:scale-[1.02] transition-all shadow-xl shadow-slate-200/50 dark:shadow-none min-h-[120px]">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white group-hover:rotate-12 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </div>
                        <span class="text-white font-black text-sm uppercase tracking-widest">Gestionar</span>
                    </div>
                </Link>
            </div>

            <!-- SECCIÓN DE ESTADO Y LISTADO (REVERTIDO A DISEÑO ORIGINAL) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-stretch">
                
                <!-- GRÁFICA DE CONSERVACIÓN -->
                <div class="bg-white dark:bg-midnight-card p-10 md:p-12 rounded-[2.5rem] shadow-sm border border-slate-200/60 dark:border-slate-700/50 flex flex-col h-[490px]">
                    <div class="flex flex-col items-center flex-1 justify-center">
                        <h3 class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.5em] mb-10 text-center">Estado de tus Alimentos</h3>
                        
                        <div v-if="total > 0" class="w-full flex flex-col items-center gap-10">
                            <!-- Gráfico Circular -->
                            <div class="relative w-56 h-56 flex items-center justify-center flex-shrink-0">
                                <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="40" fill="none" class="stroke-slate-50 dark:stroke-slate-800/50" stroke-width="12" />
                                    <circle cx="50" cy="50" r="40" fill="none" class="stroke-rose-500" stroke-width="12" stroke-linecap="round" :stroke-dasharray="`${porcentajes.caducado * 2.51} 251.2`" pathLength="251.2" />
                                    <circle cx="50" cy="50" r="40" fill="none" class="stroke-orange-400" stroke-width="12" stroke-linecap="round" :stroke-dasharray="`${porcentajes.proximo * 2.51} 251.2`" :stroke-dashoffset="`-${porcentajes.caducado * 2.51}`" pathLength="251.2" />
                                    <circle cx="50" cy="50" r="40" fill="none" class="stroke-emerald-vibrant" stroke-width="12" stroke-linecap="round" :stroke-dasharray="`${porcentajes.bueno * 2.51} 251.2`" :stroke-dashoffset="`-${((porcentajes.caducado || 0) + (porcentajes.proximo || 0)) * 2.51}`" pathLength="251.2" />
                                </svg>
                                <div class="absolute flex flex-col items-center">
                                    <span class="text-4xl font-black text-slate-900 dark:text-white">{{ total }}</span>
                                    <span class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-widest">Total</span>
                                </div>
                            </div>

                            <!-- Leyenda Detallada -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full">
                                <div class="flex flex-col items-center p-5 rounded-2xl bg-slate-50/50 dark:bg-midnight/30 border border-slate-100 dark:border-slate-800/50">
                                    <span class="text-[10px] font-black uppercase text-emerald-vibrant tracking-widest mb-1">En Fecha</span>
                                    <p class="text-3xl font-black text-slate-900 dark:text-white leading-none">{{ conteo.bueno }}</p>
                                </div>
                                <div class="flex flex-col items-center p-5 rounded-2xl bg-slate-50/50 dark:bg-midnight/30 border border-slate-100 dark:border-slate-800/50">
                                    <span class="text-[10px] font-black uppercase text-orange-500 dark:text-orange-400 tracking-widest mb-1">Próximos</span>
                                    <p class="text-3xl font-black text-slate-900 dark:text-white leading-none">{{ conteo.proximo }}</p>
                                </div>
                                <div class="flex flex-col items-center p-5 rounded-2xl bg-slate-50/50 dark:bg-midnight/30 border border-slate-100 dark:border-slate-800/50">
                                    <span class="text-[10px] font-black uppercase text-rose-500 dark:text-rose-400 tracking-widest mb-1">Caducados</span>
                                    <p class="text-3xl font-black text-slate-900 dark:text-white leading-none">{{ conteo.caducado }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LISTA DE CONSUMO PRIORITARIO -->
                <div class="bg-white dark:bg-midnight-card p-10 md:p-12 rounded-[2.5rem] shadow-sm border border-slate-200/60 dark:border-slate-700/50 flex flex-col h-[490px]">
                    <h3 class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.5em] mb-12 text-center">Consumo Prioritario</h3>
                    
                    <div v-if="productosUrgentes.length > 0" class="space-y-4 overflow-y-auto flex-1 pr-1 custom-scrollbar">
                        <div v-for="a in productosUrgentes" :key="a.id" class="p-6 rounded-[2rem] bg-slate-50/50 dark:bg-midnight/30 border border-slate-100 dark:border-slate-800/50 hover:bg-white dark:hover:bg-midnight transition-all flex items-center justify-between group">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-black text-slate-900 dark:text-slate-100 uppercase text-xs tracking-tight mb-2 truncate group-hover:text-emerald-vibrant transition-colors">{{ a.nombre }}</h4>
                                <div class="flex items-center gap-2">
                                    <span :class="new Date(a.fecha_caducidad) < new Date() ? 'bg-rose-500' : 'bg-orange-400'" class="w-2 h-2 rounded-full"></span>
                                    <p class="text-[11px] tracking-widest uppercase font-black" :class="new Date(a.fecha_caducidad) < new Date() ? 'text-rose-600' : 'text-orange-500'">{{ getRelativo(a.fecha_caducidad) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 ml-4">
                                <button 
                                    @click="generarRecetaCon(a)" 
                                    :disabled="loading"
                                    title="Generar receta con este ingrediente"
                                    class="opacity-0 group-hover:opacity-100 p-3 rounded-xl bg-emerald-vibrant/10 text-emerald-vibrant hover:bg-emerald-vibrant hover:text-white transition-all shadow-sm border border-emerald-vibrant/20 disabled:opacity-30"
                                >
                                    <svg v-if="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <span v-else class="animate-spin block w-5 h-5 border-2 border-current border-t-transparent rounded-full"></span>
                                </button>
                                <button @click="eliminar(a.id)" class="opacity-0 group-hover:opacity-100 p-3 rounded-xl bg-white dark:bg-slate-700 text-slate-400 hover:text-rose-500 transition-all shadow-sm border border-slate-100 dark:border-slate-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FILA 3: BANNER RECETA -->
            <div class="bg-slate-900 dark:bg-midnight-card p-8 sm:p-12 rounded-[3rem] text-white shadow-2xl border border-white/5 flex flex-col md:flex-row items-center justify-between gap-8 relative overflow-hidden group backdrop-blur-sm">
                <div class="space-y-4 text-center md:text-left relative z-10">
                    <h2 class="text-3xl sm:text-4xl font-black tracking-tighter uppercase leading-none">¿Qué cocinamos hoy?</h2>
                    <p class="text-slate-400 text-sm sm:text-base font-medium max-w-xl leading-relaxed">Analizamos tu despensa priorizando los alimentos más próximos a caducar para generarte una receta deliciosa y evitar el desperdicio.</p>
                </div>
                <button 
                    @click="generarReceta" 
                    :disabled="loading"
                    class="bg-emerald-vibrant hover:bg-emerald-600 text-white px-10 py-5 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:scale-105 active:scale-95 transition-all disabled:opacity-50 flex items-center gap-3 shrink-0 relative z-10"
                >
                    <svg v-if="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span v-else class="animate-spin w-4 h-4 border-2 border-current border-t-transparent rounded-full"></span>
                    {{ loading ? 'Inspirándome...' : 'Generar Receta' }}
                </button>
            </div>
        </div>

        <!-- MODAL DE RECETA IA (DISEÑO MINIMALISTA) -->
        <Modal :show="showModal" @close="showModal = false" maxWidth="xl">
            <div class="bg-white dark:bg-midnight rounded-[2rem] overflow-hidden shadow-2xl border border-slate-200/60 dark:border-slate-800/50">
                <div class="p-8 pb-4 text-center space-y-2">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <span class="px-3 py-1 bg-emerald-vibrant/10 text-emerald-vibrant text-[9px] font-black uppercase tracking-widest rounded-full">Receta Recomendada</span>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter uppercase leading-tight">{{ receta?.titulo }}</h2>
                    <div class="flex items-center justify-center gap-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <span>{{ receta?.cuerpo.tiempo }}</span>
                        <span class="w-1 h-1 bg-slate-200 dark:bg-slate-800 rounded-full"></span>
                        <span>{{ receta?.cuerpo.dificultad }}</span>
                    </div>
                </div>

                <div class="px-8 py-6 space-y-10 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <div class="space-y-4">
                        <h3 class="text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.4em] text-center mb-6">Lista de Ingredientes</h3>
                        <div class="space-y-2">
                            <!-- Ingredientes que sí tenemos en la despensa -->
                            <template v-for="(ing, i) in receta?.cuerpo.ingredientes_usados.filter(i => !i.es_basico)" :key="'u-'+i">
                                <div v-if="ing.nombre && ing.nombre !== '-'" class="flex items-center justify-between py-3 border-b border-slate-200/60 dark:border-slate-800/50">
                                    <span class="text-xs font-bold text-emerald-vibrant uppercase tracking-tight">
                                        {{ ing.nombre }} - {{ ing.cantidad_valor }} {{ ing.unidad }}
                                    </span>
                                    <span class="text-[9px] font-black text-emerald-600/40 uppercase tracking-widest">Stock</span>
                                </div>
                            </template>

                            <!-- Condimentos Básicos (Staples) -->
                            <template v-for="(ing, i) in receta?.cuerpo.ingredientes_usados.filter(i => i.es_basico)" :key="'b-'+i">
                                <div v-if="ing.nombre && ing.nombre !== '-'" class="flex items-center justify-between py-3 border-b border-slate-200/60 dark:border-slate-800/50">
                                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-tight">
                                        {{ ing.nombre }} - {{ ing.cantidad_valor }} {{ ing.unidad }}
                                    </span>
                                    <span class="text-[9px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest">Básicos</span>
                                </div>
                            </template>

                            <!-- Ingredientes sugeridos (no están en la despensa) -->
                            <template v-for="(ing, i) in receta?.cuerpo.ingredientes_extras" :key="'f-'+i">
                                <div v-if="ing.nombre && ing.nombre !== '-'" class="flex items-center justify-between py-3 border-b border-slate-200/60 dark:border-slate-800/50 opacity-80">
                                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-tight italic">
                                        {{ ing.nombre }} - {{ ing.cantidad }} 
                                    </span>
                                    <span class="text-[9px] font-black text-slate-300 dark:text-slate-700 uppercase tracking-widest">Sugerencia</span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="space-y-6 pt-4">
                        <h3 class="text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.4em] text-center mb-6">Preparación</h3>
                        <div class="space-y-8">
                            <div v-for="(paso, i) in receta?.cuerpo.pasos" :key="i" class="flex flex-col items-center text-center gap-3">
                                <div class="w-6 h-6 rounded-full bg-slate-900 dark:bg-emerald-vibrant text-white flex items-center justify-center text-[10px] font-black shrink-0">
                                    {{ i + 1 }}
                                </div>
                                <p class="text-[13px] font-medium text-slate-600 dark:text-slate-400 leading-relaxed max-w-[280px]">
                                    {{ paso }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-slate-50/50 dark:bg-midnight/50 border-t border-slate-50 dark:border-slate-800 flex flex-col items-center gap-6">
                    <div class="flex items-center justify-center gap-4 w-full">
                        <button 
                            @click="toggleFavorito"
                            class="flex-1 flex items-center justify-center gap-2 py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] transition-all"
                            :class="receta?.es_favorito ? 'bg-rose-500 text-white shadow-lg shadow-rose-100' : 'bg-white dark:bg-midnight-card text-slate-400 border border-slate-100 dark:border-slate-700'"
                        >
                            <svg class="w-4 h-4" :fill="receta?.es_favorito ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            {{ receta?.es_favorito ? 'En Recetario' : 'Guardar' }}
                        </button>

                        <button 
                            @click="cocinarYDescontar"
                            class="flex-1 bg-slate-900 dark:bg-emerald-vibrant text-white py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-xl shadow-slate-200 dark:shadow-none transition-all active:scale-95"
                        >
                            Cocinar Ahora
                        </button>
                    </div>
                    <button @click="showModal = false" class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest hover:text-slate-500 transition-colors">
                        Cerrar Propuesta
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
