<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
// Importación de componentes reutilizables de Breeze para mantener la lógica de Laravel
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

// Definición de las propiedades (props) que recibe la vista desde el controlador de Laravel
defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    status: String,
});

// Inicialización del formulario reactivo con el hook useForm de Inertia
const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// Función para enviar los datos de inicio de sesión al servidor
const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'), // Si falla, borra solo la contraseña por seguridad
    });
};
</script>

<template>
    <Head title="Bienvenido a MiDespensa" />

    <div class="relative flex min-h-screen bg-white">
        
        <div class="hidden lg:flex w-1/2 bg-slate-900 relative overflow-hidden items-center justify-center">
            <img 
                src="https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                alt="Despensa organizada" 
                class="absolute inset-0 w-full h-full object-cover opacity-30"
            />
            
            <div class="relative z-10 flex flex-col items-center text-center px-12 text-white">
                <h1 class="text-7xl font-extrabold tracking-tighter mb-6">
                    Mi<span class="text-emerald-400">Despensa</span>
                </h1>
                <p class="text-xl text-gray-300 max-w-md leading-relaxed mb-10">
                    Gestiona tu inventario de comida de forma inteligente. 
                    Evita el desperdicio, ahorra dinero y ten siempre el control.
                </p>
                
                <div class="flex items-center gap-12 bg-white/5 backdrop-blur-sm p-8 rounded-2xl border border-white/10">
                    <div class="flex flex-col">
                        <span class="text-4xl font-bold text-emerald-400">0%</span>
                        <span class="text-xs uppercase tracking-widest text-gray-400 mt-1">Desperdicio</span>
                    </div>
                    <div class="w-px h-10 bg-gray-700"></div> 
                    <div class="flex flex-col">
                        <span class="text-4xl font-bold text-emerald-400">100%</span>
                        <span class="text-xs uppercase tracking-widest text-gray-400 mt-1">Control</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center px-8 md:px-24 bg-gray-50">
            <div class="max-w-md w-full mx-auto">
                
                <div class="lg:hidden mb-12 text-center">
                    <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight">Mi<span class="text-emerald-500">Despensa</span></h2>
                </div>

                <div class="mb-10 text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-gray-900">Iniciar sesión</h2>
                    <p class="text-gray-500 mt-3 font-medium">
                        ¿Aún no tienes cuenta? 
                        <Link :href="route('register')" class="text-emerald-600 font-bold hover:text-emerald-700 transition decoration-2 underline-offset-4">Regístrate</Link>
                    </p>
                </div>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
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

                    <div>
                        <div class="flex justify-between items-center ml-1">
                            <InputLabel for="password" value="Contraseña" class="text-gray-600" />
                            <Link :href="route('password.request')" class="text-xs text-gray-400 hover:text-emerald-600 transition">
                                ¿La has olvidado?
                            </Link>
                        </div>
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full bg-white border-gray-200 rounded-xl py-3 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                            v-model="form.password"
                            required
                            placeholder="••••••••"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center ml-1">
                        <Checkbox name="remember" v-model:checked="form.remember" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                        <span class="ms-2 text-sm text-gray-500">Recordar mi sesión</span>
                    </div>

                    <div class="pt-2">
                        <PrimaryButton
                            class="w-full justify-center bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-xl font-bold transition-all transform active:scale-[0.98] shadow-lg shadow-emerald-200"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            ACCEDER
                        </PrimaryButton>
                    </div>
                </form>

                <div class="mt-16 text-center text-[10px] text-gray-400 uppercase tracking-widest font-semibold">
                    © 2026 MiDespensa · Gestión de Inventario Inteligente
                </div>
            </div>
        </div>
    </div>
</template>