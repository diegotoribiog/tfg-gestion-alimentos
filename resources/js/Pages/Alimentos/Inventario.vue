<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Modal from "@/Components/Modal.vue";
import DatePicker from "@/Components/DatePicker.vue";

const props = defineProps({
    alimentos: Array,
    categorias: Array,
});

// --- CATÁLOGO COMPLETO ---
const catalogoFrecuente = [
    { nombre: 'Leche Entera', categoria: 'Lácteos y Huevos', unidad: 'ML' },
    { nombre: 'Leche Desnatada', categoria: 'Lácteos y Huevos', unidad: 'ML' },
    { nombre: 'Leche Semidesnatada', categoria: 'Lácteos y Huevos', unidad: 'ML' },
    { nombre: 'Leche de Avena', categoria: 'Lácteos y Huevos', unidad: 'ML' },
    { nombre: 'Huevos', categoria: 'Lácteos y Huevos', unidad: 'UD' },
    { nombre: 'Queso tierno', categoria: 'Lácteos y Huevos', unidad: 'GR' },
    { nombre: 'Queso curado', categoria: 'Lácteos y Huevos', unidad: 'GR' },
    { nombre: 'Queso rallado', categoria: 'Lácteos y Huevos', unidad: 'GR' },
    { nombre: 'Yogur Natural', categoria: 'Lácteos y Huevos', unidad: 'UD' },
    { nombre: 'Yogur Griego', categoria: 'Lácteos y Huevos', unidad: 'UD' },
    { nombre: 'Mantequilla', categoria: 'Lácteos y Huevos', unidad: 'GR' },
    { nombre: 'Nata Cocinar', categoria: 'Lácteos y Huevos', unidad: 'ML' },
    { nombre: 'Pechuga de Pollo', categoria: 'Carnes y Embutidos', unidad: 'GR' },
    { nombre: 'Muslos de Pollo', categoria: 'Carnes y Embutidos', unidad: 'GR' },
    { nombre: 'Alitas de Pollo', categoria: 'Carnes y Embutidos', unidad: 'GR' },
    { nombre: 'Lomo de Cerdo', categoria: 'Carnes y Embutidos', unidad: 'GR' },
    { nombre: 'Costillas de Cerdo', categoria: 'Carnes y Embutidos', unidad: 'GR' },
    { nombre: 'Solomillo de Cerdo', categoria: 'Carnes y Embutidos', unidad: 'GR' },
    { nombre: 'Filete de Ternera', categoria: 'Carnes y Embutidos', unidad: 'GR' },
    { nombre: 'Carne Picada Ternera', categoria: 'Carnes y Embutidos', unidad: 'GR' },
    { nombre: 'Carne Picada Mezcla', categoria: 'Carnes y Embutidos', unidad: 'GR' },
    { nombre: 'Chuletas de Cordero', categoria: 'Carnes y Embutidos', unidad: 'GR' },
    { nombre: 'Bacon', categoria: 'Carnes y Embutidos', unidad: 'GR' },
    { nombre: 'Jamón York', categoria: 'Carnes y Embutidos', unidad: 'GR' },
    { nombre: 'Jamón Serrano', categoria: 'Carnes y Embutidos', unidad: 'GR' },
    { nombre: 'Chorizo', categoria: 'Carnes y Embutidos', unidad: 'UD' },
    { nombre: 'Salchichón', categoria: 'Carnes y Embutidos', unidad: 'GR' },
    { nombre: 'Salchichas Frankfurt', categoria: 'Carnes y Embutidos', unidad: 'UD' },
    { nombre: 'Merluza', categoria: 'Pescados y Mariscos', unidad: 'GR' },
    { nombre: 'Bacalao', categoria: 'Pescados y Mariscos', unidad: 'GR' },
    { nombre: 'Congrio', categoria: 'Pescados y Mariscos', unidad: 'GR' },
    { nombre: 'Salmón', categoria: 'Pescados y Mariscos', unidad: 'GR' },
    { nombre: 'Dorada', categoria: 'Pescados y Mariscos', unidad: 'GR' },
    { nombre: 'Lubina', categoria: 'Pescados y Mariscos', unidad: 'GR' },
    { nombre: 'Atún en lata', categoria: 'Pescados y Mariscos', unidad: 'UD' },
    { nombre: 'Gambas', categoria: 'Pescados y Mariscos', unidad: 'GR' },
    { nombre: 'Langostinos', categoria: 'Pescados y Mariscos', unidad: 'GR' },
    { nombre: 'Mejillones', categoria: 'Pescados y Mariscos', unidad: 'GR' },
    { nombre: 'Calamares', categoria: 'Pescados y Mariscos', unidad: 'GR' },
    { nombre: 'Pulpo', categoria: 'Pescados y Mariscos', unidad: 'GR' },
    { nombre: 'Tomate', categoria: 'Frutas y Verduras', unidad: 'GR' },
    { nombre: 'Cebolla', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Ajo', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Patata', categoria: 'Frutas y Verduras', unidad: 'GR' },
    { nombre: 'Zanahoria', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Pimiento Rojo', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Pimiento Verde', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Calabacín', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Berenjena', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Brócoli', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Coliflor', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Lechuga', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Espinacas', categoria: 'Frutas y Verduras', unidad: 'GR' },
    { nombre: 'Pepino', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Judías Verdes', categoria: 'Frutas y Verduras', unidad: 'GR' },
    { nombre: 'Manzana Roja', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Manzana Verde', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Plátano', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Naranja', categoria: 'Frutas y Verduras', unidad: 'GR' },
    { nombre: 'Mandarina', categoria: 'Frutas y Verduras', unidad: 'GR' },
    { nombre: 'Pera', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Limón', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Fresa', categoria: 'Frutas y Verduras', unidad: 'GR' },
    { nombre: 'Uva', categoria: 'Frutas y Verduras', unidad: 'GR' },
    { nombre: 'Piña', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Kiwi', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Aguacate', categoria: 'Frutas y Verduras', unidad: 'UD' },
    { nombre: 'Garbanzos', categoria: 'Legumbres, Arroz y Pasta', unidad: 'GR' },
    { nombre: 'Lentejas', categoria: 'Legumbres, Arroz y Pasta', unidad: 'GR' },
    { nombre: 'Alubias Blancas', categoria: 'Legumbres, Arroz y Pasta', unidad: 'GR' },
    { nombre: 'Alubias Pintas', categoria: 'Legumbres, Arroz y Pasta', unidad: 'GR' },
    { nombre: 'Guisantes', categoria: 'Legumbres, Arroz y Pasta', unidad: 'GR' },
    { nombre: 'Habas', categoria: 'Legumbres, Arroz y Pasta', unidad: 'GR' },
    { nombre: 'Macarrones', categoria: 'Legumbres, Arroz y Pasta', unidad: 'GR' },
    { nombre: 'Espaguetis', categoria: 'Legumbres, Arroz y Pasta', unidad: 'GR' },
    { nombre: 'Tortellini', categoria: 'Legumbres, Arroz y Pasta', unidad: 'GR' },
    { nombre: 'Tallarines', categoria: 'Legumbres, Arroz y Pasta', unidad: 'GR' },
    { nombre: 'Fideos', categoria: 'Legumbres, Arroz y Pasta', unidad: 'GR' },
    { nombre: 'Lasaña (Placas)', categoria: 'Legumbres, Arroz y Pasta', unidad: 'UD' },
    { nombre: 'Arroz Blanco', categoria: 'Legumbres, Arroz y Pasta', unidad: 'GR' },
    { nombre: 'Arroz Integral', categoria: 'Legumbres, Arroz y Pasta', unidad: 'GR' },
    { nombre: 'Cuscús', categoria: 'Legumbres, Arroz y Pasta', unidad: 'GR' },
    { nombre: 'Pan de Molde', categoria: 'Panadería y Dulces', unidad: 'UD' },
    { nombre: 'Barra de Pan', categoria: 'Panadería y Dulces', unidad: 'UD' },
    { nombre: 'Galletas María', categoria: 'Panadería y Dulces', unidad: 'UD' },
    { nombre: 'Chocolate Negro', categoria: 'Panadería y Dulces', unidad: 'GR' }
];

// --- ESTADO DE UI ---
const mostrarPanelAdd = ref(false);
const categoriaFiltro = ref(null); 
const busquedaCatalogo = ref("");
const abiertoId = ref(null);
const alimentoParaAjustar = ref(null);
const busqueda = ref("");
const detallesSection = ref(null);
const ordenAsc = ref(true);

// --- NORMALIZACIÓN ---
const normalizar = (texto) => {
    if (!texto) return "";
    return texto.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
};

watch(busquedaCatalogo, (val) => {
    if (val.trim() !== "") {
        categoriaFiltro.value = "Todos";
    }
});

const toggleAcordeon = (id) => {
    if (abiertoId.value === id) {
        abiertoId.value = null;
    } else {
        abiertoId.value = id;
        const alimento = props.alimentos.find(a => a.id === id);
        if (alimento) {
            notasForm.notas = alimento.notas || "";
        }
    }
};

// --- LÓGICA DE CATÁLOGO ---
const categoriasCatalogo = computed(() => {
    const cats = [...new Set(catalogoFrecuente.map(i => i.categoria))].sort();
    return ["Todos", ...cats];
});

const itemsCatalogoFiltrados = computed(() => {
    if (!categoriaFiltro.value && !busquedaCatalogo.value) return [];
    let res = catalogoFrecuente;
    if (categoriaFiltro.value && categoriaFiltro.value !== "Todos") {
        res = res.filter(i => i.categoria === categoriaFiltro.value);
    }
    if (busquedaCatalogo.value) {
        res = res.filter(i => normalizar(i.nombre).includes(normalizar(busquedaCatalogo.value)));
    }
    return res.sort((a, b) => a.nombre.localeCompare(b.nombre));
});

const seleccionarItem = (item) => {
    form.nombre = item.nombre;
    form.unidad = item.unidad;
    const cat = props.categorias.find(c => c.nombre.toLowerCase() === item.categoria.toLowerCase());
    if (cat) {
        form.categoria_id = cat.id;
        categoriaFiltro.value = item.categoria; // Sincroniza el filtro visual
    }
    // Desplazamiento suave a la sección de detalles
    detallesSection.value?.scrollIntoView({ behavior: 'smooth', block: 'center' });
};

// --- GESTIÓN DE DATOS ---
const alimentosFiltrados = computed(() => {
    let res = props.alimentos.filter(a => 
        normalizar(a.nombre).includes(normalizar(busqueda.value)) ||
        normalizar(a.categoria?.nombre).includes(normalizar(busqueda.value))
    );
    return res.sort((a, b) => {
        const fechaA = new Date(a.fecha_caducidad);
        const fechaB = new Date(b.fecha_caducidad);
        return ordenAsc.value ? fechaA - fechaB : fechaB - fechaA;
    });
});

const toggleOrden = () => {
    ordenAsc.value = !ordenAsc.value;
};

const form = useForm({
    nombre: "",
    cantidad: 1,
    unidad: "UD",
    fecha_caducidad: "",
    categoria_id: "",
    notas: "",
});

const notasForm = useForm({
    notas: "",
});

// --- CAPITALIZACIÓN DE NOTAS ---
watch(() => form.notas, (val) => {
    if (val) {
        form.notas = val.replace(/(^|[.!?]\s+)([a-z])/g, (m, p1, p2) => p1 + p2.toUpperCase());
    }
});

watch(() => notasForm.notas, (val) => {
    if (val) {
        notasForm.notas = val.replace(/(^|[.!?]\s+)([a-z])/g, (m, p1, p2) => p1 + p2.toUpperCase());
    }
});

const submit = () => {
    form.post(route("alimentos.store"), { 
        onSuccess: () => {
            form.reset();
            mostrarPanelAdd.value = false;
        },
        preserveScroll: true 
    });
};

const guardarNotas = (id) => {
    notasForm.put(route("alimentos.update", id), {
        preserveScroll: true,
    });
};

const eliminar = (id) => confirm("¿Eliminar este producto?") && useForm({}).delete(route("alimentos.destroy", id), { preserveScroll: true });

// --- AJUSTE DE STOCK ---
const ajustarForm = useForm({ cantidad: 0 });
const abrirAjuste = (alimento) => { alimentoParaAjustar.value = alimento; ajustarForm.cantidad = 0; };
const confirmarAjuste = () => {
    ajustarForm.post(route("alimentos.ajustarStock", alimentoParaAjustar.value.id), {
        onSuccess: () => { alimentoParaAjustar.value = null; ajustarForm.reset(); },
        preserveScroll: true
    });
};

// --- UTILS ---
const getStatusCaducidad = (fecha) => {
    const hoy = new Date().setHours(0,0,0,0);
    const cad = new Date(fecha).setHours(0,0,0,0);
    const dif = Math.ceil((cad - hoy) / 86400000);
    if (dif < 0) return { label: 'Caducado', class: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30', dot: 'bg-rose-500' };
    if (dif === 0) return { label: 'Hoy', class: 'bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-100 dark:border-orange-900/30', dot: 'bg-orange-500' };
    if (dif === 1) return { label: 'En 1 día', class: 'bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-100 dark:border-orange-900/30', dot: 'bg-orange-500' };
    if (dif <= 3) return { label: `En ${dif} días`, class: 'bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-100 dark:border-orange-900/30', dot: 'bg-orange-500' };
    return { label: `Faltan ${dif} días`, class: 'bg-emerald-vibrant/10 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30', dot: 'bg-emerald-vibrant' };
};
</script>

<template>
    <Head title="Inventario" />

    <AuthenticatedLayout>
        <div class="max-w-6xl mx-auto space-y-8 pb-24">
            
            <!-- PANEL: AÑADIR A LA DESPENSA -->
            <section class="bg-white dark:bg-midnight-card backdrop-blur-md rounded-[2.5rem] shadow-sm border border-slate-200/60 dark:border-slate-700/50">
                <button 
                    @click="mostrarPanelAdd = !mostrarPanelAdd"
                    class="w-full flex items-center justify-between p-8"
                >
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-emerald-vibrant rounded-2xl text-white shadow-lg shadow-emerald-500/20 dark:shadow-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight uppercase">Añadir a la Despensa</h2>
                    </div>
                    <svg 
                        class="w-6 h-6 text-slate-300 dark:text-slate-600 transition-transform duration-300" 
                        :class="{ 'rotate-180': mostrarPanelAdd }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div v-show="mostrarPanelAdd" class="px-8 pb-10 space-y-10 animate-in slide-in-from-top duration-300">
                    
                    <!-- SELECCIÓN RÁPIDA (CATÁLOGO) -->
                    <div class="bg-slate-50/50 dark:bg-midnight/30 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-800/50">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                            <div>
                                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Selección Rápida</h3>
                                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-tight">Elige un producto común para autocompletar</p>
                            </div>
                            <div class="relative w-full md:w-80">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input v-model="busquedaCatalogo" type="text" placeholder="Buscar en catálogo..." class="w-full bg-white dark:bg-midnight/60 border border-slate-100 dark:border-slate-800/50 rounded-2xl py-3.5 pl-10 pr-4 text-xs font-black uppercase tracking-widest focus:ring-2 focus:ring-emerald-vibrant/20 shadow-sm dark:text-white dark:placeholder-slate-700" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
                            <button 
                                v-for="cat in categoriasCatalogo" :key="cat"
                                @click="categoriaFiltro = (categoriaFiltro === cat ? null : cat)"
                                :class="[
                                    categoriaFiltro === cat 
                                        ? 'bg-emerald-vibrant text-white shadow-xl shadow-emerald-500/20 dark:shadow-none scale-105'
                                        : 'bg-white dark:bg-midnight/60 text-slate-400 dark:text-slate-500 hover:text-emerald-vibrant dark:hover:text-emerald-vibrant border border-slate-200/60 dark:border-slate-800/50'
                                ]"
                                class="w-full h-12 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all active:scale-95 flex items-center justify-center text-center leading-tight px-2"
                            >
                                {{ cat }}
                            </button>
                        </div>

                        <div class="min-h-48">
                            <div v-if="itemsCatalogoFiltrados.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 max-h-72 overflow-y-auto pr-4 custom-scrollbar">
                                <button 
                                    v-for="item in itemsCatalogoFiltrados" :key="item.nombre"
                                    @click="seleccionarItem(item)"
                                    class="p-5 rounded-[1.5rem] bg-white dark:bg-midnight/60 border border-slate-200/60 dark:border-slate-800/50 hover:border-emerald-vibrant dark:hover:border-emerald-vibrant/50 hover:shadow-2xl hover:shadow-slate-200/50 dark:hover:shadow-none transition-all text-left group relative overflow-hidden"
                                >
                                    <div class="relative z-10">
                                        <span class="text-sm font-black text-slate-700 dark:text-slate-300 group-hover:text-emerald-vibrant block mb-2">{{ item.nombre }}</span>
                                        <div class="flex items-center gap-3">
                                            <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-vibrant bg-emerald-50 dark:bg-emerald-vibrant/10 px-2 py-0.5 rounded-lg uppercase tracking-widest">{{ item.unidad }}</span>
                                            <span class="text-[9px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-tight">{{ item.categoria }}</span>
                                        </div>
                                    </div>
                                </button>
                            </div>
                            <div v-else class="h-48 flex flex-col items-center justify-center text-slate-200 dark:text-slate-800 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-[2rem]">
                                <svg class="w-12 h-12 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-center px-8 text-slate-300 dark:text-slate-700">Explora las categorías o usa el buscador superior</p>
                            </div>
                        </div>
                    </div>

                    <!-- FORMULARIO DE DETALLES -->
                    <div ref="detallesSection" class="border-t border-slate-100 dark:border-slate-800/50 pt-8">
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-8">Confirmar Detalles</h3>
                        
                        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="md:col-span-2">
                                <InputLabel value="Nombre del Alimento" class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-400 mb-2" />
                                <TextInput v-model="form.nombre" type="text" class="w-full h-[44px] text-[10px] font-black uppercase tracking-widest px-4" placeholder="Ej: Manzana" required />
                                <InputError :message="form.errors.nombre" />
                            </div>
                            <div>
                                <InputLabel value="Cantidad" class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-400 mb-2" />
                                <div class="flex gap-2">
                                    <TextInput v-model="form.cantidad" type="number" step="1" class="w-full h-[44px] text-[10px] font-black uppercase tracking-widest px-4" required />
                                    <select v-model="form.unidad" class="border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-midnight/50 rounded-2xl text-[10px] font-black uppercase tracking-widest pl-4 pr-10 dark:text-slate-300 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors focus:ring-2 focus:ring-emerald-vibrant/20 h-[44px]">
                                        <option value="UD">UD</option>
                                        <option value="GR">GR</option>
                                        <option value="ML">ML</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <InputLabel value="Fecha de Caducidad" class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-400 mb-2" />
                                <DatePicker v-model="form.fecha_caducidad" />
                            </div>
                            <div class="md:col-span-2">
                                <InputLabel value="Categoría" class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-400 mb-2" />
                                <select v-model="form.categoria_id" class="w-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-midnight/50 rounded-2xl text-[10px] font-black uppercase tracking-widest h-[44px] pl-4 pr-10 dark:text-slate-300 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors focus:ring-2 focus:ring-emerald-vibrant/20" required>
                                    <option value="" disabled>Seleccionar...</option>
                                    <option v-for="c in categorias" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                                </select>
                                <InputError :message="form.errors.categoria_id" />
                            </div>
                            <div class="md:col-span-1"></div>
                            <div class="flex items-end">
                                <PrimaryButton class="w-full justify-center py-3.5 bg-slate-900 dark:bg-emerald-vibrant text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-slate-200/50 dark:shadow-none hover:bg-slate-800 dark:hover:bg-emerald-600 transition-colors" :disabled="form.processing">
                                    Añadir a Despensa
                                </PrimaryButton>
                            </div>
                            <div class="md:col-span-4">
                                <InputLabel value="Notas (Opcional)" class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-400 mb-2" />
                                <textarea v-model="form.notas" class="w-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-midnight/50 rounded-2xl text-sm font-medium p-4 focus:ring-2 focus:ring-emerald-vibrant/10 dark:text-slate-300 dark:placeholder-slate-700" rows="2" placeholder="Notas sobre el producto..."></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- INVENTARIO -->
            <div class="bg-white dark:bg-midnight-card backdrop-blur-md rounded-[2.5rem] shadow-sm border border-slate-200/60 dark:border-slate-700/50 overflow-hidden">
                <div class="p-8 md:p-10 border-b border-slate-100 dark:border-slate-800/50 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter uppercase">Mi Despensa</h3>
                    <div class="relative w-full md:w-96">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input v-model="busqueda" type="text" placeholder="¿Qué buscas hoy?" class="pl-10 w-full bg-slate-50/50 dark:bg-midnight/30 border border-slate-200 dark:border-slate-700 rounded-2xl py-3.5 text-xs font-black uppercase tracking-widest focus:ring-2 focus:ring-emerald-vibrant/20 dark:text-white dark:placeholder-slate-700" />
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400 dark:text-slate-500 bg-slate-50/50 dark:bg-midnight/20">
                                <th class="px-10 py-6">Producto</th>
                                <th class="px-10 py-6">Stock</th>
                                <th class="px-10 py-6 cursor-pointer hover:text-emerald-vibrant transition-colors" @click="toggleOrden">
                                    <div class="flex items-center gap-2">
                                        Caducidad
                                        <svg 
                                            class="w-3 h-3 transition-transform" 
                                            :class="{ 'rotate-180': !ordenAsc }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-10 py-6 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                            <template v-for="a in alimentosFiltrados" :key="a.id">
                                <tr @click="toggleAcordeon(a.id)" class="group hover:bg-slate-50/50 dark:hover:bg-midnight/20 transition-all cursor-pointer">
                                    <td class="px-10 py-8">
                                        <div class="flex items-center gap-4">
                                            <div class="w-2.5 h-2.5 rounded-full shrink-0 shadow-sm" :class="getStatusCaducidad(a.fecha_caducidad).dot"></div>
                                            <div class="flex flex-col">
                                                <span class="font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight">{{ a.nombre }}</span>
                                                <span class="text-[9px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest">{{ a.categoria?.nombre }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8">
                                        <span class="font-black text-lg text-slate-900 dark:text-slate-100">{{ Number(a.cantidad) }} <small class="text-[10px] opacity-40 uppercase tracking-widest">{{ a.unidad }}</small></span>
                                    </td>
                                    <td class="px-10 py-8">
                                        <span :class="getStatusCaducidad(a.fecha_caducidad).class" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] shadow-sm">
                                            {{ getStatusCaducidad(a.fecha_caducidad).label }}
                                        </span>
                                    </td>
                                    <td class="px-10 py-8 text-right" @click.stop>
                                        <div class="flex justify-end gap-2">
                                            <button @click="abrirAjuste(a)" title="Ajustar Stock" class="p-4 rounded-2xl bg-slate-50 dark:bg-midnight/50 text-slate-400 dark:text-slate-500 hover:text-emerald-vibrant dark:hover:text-emerald-vibrant transition-all border border-transparent hover:border-emerald-100 dark:hover:border-emerald-900">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>
                                            <button @click="eliminar(a.id)" class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/20 text-rose-300 hover:text-rose-500 transition-all border border-transparent hover:border-rose-100 dark:hover:border-rose-900">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="abiertoId === a.id" class="bg-slate-50/30 dark:bg-midnight/30">
                                    <td colspan="4" class="px-10 py-8 animate-in slide-in-from-top duration-200">
                                        <div class="max-w-3xl">
                                            <div class="flex items-center justify-between mb-4">
                                                <p class="text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.4em]">Notas y Descripción</p>
                                                <button 
                                                    @click="guardarNotas(a.id)" 
                                                    :disabled="notasForm.processing"
                                                    class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-vibrant hover:scale-105 transition-all flex items-center gap-2"
                                                >
                                                    <svg v-if="notasForm.recentlySuccessful" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    {{ notasForm.recentlySuccessful ? 'Guardado' : 'Guardar Notas' }}
                                                </button>
                                            </div>                                            
                                            <textarea 
                                                v-model="notasForm.notas" 
                                                class="w-full border border-slate-100 dark:border-slate-800 bg-white dark:bg-midnight/60 rounded-[1.5rem] text-sm font-medium p-6 focus:ring-2 focus:ring-emerald-vibrant/10 shadow-sm dark:text-slate-400 dark:placeholder-slate-800"
                                                rows="3"
                                                placeholder="Añade detalles del producto..."
                                            ></textarea>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <Modal :show="!!alimentoParaAjustar" @close="alimentoParaAjustar = null">
            <div class="p-10 bg-white dark:bg-midnight border border-slate-200/60 dark:border-slate-800/50 rounded-[2.5rem]">
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2 tracking-tighter uppercase">Ajustar Stock</h3>
                <p class="text-slate-400 font-bold text-[10px] mb-10 uppercase tracking-[0.4em]">{{ alimentoParaAjustar?.nombre }}</p>
                <div class="space-y-8">
                    <div>
                        <InputLabel value="Cantidad a sumar (+) o restar (-)" class="text-center text-[9px] font-black uppercase tracking-[0.3em] mb-4 text-slate-300 dark:text-slate-600" />
                        <div class="relative">
                            <TextInput 
                                v-model="ajustarForm.cantidad" 
                                type="number" 
                                step="any" 
                                class="w-full text-center text-6xl font-black py-12 rounded-[3rem] dark:bg-slate-950 dark:border-slate-800 dark:text-white focus:ring-emerald-vibrant/20 shadow-inner" 
                                placeholder="0" 
                                autofocus 
                                @keyup.enter="confirmarAjuste"
                            />
                            <div class="absolute inset-y-0 right-10 flex items-center pointer-events-none">
                                <span class="text-2xl font-black text-slate-200 dark:text-slate-800 uppercase tracking-widest">{{ alimentoParaAjustar?.unidad }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <SecondaryButton @click="alimentoParaAjustar = null" class="flex-1 justify-center py-5 rounded-[2rem] font-black text-[10px] uppercase tracking-widest dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">Cancelar</SecondaryButton>
                        <PrimaryButton @click="confirmarAjuste" class="flex-1 justify-center py-5 rounded-[2rem] bg-slate-900 dark:bg-emerald-vibrant text-white font-black text-[10px] uppercase tracking-widest shadow-2xl shadow-slate-200/50 dark:shadow-none" :disabled="ajustarForm.processing">
                            Confirmar
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; }

/* Ocultar flechas en inputs numéricos */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type=number] {
    -moz-appearance: textfield;
}
</style>
