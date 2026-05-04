<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
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
                alt="Despensa organizada" 
                class="absolute inset-0 w-full h-full object-cover opacity-30"
            />
            
            <div class="relative z-10 flex flex-col items-center text-center px-12 text-white">
                <h1 class="text-7xl font-extrabold tracking-tighter mb-6">
                    Seguridad<br/><span class="text-emerald-400">Primero</span>
                </h1>
                <p class="text-xl text-gray-300 max-w-md leading-relaxed">
                    Estás entrando en una zona protegida. 
                    Por favor, confirma que eres tú para continuar.
                </p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center px-8 md:px-24 bg-gray-50">
            <div class="max-w-md w-full mx-auto">
                
                <div class="lg:hidden mb-12 text-center">
                    <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight">Mi<span class="text-emerald-500">Despensa</span></h2>
                </div>

                <div class="mb-8 text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-gray-900">Área segura</h2>
                    <p class="mt-4 text-sm text-gray-600 leading-relaxed italic">
                        Esta es una zona de seguridad de tu cuenta. Por favor, confirma tu contraseña antes de continuar.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="password" value="Tu contraseña" class="text-gray-600 ml-1" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full bg-white border-gray-200 rounded-xl py-3 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
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
                    © 2026 MiDespensa · Protección de Datos
                </div>
            </div>
        </div>
    </div>
</template>