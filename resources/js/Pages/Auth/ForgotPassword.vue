<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

// Recibimos el 'status' para confirmar que el email se ha enviado correctamente
defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Recuperar contraseña" />

    <div class="relative flex min-h-screen bg-white">
        
        <div class="hidden lg:flex w-1/2 bg-slate-900 relative overflow-hidden items-center justify-center">
            <img 
                src="https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                alt="Despensa organizada" 
                class="absolute inset-0 w-full h-full object-cover opacity-30"
            />
            
            <div class="relative z-10 flex flex-col items-center text-center px-12 text-white">
                <h1 class="text-7xl font-extrabold tracking-tighter mb-6">
                    No pasa<br/><span class="text-emerald-400">nada</span>
                </h1>
                <p class="text-xl text-gray-300 max-w-md leading-relaxed">
                    A todos se nos olvida algo de vez en cuando. 
                    Te ayudamos a recuperar el acceso a MiDespensa.
                </p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center px-8 md:px-24 bg-gray-50">
            <div class="max-w-md w-full mx-auto">
                
                <div class="lg:hidden mb-12 text-center">
                    <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight">Mi<span class="text-emerald-500">Despensa</span></h2>
                </div>

                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 text-center lg:text-left">¿Olvidaste tu clave?</h2>
                    <p class="mt-4 text-sm text-gray-600 leading-relaxed text-center lg:text-left">
                        Introduce tu correo electrónico y te enviaremos un enlace para que puedas elegir una nueva contraseña y volver a organizar tu inventario.
                    </p>
                </div>

                <div v-if="status" class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 font-medium text-sm text-emerald-700 shadow-sm">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="email" value="Correo electrónico" class="text-gray-600 ml-1" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full bg-white border-gray-200 rounded-xl py-3 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                            v-model="form.email"
                            required
                            autofocus
                            placeholder="nombre@ejemplo.com"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="flex flex-col gap-4 pt-2">
                        <PrimaryButton
                            class="w-full justify-center bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-xl font-bold transition-all transform active:scale-[0.98] shadow-lg shadow-emerald-200 uppercase tracking-widest"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Enviar enlace de recuperación
                        </PrimaryButton>

                        <Link :href="route('login')" class="text-center text-sm text-gray-400 hover:text-gray-600 transition font-medium">
                            Volver al inicio de sesión
                        </Link>
                    </div>
                </form>

                <div class="mt-16 text-center text-[10px] text-gray-400 uppercase tracking-widest font-semibold">
                    © 2026 MiDespensa · Recuperación Segura
                </div>
            </div>
        </div>
    </div>
</template>