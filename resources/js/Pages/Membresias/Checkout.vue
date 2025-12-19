<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    membresia: Object,
});

const tipoPago = ref('contado');
const metodoPago = ref('qr');
const cantidadCuotas = ref(4);
const procesando = ref(false);

// Calcular monto por cuota
const montoPorCuota = computed(() => {
    if (tipoPago.value === 'credito') {
        return (props.membresia.precio / cantidadCuotas.value).toFixed(2);
    }
    return props.membresia.precio.toFixed(2);
});

// Generar fechas automáticas para cuotas
const generarFechas = () => {
    const fechas = [];
    const montos = [];
    const hoy = new Date();

    for (let i = 0; i < cantidadCuotas.value; i++) {
        const fecha = new Date(hoy);
        fecha.setDate(fecha.getDate() + ((i + 1) * 7)); // Cada 7 días
        fechas.push(fecha.toISOString().split('T')[0]);
        montos.push(parseFloat(montoPorCuota.value));
    }

    return { fechas, montos };
};

// Procesar compra
const procesarCompra = () => {
    if (procesando.value) return;

    procesando.value = true;

    let datos = {
        membresia_id: props.membresia.id,
        tipo_pago: tipoPago.value,
        metodo_pago: metodoPago.value,
    };

    // Si es crédito, agregar fechas y montos
    if (tipoPago.value === 'credito') {
        const { fechas, montos } = generarFechas();
        datos.cantidad_cuotas = cantidadCuotas.value;
        datos.fechas = fechas;
        datos.montos = montos;
    }

    router.post(route('membresias.comprar'), datos, {
        preserveState: false,
        preserveScroll: false,
        onFinish: () => {
            procesando.value = false;
        },
        onError: (errors) => {
            console.error('Error al procesar compra:', errors);
            procesando.value = false;
        }
    });
};

// Formatear duración
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

    <Head title="Checkout - Membresía" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Checkout - Comprar Membresía
            </h2>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Formulario de Pago -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Tipo de Pago -->
                        <div
                            class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                1. Selecciona el Tipo de Pago
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label
                                    class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                    :class="tipoPago === 'contado' ? 'border-slate-600 bg-slate-50 dark:bg-slate-700/30' : 'border-gray-300 dark:border-slate-600 hover:border-gray-400'">
                                    <input type="radio" v-model="tipoPago" value="contado" class="sr-only">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">Al Contado</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Pago único completo</p>
                                    </div>
                                    <svg v-if="tipoPago === 'contado'" class="w-6 h-6 text-indigo-600"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </label>

                                <label
                                    class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                    :class="tipoPago === 'credito' ? 'border-slate-600 bg-slate-50 dark:bg-slate-700/30' : 'border-gray-300 dark:border-slate-600 hover:border-gray-400'">
                                    <input type="radio" v-model="tipoPago" value="credito" class="sr-only">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">A Crédito</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Pago en cuotas</p>
                                    </div>
                                    <svg v-if="tipoPago === 'credito'" class="w-6 h-6 text-indigo-600"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </label>
                            </div>

                            <!-- Configuración de Cuotas -->
                            <div v-if="tipoPago === 'credito'"
                                class="mt-4 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Cantidad de Cuotas
                                </label>
                                <select v-model.number="cantidadCuotas"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-slate-500 focus:border-slate-500 dark:bg-slate-700 dark:text-white">
                                    <option v-for="n in 11" :key="n + 1" :value="n + 1">{{ n + 1 }} cuotas</option>
                                </select>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Monto por cuota: <span class="font-bold text-slate-700 dark:text-slate-300">Bs. {{
                                        montoPorCuota }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Método de Pago -->
                        <div
                            class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                2. Selecciona el Método de Pago
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label
                                    class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                    :class="metodoPago === 'qr' ? 'border-slate-600 bg-slate-50 dark:bg-slate-700/30' : 'border-gray-300 dark:border-slate-600 hover:border-gray-400'">
                                    <input type="radio" v-model="metodoPago" value="qr" class="sr-only">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">Código QR</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">PagoFácil</p>
                                    </div>
                                    <svg v-if="metodoPago === 'qr'" class="w-6 h-6 text-slate-600" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </label>

                                <label
                                    class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                    :class="metodoPago === 'tarjeta' ? 'border-slate-600 bg-slate-50 dark:bg-slate-700/30' : 'border-gray-300 dark:border-slate-600 hover:border-gray-400'">
                                    <input type="radio" v-model="metodoPago" value="tarjeta" class="sr-only">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">Tarjeta</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Crédito/Débito</p>
                                    </div>
                                    <svg v-if="metodoPago === 'tarjeta'" class="w-6 h-6 text-slate-600"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </label>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex gap-4">
                            <Link :href="route('membresias.catalogo')"
                                class="flex-1 px-6 py-3 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition text-center font-semibold">
                                Volver al Catálogo
                            </Link>
                            <button @click="procesarCompra" :disabled="procesando"
                                class="flex-1 px-6 py-3 bg-slate-700 dark:bg-slate-600 text-white font-semibold rounded-lg hover:bg-slate-800 dark:hover:bg-slate-700 transition shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                <span v-if="procesando" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Procesando...
                                </span>
                                <span v-else>Confirmar Compra</span>
                            </button>
                        </div>
                    </div>

                    <!-- Resumen de Compra -->
                    <div class="lg:col-span-1">
                        <div
                            class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6 sticky top-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                Resumen de Compra
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Membresía</p>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ membresia.nombre }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Duración</p>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{
                                        formatearDuracion(membresia.duracion_dias) }}</p>
                                </div>

                                <div class="border-t border-gray-200 dark:border-slate-700 pt-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Tipo de Pago</p>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                                        {{ tipoPago === 'contado' ? 'Al Contado' : `A Crédito (${cantidadCuotas}
                                        cuotas)` }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Método de Pago</p>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                                        {{ metodoPago === 'qr' ? 'Código QR' : 'Tarjeta' }}
                                    </p>
                                </div>

                                <div class="border-t border-gray-200 dark:border-slate-700 pt-4">
                                    <div class="flex justify-between items-baseline">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Total</p>
                                        <p class="text-2xl font-bold text-slate-700 dark:text-slate-300">
                                            Bs. {{ membresia.precio.toFixed(2) }}
                                        </p>
                                    </div>
                                    <p v-if="tipoPago === 'credito'"
                                        class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Bs. {{ montoPorCuota }} por cuota
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
