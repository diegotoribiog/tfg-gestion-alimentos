<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

// Recibimos el 'status' para saber si acabamos de reenviar el correo de verificación
const props = defineProps({
    status: String,
});

const form = useForm({});

// Función para solicitar un nuevo enlace de verificación
const submit = () => {
    form.post(route('verification.send'));
};

// Comprobamos si el mensaje de estado indica que el correo se envió con éxito
const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head title="Verificar Correo" />

    <div class="relative flex min-h-screen bg-white">
        
        <div class="hidden lg:flex w-1/2 bg-slate-900 relative overflow-hidden items-center justify-center">
            <img 
                src="https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                alt="Verificación" 
                class="absolute inset-0 w-full h-full object-cover opacity-30"
            />
            
            <div class="relative z-10 flex flex-col items-center text-center px-12 text-white">
                <h1 class="text-7xl font-extrabold tracking-tighter mb-6">
                    Casi<br/><span class="text-emerald-400">Listo</span>
                </h1>
                <p class="text-xl text-gray-300 max-w-md leading-relaxed">
                    Solo un paso más para empezar a organizar tu despensa de forma inteligente.
                </p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center px-8 md:px-24 bg-gray-50">
            <div class="max-w-md w-full mx-auto">
                
                <div class="lg:hidden mb-12 text-center">
                    <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight">Mi<span class="text-emerald-500">Despensa</span></h2>
                </div>

                <div class="mb-8 text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-gray-900">Verifica tu cuenta</h2>
                    <p class="mt-4 text-sm text-gray-600 leading-relaxed">
                        ¡Gracias por registrarte! Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar? Si no lo recibiste, te enviaremos otro con gusto.
                    </p>
                </div>

                <div v-if="verificationLinkSent" class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 font-medium text-sm text-emerald-700 shadow-sm text-center">
                    Se ha enviado un nuevo enlace de verificación a la dirección de correo que proporcionaste durante el registro.
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="flex flex-col gap-4 items-center lg:items-start">
                        <PrimaryButton
                            class="w-full justify-center bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-xl font-bold transition-all transform active:scale-[0.98] shadow-lg shadow-emerald-200 uppercase tracking-widest"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Reenviar correo de verificación
                        </PrimaryButton>

                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="text-sm text-gray-400 hover:text-emerald-600 underline transition underline-offset-4"
                        >
                            Cerrar sesión
                        </Link>
                    </div>
                </form>

                <div class="mt-16 text-center text-[10px] text-gray-400 uppercase tracking-widest font-semibold">
                    © 2026 MiDespensa · Activación de Cuenta
                </div>
            </div>
        </div>
    </div>
</template>