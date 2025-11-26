<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen flex bg-slate-900 selection:bg-lime-500 selection:text-white">

        <Head title="Iniciar Sesión" />

        <!-- Left Side - Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center px-8 sm:px-12 lg:px-24 py-12 z-10 bg-slate-900">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <!-- Logo -->
                <Link :href="route('welcome')" class="flex items-center gap-3 mb-10">
                <img src="/img/image.png" alt="MAROMBA" class="h-12 w-auto" />
                <span class="text-3xl font-bold text-white tracking-wider">MAROMBA</span>
                </Link>

                <h2 class="text-3xl font-bold text-white mb-2">
                    Bienvenido de nuevo
                </h2>
                <p class="text-gray-400 mb-8">
                    Ingresa tus credenciales para acceder a tu cuenta.
                </p>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-400">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="email" value="Correo Electrónico" class="text-white" />
                        <TextInput id="email" type="email"
                            class="mt-1 block w-full bg-slate-800 border-slate-700 text-white focus:border-lime-500 focus:ring-lime-500 placeholder-gray-500"
                            v-model="form.email" required autofocus autocomplete="username"
                            placeholder="tu@email.com" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Contraseña" class="text-white" />
                        <TextInput id="password" type="password"
                            class="mt-1 block w-full bg-slate-800 border-slate-700 text-white focus:border-lime-500 focus:ring-lime-500 placeholder-gray-500"
                            v-model="form.password" required autocomplete="current-password" placeholder="••••••••" />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember"
                                class="text-lime-500 focus:ring-lime-500 bg-slate-800 border-slate-700" />
                            <span class="ms-2 text-sm text-gray-400">Recordarme</span>
                        </label>

                        <Link v-if="canResetPassword" :href="route('password.request')"
                            class="text-sm text-lime-400 hover:text-lime-300 font-medium transition">
                        ¿Olvidaste tu contraseña?
                        </Link>
                    </div>

                    <div>
                        <PrimaryButton
                            class="w-full justify-center py-3 bg-lime-500 hover:bg-lime-400 text-slate-900 font-bold text-lg focus:ring-lime-500 transition transform hover:scale-[1.02]"
                            :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Iniciar Sesión
                        </PrimaryButton>
                    </div>
                </form>

                <p class="mt-8 text-center text-sm text-gray-400">
                    ¿No tienes una cuenta?
                    <Link :href="route('register')" class="font-semibold text-lime-400 hover:text-lime-300 transition">
                    Regístrate gratis
                    </Link>
                </p>
            </div>
        </div>

        <!-- Right Side - Image -->
        <div class="hidden lg:block lg:w-1/2 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-900 to-transparent z-10"></div>
            <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=2070&auto=format&fit=crop"
                alt="Gym Motivation" class="absolute inset-0 w-full h-full object-cover" />
            <div class="absolute bottom-0 left-0 p-12 z-20 max-w-lg">
                <div class="w-16 h-1 bg-lime-500 mb-6"></div>
                <h3 class="text-4xl font-bold text-white mb-4 leading-tight">
                    "El dolor que sientes hoy es la fuerza que sentirás mañana."
                </h3>
                <p class="text-gray-300 text-lg">
                    Únete a la comunidad de MAROMBA y transforma tu vida.
                </p>
            </div>
        </div>
    </div>
</template>
