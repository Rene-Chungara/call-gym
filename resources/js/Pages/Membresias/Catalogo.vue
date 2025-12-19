<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    membresias: Array,
    tieneSuscripcionActiva: Boolean,
    suscripcionActiva: Object,
});

// Formatear duración en días a texto legible
const formatearDuracion = (dias) => {
    if (dias === 30) return '1 Mes';
    if (dias === 90) return '3 Meses';
    if (dias === 180) return '6 Meses';
    if (dias === 365) return '1 Año';
    if (dias < 30) return `${dias} Días`;
    const meses = Math.floor(dias / 30);
    return `${meses} ${meses === 1 ? 'Mes' : 'Meses'}`;
};
</script>

<template>

    <Head title="Catálogo de Membresías" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Catálogo de Membresías
            </h2>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Alerta si ya tiene suscripción activa -->
                <div v-if="tieneSuscripcionActiva"
                    class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3 flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200">
                                Ya tienes una suscripción activa
                            </h3>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                Tu membresía <strong>{{ suscripcionActiva.membresia }}</strong> está activa hasta el
                                <strong>{{ new Date(suscripcionActiva.fecha_fin).toLocaleDateString('es-ES', {
                                    year:
                                        'numeric',
                                    month: 'long', day: 'numeric'
                                }) }}</strong>.
                                No puedes comprar otra membresía hasta que expire.
                            </p>
                            <Link :href="route('suscripciones.index')"
                                class="inline-flex items-center mt-2 text-sm font-medium text-yellow-800 dark:text-yellow-200 hover:text-yellow-900 dark:hover:text-yellow-100">
                                Ver mi suscripción
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Header -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Elige tu Membresía
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Selecciona el plan que mejor se adapte a tus necesidades
                    </p>
                </div>

                <!-- Grid de Membresías -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="membresia in membresias" :key="membresia.id"
                        class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden border border-gray-200 dark:border-slate-700 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">

                        <!-- Header de la Card -->
                        <div
                            class="bg-gradient-to-r from-slate-700 to-slate-900 dark:from-slate-800 dark:to-slate-950 p-6 text-white">
                            <h3 class="text-2xl font-bold mb-2">{{ membresia.nombre }}</h3>
                            <div class="flex items-baseline">
                                <span class="text-4xl font-extrabold">Bs. {{ membresia.precio.toFixed(2) }}</span>
                            </div>
                            <p class="text-slate-200 dark:text-slate-300 mt-2">{{
                                formatearDuracion(membresia.duracion_dias) }}
                            </p>
                        </div>

                        <!-- Contenido -->
                        <div class="p-6">
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-gray-700 dark:text-gray-300">Acceso completo al gimnasio</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-gray-700 dark:text-gray-300">Rutinas personalizadas</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-gray-700 dark:text-gray-300">Seguimiento de progreso</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-gray-700 dark:text-gray-300">Asesoría de instructores</span>
                                </li>
                            </ul>

                            <!-- Botón de Compra -->
                            <Link v-if="!tieneSuscripcionActiva" :href="route('membresias.checkout', membresia.id)"
                                class="block w-full text-center px-6 py-3 bg-slate-700 dark:bg-slate-600 text-white font-semibold rounded-lg hover:bg-slate-800 dark:hover:bg-slate-700 transition-all shadow-md hover:shadow-lg">
                                Comprar Ahora
                            </Link>
                            <button v-else disabled
                                class="block w-full text-center px-6 py-3 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 font-semibold rounded-lg cursor-not-allowed">
                                No Disponible
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div
                    class="mt-12 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        💳 Métodos de Pago Disponibles
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                            <svg class="w-8 h-8 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">Código QR</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Paga con tu app bancaria</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                            <svg class="w-8 h-8 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">Tarjeta de Crédito/Débito</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Pago seguro con Stripe</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
