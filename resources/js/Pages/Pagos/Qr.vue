<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const props = defineProps({
    qrImage: String,
    transactionId: String,
    pagoId: Number,
    suscripcionId: Number,
    monto: Number,
});

const verificando = ref(false);
const contadorVerificacion = ref(0);
let pollingInterval = null;

const verificarEstado = async () => {
    if (verificando.value) return;

    verificando.value = true;
    contadorVerificacion.value++;

    try {
        const response = await axios.post('/pagos/pagofacil/consultar', {
            transactionId: props.transactionId,
            pagoId: props.pagoId,
            suscripcionId: props.suscripcionId
        }, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.data.success && response.data.paid) {
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }

            setTimeout(() => {
                window.location.href = response.data.redirect;
            }, 100);
        }
    } catch (error) {
        console.error('Error al verificar estado:', error);
    } finally {
        verificando.value = false;
    }
};

onMounted(() => {
    pollingInterval = setInterval(() => {
        verificarEstado();
    }, 5000);
});

onUnmounted(() => {
    if (pollingInterval) clearInterval(pollingInterval);
});
</script>

<template>

    <Head title="Pago QR" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Pago con QR - PagoFácil
            </h2>
        </template>

        <div class="py-12 bg-gray-100 dark:bg-slate-900 min-h-screen">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-8 text-gray-900 dark:text-gray-100">

                        <!-- Header con monto -->
                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-bold mb-2">Escanea el código QR para pagar</h3>
                            <div class="inline-block bg-blue-100 dark:bg-blue-900 px-6 py-3 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Monto a pagar</p>
                                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                    Bs. {{ parseFloat(monto).toFixed(2) }}
                                </p>
                            </div>
                        </div>

                        <!-- QR Code -->
                        <div class="mb-8 flex justify-center">
                            <div class="bg-white p-6 rounded-xl shadow-2xl">
                                <img :src="qrImage" alt="Código QR PagoFácil" class="w-72 h-72 object-contain">
                            </div>
                        </div>

                        <!-- Instrucciones -->
                        <div
                            class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-6 rounded-lg mb-6">
                            <h4 class="font-semibold text-lg mb-3 text-gray-800 dark:text-gray-200">
                                📱 Instrucciones para pagar:
                            </h4>
                            <ol class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <li class="flex items-start">
                                    <span class="font-bold mr-2">1.</span>
                                    <span>Abre tu aplicación bancaria o billetera digital</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="font-bold mr-2">2.</span>
                                    <span>Selecciona la opción de pago con QR</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="font-bold mr-2">3.</span>
                                    <span>Escanea el código QR mostrado arriba</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="font-bold mr-2">4.</span>
                                    <span>Confirma el pago en tu aplicación</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="font-bold mr-2">5.</span>
                                    <span>El sistema detectará automáticamente tu pago</span>
                                </li>
                            </ol>
                        </div>

                        <!-- Estado de verificación -->
                        <div
                            class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4 rounded-lg mb-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="animate-spin h-5 w-5 text-yellow-600 dark:text-yellow-400 mr-3"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <div>
                                        <p class="font-semibold text-yellow-800 dark:text-yellow-200">
                                            Verificando pago automáticamente...
                                        </p>
                                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                            Verificaciones realizadas: {{ contadorVerificacion }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="space-y-4">
                            <button @click="verificarEstado" :disabled="verificando"
                                class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                                <span v-if="verificando" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Verificando...
                                </span>
                                <span v-else>
                                    🔍 Verificar Estado del Pago Ahora
                                </span>
                            </button>

                            <div class="text-center pt-4 border-t border-gray-200 dark:border-gray-700">
                                <Link :href="route('suscripciones.show', suscripcionId)"
                                    class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Volver a la suscripción
                                </Link>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                ID de Transacción: {{ transactionId }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
