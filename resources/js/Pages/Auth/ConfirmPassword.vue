<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Confirmar Contraseña" />

    <div class="relative flex min-h-screen bg-white">
        
        <div class="hidden lg:flex w-1/2 bg-slate-900 relative overflow-hidden items-center justify-center">
            <img 
                src="https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                alt="Seguridad" 
                class="absolute inset-0 w-full h-full object-cover opacity-30"
            />
            
            <div class="relative z-10 flex flex-col items-center text-center px-12 text-white">
                <h1 class="text-7xl font-extrabold tracking-tighter mb-6">
                    Zona<br/><span class="text-emerald-400">Segura</span>
                </h1>
                <p class="text-xl text-gray-300 max-w-md leading-relaxed">
                    Estás entrando en una sección crítica. Por favor, confirma tu identidad para proteger tu despensa.
                </p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center px-8 md:px-24 bg-gray-50">
            <div class="max-w-md w-full mx-auto">
                
                <div class="lg:hidden mb-12 text-center">
                    <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight">mi<span class="text-emerald-500">despensa</span></h2>
                </div>

                <div class="mb-8 text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-gray-900">Confirmar acceso</h2>
                    <p class="mt-4 text-sm text-gray-600 leading-relaxed">
                        Esta es una zona de seguridad. Por favor, introduce tu contraseña de midespensa para continuar.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="password" value="Tu contraseña" class="text-gray-600 ml-1" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            autofocus
                            placeholder="••••••••"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="pt-2">
                        <PrimaryButton
                            class="w-full justify-center bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-xl font-bold transition-all transform active:scale-[0.98] shadow-lg shadow-emerald-200 uppercase tracking-widest"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Confirmar acceso
                        </PrimaryButton>
                    </div>
                </form>

                <div class="mt-16 text-center text-[10px] text-gray-400 uppercase tracking-widest font-semibold">
                    © 2026 midespensa · Seguridad y Privacidad
                </div>
            </div>
        </div>
    </div>
</template>
