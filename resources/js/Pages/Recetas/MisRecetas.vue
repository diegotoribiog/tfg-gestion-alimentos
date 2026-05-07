<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import axios from "axios";

const props = defineProps({
    recetas: Array,
});

const recetaSeleccionada = ref(null);
const showModal = ref(false);
const ordenReciente = ref('desc'); // 'desc' para nuevas, 'asc' para antiguas

// Lógica de organización Senior
const favoritas = computed(() => props.recetas.filter(r => r.es_favorito));
const historialReciente = computed(() => {
    return [...props.recetas].sort((a, b) => {
        const dateA = new Date(a.created_at);
        const dateB = new Date(b.created_at);
        return ordenReciente.value === 'desc' ? dateB - dateA : dateA - dateB;
    });
});

const toggleOrden = () => {
    ordenReciente.value = ordenReciente.value === 'desc' ? 'asc' : 'desc';
};

const verReceta = (receta) => {
    recetaSeleccionada.value = receta;
    showModal.value = true;
};

const toggleFavorito = async (receta) => {
    try {
        const response = await axios.post(route('recetas.favorito', receta.id));
        receta.es_favorito = response.data.es_favorito;
        router.reload({ preserveScroll: true });
    } catch (e) {
        alert("Error al actualizar favorito");
    }
};

const eliminar = (id) => {
    if (confirm("¿Eliminar esta receta definitivamente?")) {
        router.delete(route('recetas.destroy', id), {
            onSuccess: () => {
                showModal.value = false;
                recetaSeleccionada.value = null;
            }
        });
    }
};

const cocinarYDescontar = async () => {
    if (!recetaSeleccionada.value) return;
    if (!confirm("¿Descontar ingredientes del stock actual?")) return;
    
    try {
        await axios.post(route('recetas.cocinar'), {
            ingredientes: recetaSeleccionada.value.cuerpo.ingredientes_usados
        });
        alert("¡Buen provecho! Stock actualizado.");
        showModal.value = false;
        router.reload();
    } catch (e) {
        alert("Error técnico al actualizar el stock.");
    }
};
</script>

<template>
    <Head title="Mis Recetas" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-6 space-y-16 pb-24">
            
            <!-- SECCIÓN: FAVORITAS (DISEÑO DESTACADO) -->
            <section class="space-y-8">
                <div class="flex items-center gap-4">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter uppercase flex items-center gap-3">
                        <span class="p-2 bg-rose-500 rounded-xl text-white shadow-lg shadow-rose-500/20 dark:shadow-none">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
                        </span>
                        Recetas Favoritas
                    </h2>
                    <div class="h-px bg-slate-200/60 dark:bg-slate-700/50 flex-1"></div>
                </div>

                <div v-if="favoritas.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div 
                        v-for="r in favoritas" :key="r.id"
                        class="bg-white dark:bg-midnight-card backdrop-blur-sm rounded-[2.5rem] border border-rose-100/50 dark:border-rose-900/30 overflow-hidden group hover:shadow-2xl hover:shadow-rose-500/10 dark:hover:shadow-none transition-all cursor-pointer flex flex-col h-full relative"
                        @click="verReceta(r)"
                    >
                        <!-- (contenido de la tarjeta de favorita igual) -->
                        <div class="p-8 space-y-6 flex-1">
                            <div class="flex items-center justify-between">
                                <span class="px-3 py-1 bg-rose-50 dark:bg-rose-500/10 text-rose-500 rounded-full text-[8px] font-black uppercase tracking-[0.2em] border border-rose-100/50 dark:border-rose-500/20">Receta Guardada</span>
                                <button @click.stop="toggleFavorito(r)" class="text-rose-500 hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                </button>
                            </div>
                            <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight leading-tight group-hover:text-emerald-500 transition-colors">{{ r.titulo }}</h3>
                            <div class="flex items-center gap-3 text-[10px] font-black uppercase text-slate-400">
                                <span class="px-3 py-1 bg-slate-100/50 dark:bg-midnight rounded-lg">{{ r.cuerpo.tiempo }}</span>
                                <span class="px-3 py-1 bg-slate-100/50 dark:bg-midnight rounded-lg">{{ r.cuerpo.dificultad }}</span>
                            </div>
                        </div>
                        <div class="p-8 pt-0 mt-auto">
                            <button class="w-full py-4 bg-emerald-500 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-lg shadow-emerald-500/20 dark:shadow-none group-hover:bg-emerald-600 transition-colors">
                                Preparar de Nuevo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Placeholder para Favoritas Vacío -->
                <div v-else class="py-16 bg-white dark:bg-midnight-card rounded-[2.5rem] border-2 border-dashed border-slate-200 dark:border-slate-700/50 flex flex-col items-center justify-center text-center space-y-4">
                    <div class="w-16 h-16 bg-white dark:bg-midnight/30 rounded-2xl flex items-center justify-center text-rose-300 dark:text-rose-900/50 shadow-sm border border-slate-100 dark:border-slate-800">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">Tu recetario personal</p>
                        <p class="text-xs font-bold text-slate-400 mt-1">Marca con un corazón tus recetas favoritas para guardarlas aquí.</p>
                    </div>
                </div>
            </section>

            <!-- SECCIÓN: HISTORIAL (LISTADO CRONOLÓGICO) -->
            <section class="space-y-8">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter uppercase flex items-center gap-3">
                            <span class="p-2 bg-slate-100 dark:bg-midnight-card rounded-xl text-slate-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </span>
                            Historial Reciente
                        </h2>
                        
                        <!-- BOTÓN DE ORDENACIÓN -->
                        <button 
                            @click="toggleOrden"
                            class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-midnight-card backdrop-blur-sm border border-slate-200 dark:border-slate-700 rounded-xl hover:border-emerald-vibrant transition-all group"
                        >
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-emerald-vibrant">
                                {{ ordenReciente === 'desc' ? 'Más nuevas' : 'Más antiguas' }}
                            </span>
                            <svg 
                                class="w-3 h-3 text-slate-300 transition-transform duration-300" 
                                :class="{ 'rotate-180': ordenReciente === 'asc' }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="h-px bg-slate-200/60 dark:bg-slate-700/50 flex-1"></div>
                </div>

                <div v-if="historialReciente.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div 
                        v-for="r in historialReciente" :key="r.id"
                        class="bg-white dark:bg-midnight-card backdrop-blur-sm rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/50 overflow-hidden group hover:border-emerald-vibrant/50 transition-all cursor-pointer flex flex-col h-full"
                        @click="verReceta(r)"
                    >
                        <div class="p-8 space-y-5 flex-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ new Date(r.created_at).toLocaleDateString() }}</span>
                                <button @click.stop="toggleFavorito(r)" class="text-slate-200 dark:text-slate-700 hover:text-rose-500 hover:scale-110 transition-all">
                                    <svg class="w-5 h-5" :fill="r.es_favorito ? 'currentColor' : 'none'" :class="{ 'text-rose-500': r.es_favorito }" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                </button>
                            </div>
                            <h3 class="text-lg font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight group-hover:text-emerald-vibrant transition-colors">{{ r.titulo }}</h3>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="(ing, i) in r.cuerpo.ingredientes_usados.slice(0, 2)" :key="i" class="text-[9px] font-bold text-slate-400 uppercase">
                                    #{{ ing.nombre }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else-if="favoritas.length === 0" class="py-32 flex flex-col items-center justify-center text-center space-y-4 opacity-40">
                    <div class="w-20 h-20 bg-slate-100 dark:bg-midnight-card rounded-3xl flex items-center justify-center text-slate-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <p class="text-xs font-black uppercase tracking-widest">No hay recetas en el historial</p>
                </div>
            </section>
        </div>

        <!-- MODAL DE DETALLE (DISEÑO MINIMALISTA) -->
        <Modal :show="showModal" @close="showModal = false" maxWidth="xl">
            <div v-if="recetaSeleccionada" class="bg-white dark:bg-midnight rounded-[2rem] overflow-hidden shadow-2xl border border-slate-200/60 dark:border-slate-800/50">
                <!-- Header Minimalista -->
                <div class="p-8 pb-4 text-center space-y-2">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <span class="px-3 py-1 bg-slate-100 dark:bg-midnight-card text-slate-500 dark:text-slate-400 text-[9px] font-black uppercase tracking-widest rounded-full">Receta Guardada</span>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter uppercase leading-tight">{{ recetaSeleccionada.titulo }}</h2>
                    <div class="flex items-center justify-center gap-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <span>{{ recetaSeleccionada.cuerpo.tiempo }}</span>
                        <span class="w-1 h-1 bg-slate-200 dark:bg-slate-800 rounded-full"></span>
                        <span>{{ recetaSeleccionada.cuerpo.dificultad }}</span>
                    </div>
                </div>

                <div class="px-8 py-6 space-y-10 max-h-[65vh] overflow-y-auto custom-scrollbar">
                    <!-- Ingredientes -->
                    <div class="space-y-4">
                        <h3 class="text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.4em] text-center mb-6">Ingredientes Usados</h3>
                        <div class="space-y-2">
                            <!-- Ingredientes del Stock -->
                            <template v-for="(ing, i) in recetaSeleccionada.cuerpo.ingredientes_usados" :key="i">
                                <div v-if="ing.nombre && ing.nombre !== '-'" class="flex items-center justify-between py-3 border-b border-slate-100 dark:border-slate-700/50">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-bold uppercase tracking-tight" :class="ing.es_basico ? 'text-slate-400' : 'text-emerald-vibrant'">
                                            {{ ing.nombre }}
                                        </span>
                                        <span class="text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md" :class="ing.es_basico ? 'bg-slate-50 dark:bg-midnight/50 text-slate-400' : 'bg-emerald-50 dark:bg-emerald-vibrant/10 text-emerald-vibrant'">
                                            {{ ing.es_basico ? 'Básicos' : 'Stock' }}
                                        </span>
                                    </div>
                                    <span class="text-xs font-black text-slate-900 dark:text-white uppercase">{{ ing.cantidad_valor }} {{ ing.unidad }}</span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Ingredientes Extras (Ingredientes_extras) -->
                    <div v-if="recetaSeleccionada.cuerpo.ingredientes_extras?.length > 0" class="space-y-4">
                        <h3 class="text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.4em] text-center mb-6">Ingredientes Extras</h3>
                        <div class="space-y-2">
                            <template v-for="(ing, i) in recetaSeleccionada.cuerpo.ingredientes_extras" :key="i">
                                <div v-if="ing.nombre && ing.nombre !== '-'" class="flex items-center justify-between py-3 border-b border-slate-100 dark:border-slate-700/50 italic">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-tight">
                                            {{ ing.nombre }}
                                        </span>
                                        <span class="text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md bg-slate-50 dark:bg-midnight/50 text-slate-300">
                                            Sugerencia
                                        </span>
                                    </div>
                                    <span class="text-xs font-black text-slate-400 uppercase">{{ ing.cantidad }}</span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Preparación -->
                    <div class="space-y-6 pt-4">
                        <h3 class="text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.4em] text-center mb-8">Preparación</h3>
                        <div class="space-y-8">
                            <div v-for="(paso, i) in recetaSeleccionada.cuerpo.pasos" :key="i" class="flex flex-col items-center text-center gap-3">
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

                <!-- Acciones -->
                <div class="p-8 bg-slate-50/50 dark:bg-midnight/40 border-t border-slate-100 dark:border-slate-800 flex flex-col items-center gap-6">
                    <div class="flex items-center justify-center gap-4 w-full">
                        <button 
                            @click="toggleFavorito(recetaSeleccionada)"
                            class="p-4 rounded-2xl bg-white dark:bg-midnight-card border border-slate-200 dark:border-slate-700 text-rose-500 transition-all active:scale-95 shadow-sm"
                        >
                            <svg class="w-5 h-5" :fill="recetaSeleccionada.es_favorito ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        </button>
                        <button 
                            @click="cocinarYDescontar"
                            class="flex-1 bg-slate-900 dark:bg-emerald-vibrant text-white py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-xl shadow-slate-200/50 dark:shadow-none transition-all active:scale-95"
                        >
                            Cocinar de Nuevo
                        </button>
                        <button 
                            @click="eliminar(recetaSeleccionada.id)"
                            class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20 text-rose-500 transition-all active:scale-95"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                    <button @click="showModal = false" class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest hover:text-slate-500 transition-colors">
                        Cerrar Archivo
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; }
</style>
