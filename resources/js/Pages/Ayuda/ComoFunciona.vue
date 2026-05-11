<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const pasos = [
    {
        titulo: "TU DESPENSA SIEMPRE AL DÍA",
        descripcion: "Registra tus productos y olvídate de revisar el armario cada vez que vas a comprar. midespensa te avisará automáticamente cuando un alimento está cerca de su fecha de caducidad.",
        icono: "M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
    },
    {
        titulo: "RECETAS SIN ESFUERZO",
        descripcion: "¿No sabes qué comer? Nuestra IA analiza lo que ya tienes y te propone platos rápidos y sencillos, priorizando aquello que necesitas gastar pronto para que nada se eche a perder.",
        icono: "M13 10V3L4 14h7v7l9-11h-7z"
    },
    {
        titulo: "TÚ ELIGES QUÉ COCINAR",
        descripcion: "¿Te apetece algo con ese ingrediente especial? Selecciona hasta 3 productos de tu inventario y la IA diseñará una receta personalizada que los convierta en los protagonistas.",
        icono: "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
    },
    {
        titulo: "ACTUALIZACIÓN AUTOMÁTICA",
        descripcion: "Al confirmar que has cocinado una receta, el sistema descuenta automáticamente las cantidades de tu inventario. Control total de tu stock sin mover un dedo.",
        icono: "M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
    }
];
</script>

<template>
    <Head title="Ayuda" />

    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto space-y-16 pb-20">
            <!-- CABECERA -->
            <div class="text-center space-y-4">
                <h1 class="text-5xl font-black text-slate-900 dark:text-white tracking-tighter">¿Cómo funciona mi<span class="text-emerald-vibrant">despensa</span>?</h1>
                <p class="text-slate-500 dark:text-slate-400 text-lg font-medium max-w-2xl mx-auto">Tu asistente personal para saber qué tienes en casa y qué puedes cocinar hoy con ello, de forma fácil y automática.</p>
            </div>

            <!-- GRID DE PASOS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div v-for="(paso, index) in pasos" :key="index" class="bg-white dark:bg-midnight-card p-10 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-800 shadow-sm group transition-all">
                    <div class="w-14 h-14 bg-emerald-vibrant text-white rounded-2xl flex items-center justify-center mb-8 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="paso.icono" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight mb-4">{{ paso.titulo }}</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed font-medium">{{ paso.descripcion }}</p>
                </div>
            </div>

            <!-- SECCIÓN DE SEGURIDAD -->
            <div class="bg-rose-50 dark:bg-rose-500/5 p-10 rounded-[3rem] border border-rose-100 dark:border-rose-900/30">
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="w-20 h-20 bg-white dark:bg-midnight rounded-3xl flex items-center justify-center text-rose-500 shadow-sm shrink-0">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div class="space-y-2">
                        <h4 class="text-lg font-black text-rose-600 dark:text-rose-400 uppercase tracking-tight">RECORDATORIO DE SALUD</h4>
                        <p class="text-rose-700/70 dark:text-rose-400/60 text-sm font-medium leading-relaxed">
                            Por tu seguridad, el generador automático nunca incluirá productos caducados en sus propuestas. Si decides aprovechar algún alimento bajo tu propia responsabilidad, puedes hacerlo mediante la selección manual desde el inventario.
                        </p>
                    </div>
                </div>
            </div>

            <!-- BOTÓN DE ACCIÓN -->
            <div class="flex justify-center pt-8">
                <Link :href="route('alimentos.index')" class="bg-emerald-vibrant hover:bg-emerald-600 text-white px-12 py-5 rounded-2xl font-black uppercase tracking-widest text-xs shadow-2xl shadow-emerald-500/20 transition-all hover:scale-105 active:scale-95">
                    Empezar a Organizar
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
