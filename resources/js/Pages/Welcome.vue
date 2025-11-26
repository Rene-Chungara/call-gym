<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>

    <Head title="Bienvenido" />

    <div class="min-h-screen bg-slate-900 text-white selection:bg-lime-500 selection:text-white">
        <!-- Navbar -->
        <nav class="absolute top-0 left-0 w-full z-50 px-6 py-4">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <img src="/img/image.png" alt="MAROMBA" class="h-12 w-auto" />
                    <span class="text-2xl font-bold tracking-wider hidden sm:block">MAROMBA</span>
                </div>

                <!-- Auth Links -->
                <div v-if="canLogin" class="flex items-center gap-4">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                        class="text-sm font-semibold text-white hover:text-lime-400 transition">
                    Dashboard
                    </Link>

                    <template v-else>
                        <Link :href="route('login')"
                            class="text-sm font-semibold text-white hover:text-lime-400 transition">
                        Iniciar Sesión
                        </Link>

                        <Link v-if="canRegister" :href="route('register')"
                            class="px-4 py-2 bg-lime-500 hover:bg-lime-400 text-slate-900 font-bold rounded-full transition text-sm">
                        Registrarse
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative min-h-screen flex items-center justify-center overflow-hidden">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/90 to-slate-900/50 z-10">
                </div>
                <!-- Placeholder for gym background image if available, otherwise dark gradient -->
                <div
                    class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center opacity-40">
                </div>
            </div>

            <!-- Content -->
            <div class="relative z-20 max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <h1 class="text-5xl md:text-7xl font-black leading-tight tracking-tight">
                        TU MEJOR VERSIÓN <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-lime-400 to-emerald-500">
                            COMIENZA AQUÍ
                        </span>
                    </h1>

                    <p class="text-lg text-gray-300 max-w-xl leading-relaxed">
                        En MAROMBA Training Center combinamos tecnología, equipamiento de primera y entrenadores
                        expertos para
                        ayudarte a superar tus límites.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <Link :href="route('register')"
                            class="px-8 py-4 bg-lime-500 hover:bg-lime-400 text-slate-900 font-bold rounded-lg transition text-center shadow-lg shadow-lime-500/20">
                        Comenzar Ahora
                        </Link>
                        <Link :href="route('login')"
                            class="px-8 py-4 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-lg transition text-center border border-slate-700">
                        Ya soy miembro
                        </Link>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-8 pt-8 border-t border-slate-800">
                        <div>
                            <p class="text-3xl font-bold text-white">500+</p>
                            <p class="text-sm text-gray-400">Miembros Activos</p>
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-white">20+</p>
                            <p class="text-sm text-gray-400">Entrenadores</p>
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-white">50+</p>
                            <p class="text-sm text-gray-400">Clases Semanales</p>
                        </div>
                    </div>
                </div>

                <!-- Right Side Image/Graphic -->
                <div class="hidden lg:block relative">
                    <div
                        class="relative z-10 rounded-2xl overflow-hidden shadow-2xl border border-slate-700 transform rotate-2 hover:rotate-0 transition duration-500">
                        <img src="https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?q=80&w=2070&auto=format&fit=crop"
                            alt="Gym Interior" class="w-full h-auto object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
                        <div class="absolute bottom-6 left-6">
                            <p class="text-lime-400 font-bold text-sm tracking-wider uppercase mb-1">Entrenamiento
                                Funcional</p>
                            <h3 class="text-2xl font-bold text-white">Zona de Alto Rendimiento</h3>
                        </div>
                    </div>

                    <!-- Decorative Elements -->
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-lime-500/20 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-emerald-500/20 rounded-full blur-3xl"></div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="py-24 bg-slate-950">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">¿Por qué elegir MAROMBA?</h2>
                    <p class="text-gray-400 max-w-2xl mx-auto">
                        No somos solo un gimnasio, somos una comunidad dedicada al fitness y al bienestar integral.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div
                        class="p-8 bg-slate-900 rounded-2xl border border-slate-800 hover:border-lime-500/50 transition group">
                        <div
                            class="w-12 h-12 bg-slate-800 rounded-lg flex items-center justify-center mb-6 group-hover:bg-lime-500/20 transition">
                            <svg class="w-6 h-6 text-lime-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Equipamiento Moderno</h3>
                        <p class="text-gray-400">
                            Máquinas de última generación para garantizar el mejor rendimiento en cada entrenamiento.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div
                        class="p-8 bg-slate-900 rounded-2xl border border-slate-800 hover:border-lime-500/50 transition group">
                        <div
                            class="w-12 h-12 bg-slate-800 rounded-lg flex items-center justify-center mb-6 group-hover:bg-lime-500/20 transition">
                            <svg class="w-6 h-6 text-lime-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Entrenadores Expertos</h3>
                        <p class="text-gray-400">
                            Profesionales certificados listos para guiarte y motivarte a alcanzar tus objetivos.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div
                        class="p-8 bg-slate-900 rounded-2xl border border-slate-800 hover:border-lime-500/50 transition group">
                        <div
                            class="w-12 h-12 bg-slate-800 rounded-lg flex items-center justify-center mb-6 group-hover:bg-lime-500/20 transition">
                            <svg class="w-6 h-6 text-lime-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Horarios Flexibles</h3>
                        <p class="text-gray-400">
                            Abierto desde temprano hasta tarde para adaptarse a tu estilo de vida ocupado.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-slate-950 border-t border-slate-900 py-12">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <div class="flex items-center justify-center gap-3 mb-6">
                    <img src="/img/image.png" alt="MAROMBA" class="h-8 w-auto opacity-70" />
                    <span class="text-xl font-bold tracking-wider text-gray-400">MAROMBA</span>
                </div>
                <p class="text-gray-500 text-sm">
                    © {{ new Date().getFullYear() }} MAROMBA Training Center. Todos los derechos reservados.
                </p>
            </div>
        </footer>
    </div>
</template>
