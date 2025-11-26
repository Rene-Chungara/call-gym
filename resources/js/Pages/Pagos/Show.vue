<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    pago: Object,
});

function format(fecha) {
    if (!fecha) return "";
    const d = new Date(fecha);
    return `${String(d.getDate()).padStart(2, "0")}-${String(
        d.getMonth() + 1
    ).padStart(2, "0")}-${d.getFullYear()}`;
}

function formatHora(fecha) {
    if (!fecha) return "";
    const d = new Date(fecha);
    return `${String(d.getHours()).padStart(2, "0")}:${String(d.getMinutes()).padStart(2, "0")}`;
}

const porcentajePagado = computed(() => {
    return Math.min(100, Math.floor((props.pago.monto_abonado / props.pago.monto_total_membresia) * 100));
});

const estadoColor = computed(() => {
    if (props.pago.estado_pago) return "green";
    return "red";
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    Detalle del Pago
                </h2>
                <Link
                    :href="route('pagos.index')"
                    class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm"
                >
                    Volver a pagos
                </Link>
            </div>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- ENCABEZADO CON ESTADO -->
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-8 mb-6 border-l-4"
                    :class="{
                        'border-l-green-500': props.pago.estado_pago,
                        'border-l-red-500': !props.pago.estado_pago,
                    }">

                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                {{ props.pago.suscripcion?.usuario?.nombre || 'N/A' }}
                            </h1>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ props.pago.suscripcion?.membresia?.nombre || 'N/A' }}
                            </p>
                        </div>

                        <div class="text-right">
                            <span
                                class="inline-block px-4 py-2 rounded-full text-sm font-bold"
                                :class="{
                                    'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-100': props.pago.estado_pago,
                                    'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-100': !props.pago.estado_pago,
                                }"
                            >
                                {{ props.pago.estado_pago ? "Pagado" : "Pendiente de Pago" }}
                            </span>
                        </div>
                    </div>

                    <!-- BARRA DE PROGRESO -->
                    <div class="mt-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progreso de Pago</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ porcentajePagado }}%</span>
                        </div>
                        <div class="w-full h-3 bg-gray-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div
                                class="h-full rounded-full transition-all duration-500"
                                :class="{
                                    'bg-green-500': props.pago.estado_pago,
                                    'bg-indigo-600': !props.pago.estado_pago,
                                }"
                                :style="{ width: porcentajePagado + '%' }"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- GRID DE INFORMACIÓN -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                    <!-- MONTOS -->
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-3 border-b border-gray-200 dark:border-slate-700">
                            Información de Montos
                        </h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Monto Total:</span>
                                <span class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                    Bs. {{ parseFloat(props.pago.monto_total_membresia).toFixed(2) }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Monto Abonado:</span>
                                <span class="text-xl font-bold text-green-600 dark:text-green-400">
                                    Bs. {{ parseFloat(props.pago.monto_abonado).toFixed(2) }}
                                </span>
                            </div>

                            <div class="border-t border-gray-200 dark:border-slate-700 pt-4 flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">Método de Pago:</span>
                                <span class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ props.pago.metodo_pago === 'efectivo' ? 'Efectivo' : 'Tarjeta' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- FECHAS -->
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-3 border-b border-gray-200 dark:border-slate-700">
                            Información de Fechas
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Fecha de Abono</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ format(props.pago.fecha_abono) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ formatHora(props.pago.fecha_abono) }}
                                </p>
                            </div>

                            <div v-if="props.pago.observaciones" class="border-t border-gray-200 dark:border-slate-700 pt-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Observaciones</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ props.pago.observaciones }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- INFORMACIÓN ADICIONAL -->
                <div v-if="props.pago.stripe_payment_id || props.pago.stripe_session_id" class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-3 border-b border-gray-200 dark:border-slate-700">
                        Información de Stripe
                    </h3>

                    <div class="space-y-3 text-sm">
                        <div v-if="props.pago.stripe_payment_id" class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">ID de Pago:</span>
                            <span class="font-mono text-gray-900 dark:text-gray-100">{{ props.pago.stripe_payment_id }}</span>
                        </div>
                        <div v-if="props.pago.stripe_session_id" class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">ID de Sesión:</span>
                            <span class="font-mono text-gray-900 dark:text-gray-100">{{ props.pago.stripe_session_id }}</span>
                        </div>
                        <div v-if="props.pago.stripe_status" class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Estado:</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ props.pago.stripe_status }}</span>
                        </div>
                    </div>
                </div>

                <!-- BOTONES DE ACCIÓN -->
                <div class="flex gap-3 justify-end">
                    <Link
                        :href="route('pagos.index')"
                        class="px-6 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-gray-100 rounded-lg font-medium transition"
                    >
                        Volver
                    </Link>

                    <Link
                        v-if="!props.pago.estado_pago"
                        :href="route('pagos.create')"
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition"
                    >
                        Agregar Pago
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
