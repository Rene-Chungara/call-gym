<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const isCliente = computed(() => user.value.is_clientes && !user.value.is_propietario && !user.value.is_secretaria);

const props = defineProps({
    suscripcion: Object,
    pagos: Array,
    planesPago: Array,
});

// Formatear fecha
function formatFecha(fecha) {
    if (!fecha) return '';
    const d = new Date(fecha);
    return `${String(d.getDate()).padStart(2, '0')}-${String(d.getMonth() + 1).padStart(2, '0')}-${d.getFullYear()}`;
}

// Determinar color del estado
const colorEstado = computed(() => {
    if (props.suscripcion.estado_pago === 'pagado') return 'green';
    if (props.suscripcion.estado_pago === 'pendiente') return 'amber';
    return 'red';
});

// Determinar icono del tipo de pago
const iconoTipoPago = computed(() => {
    return props.suscripcion.tipo_pago === 'contado' ? '→' : '📋';
});

// Obtener cuotas pendientes
const cuotasPendientes = computed(() => {
    return props.planesPago.flatMap(plan =>
        plan.cuotas.filter(cuota => cuota.estado !== 'pagado')
    );
});

// Realizar pago al contado
function realizarPago() {
    router.get(route('pagos.create', { suscripcion_id: props.suscripcion.id }));
}

// Pagar más tarde
function pagarMasTarde() {
    if (confirm('La suscripción quedará inactiva hasta que realices el pago. ¿Continuar?')) {
        router.put(route('suscripciones.update', props.suscripcion.id), {
            usuario_id: props.suscripcion.usuario.id,
            membresia_id: props.suscripcion.membresia.id,
            fecha_inicio: props.suscripcion.fecha_inicio,
            estado: 0,
        });
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Detalle de Suscripción
                </h2>
                <Link :href="route('suscripciones.index')"
                    class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">
                Volver
                </Link>
            </div>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- ENCABEZADO CON CLIENTE Y ESTADO -->
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-8 mb-6 border-l-4" :class="{
                    'border-l-green-500': suscripcion.estado_pago === 'pagado',
                    'border-l-amber-500': suscripcion.estado_pago === 'pendiente',
                    'border-l-red-500': suscripcion.estado_pago === 'vencido',
                }">

                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                {{ suscripcion.usuario.nombre }}
                            </h1>
                            <p class="text-lg text-gray-600 dark:text-gray-400">
                                {{ suscripcion.membresia.nombre }}
                            </p>
                        </div>

                        <div class="text-right">
                            <span class="inline-block px-4 py-2 rounded-lg text-sm font-bold" :class="{
                                'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-100': suscripcion.estado_pago === 'pagado',
                                'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-100': suscripcion.estado_pago === 'pendiente',
                                'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-100': suscripcion.estado_pago === 'vencido',
                            }">
                                {{ suscripcion.estado_pago === 'pagado' ? 'Pagado' : suscripcion.estado_pago ===
                                    'pendiente' ?
                                    'Pendiente' : 'Vencido' }}
                            </span>
                        </div>
                    </div>

                    <!-- BARRA DE PROGRESO -->
                    <div class="mt-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progreso de Pago</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{
                                suscripcion.porcentaje_pagado
                                }}%</span>
                        </div>
                        <div class="w-full h-3 bg-gray-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500" :class="{
                                'bg-green-500': suscripcion.estado_pago === 'pagado',
                                'bg-indigo-600': suscripcion.estado_pago === 'pendiente',
                                'bg-red-500': suscripcion.estado_pago === 'vencido',
                            }" :style="{ width: suscripcion.porcentaje_pagado + '%' }"></div>
                        </div>
                    </div>
                </div>

                <!-- GRID: INFORMACIÓN Y MONTOS -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

                    <!-- INFORMACIÓN GENERAL -->
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-3 border-b border-gray-200 dark:border-slate-700">
                            Información General
                        </h3>

                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Inicio</p>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{
                                    formatFecha(suscripcion.fecha_inicio) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Vencimiento</p>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{
                                    formatFecha(suscripcion.fecha_fin)
                                    }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Tipo de Pago</p>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ suscripcion.tipo_pago === 'contado' ? 'Al Contado' : 'A Crédito' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- MONTOS -->
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-3 border-b border-gray-200 dark:border-slate-700">
                            Información de Montos
                        </h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Total:</span>
                                <span class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                    Bs. {{ parseFloat(suscripcion.monto_total).toFixed(2) }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Pagado:</span>
                                <span class="text-xl font-bold text-green-600 dark:text-green-400">
                                    Bs. {{ parseFloat(suscripcion.monto_pagado).toFixed(2) }}
                                </span>
                            </div>

                            <div
                                class="border-t border-gray-200 dark:border-slate-700 pt-4 flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">Pendiente:</span>
                                <span class="text-xl font-bold" :class="{
                                    'text-green-600 dark:text-green-400': suscripcion.monto_pendiente <= 0,
                                    'text-red-600 dark:text-red-400': suscripcion.monto_pendiente > 0,
                                }">
                                    Bs. {{ parseFloat(suscripcion.monto_pendiente).toFixed(2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ACCIONES -->
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-3 border-b border-gray-200 dark:border-slate-700">
                            Acciones
                        </h3>

                        <div class="space-y-3">
                            <!-- Realizar pago al contado -->
                            <button
                                v-if="suscripcion.tipo_pago === 'contado' && suscripcion.estado_pago === 'pendiente'"
                                @click="realizarPago"
                                class="block w-full text-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                                Realizar Pago
                            </button>

                            <!-- Pagar más tarde -->
                            <button v-if="suscripcion.estado_pago === 'pendiente'" @click="pagarMasTarde"
                                class="block w-full text-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition">
                                Pagar Más Tarde
                            </button>

                            <!-- Crear plan a crédito -->
                            <Link
                                v-if="!isCliente && suscripcion.tipo_pago === 'contado' && suscripcion.monto_pendiente > 0"
                                :href="route('plan-pagos.create')"
                                class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            Crear Plan a Crédito
                            </Link>

                            <!-- Editar suscripción -->
                            <Link :href="route('suscripciones.edit', suscripcion.id)"
                                class="block w-full text-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition">
                            Editar
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- HISTORIAL DE PAGOS AL CONTADO -->
                <div v-if="pagos.length > 0" class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 mb-6">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-3 border-b border-gray-200 dark:border-slate-700">
                        Historial de Pagos al Contado
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-slate-700">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">Fecha
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">Monto
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                        Método</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                        Estado</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                        Observaciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                                <tr v-for="pago in pagos" :key="pago.id"
                                    class="hover:bg-gray-50 dark:hover:bg-slate-700">
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{
                                        formatFecha(pago.fecha_abono) }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">Bs. {{
                                        parseFloat(pago.monto_abonado).toFixed(2) }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ pago.metodo_pago ===
                                        'efectivo' ?
                                        'Efectivo' : 'Tarjeta' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block px-2 py-1 rounded text-xs font-semibold" :class="{
                                            'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-100': pago.estado_pago,
                                            'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-100': !pago.estado_pago,
                                        }">
                                            {{ pago.estado_pago ? 'Pagado' : 'Pendiente' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ pago.observaciones
                                        || '-'
                                        }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PLANES A CRÉDITO -->
                <div v-if="planesPago.length > 0" class="space-y-6">
                    <div v-for="plan in planesPago" :key="plan.id"
                        class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
                        <div
                            class="flex justify-between items-start mb-4 pb-4 border-b border-gray-200 dark:border-slate-700">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    Plan a Crédito - {{ plan.cantidad_cuotas }} Cuotas
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Iniciado: {{ formatFecha(plan.fecha_inicio) }}
                                </p>
                            </div>
                            <span class="inline-block px-3 py-1 rounded-lg text-sm font-bold" :class="{
                                'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-100': plan.estado === 'completado',
                                'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-100': plan.estado === 'activo',
                                'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-100': plan.estado === 'cancelado',
                            }">
                                {{ plan.estado === 'completado' ? 'Completado' : plan.estado === 'activo' ? 'Activo' :
                                    'Cancelado' }}
                            </span>
                        </div>

                        <!-- CUOTAS -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-slate-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                            Cuota
                                        </th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                            Monto
                                        </th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                            Vencimiento</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                            Pagada
                                        </th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                            Estado
                                        </th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                                    <tr v-for="cuota in plan.cuotas" :key="cuota.id"
                                        class="hover:bg-gray-50 dark:hover:bg-slate-700">
                                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">#{{
                                            cuota.numero_cuota }}</td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">Bs. {{
                                            parseFloat(cuota.monto).toFixed(2) }}</td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{
                                            formatFecha(cuota.fecha_vencimiento) }}</td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ cuota.fecha_pago ?
                                            formatFecha(cuota.fecha_pago) : '-' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-block px-2 py-1 rounded text-xs font-semibold" :class="{
                                                'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-100': cuota.estado === 'pagado',
                                                'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-100': cuota.estado === 'pendiente',
                                                'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-100': cuota.estado === 'vencido',
                                            }">
                                                {{ cuota.estado === 'pagado' ? 'Pagada' : cuota.estado === 'pendiente' ?
                                                    'Pendiente' : 'Vencida' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <Link v-if="cuota.estado === 'pendiente'"
                                                :href="route('cuotas-pago.create', cuota.id)"
                                                class="inline-block px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-medium transition">
                                            Pagar
                                            </Link>
                                            <div>
                                                <p class="text-gray-600 dark:text-gray-400">Vencimiento</p>
                                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{
                                                    formatFecha(suscripcion.fecha_fin) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 dark:text-gray-400">Tipo de Pago</p>
                                                <p class="font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ suscripcion.tipo_pago === 'contado' ? 'Al Contado' : 'A Crédito'
                                                    }}
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- MONTOS -->
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-3 border-b border-gray-200 dark:border-slate-700">
                            Información de Montos
                        </h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Total:</span>
                                <span class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                    Bs. {{ parseFloat(suscripcion.monto_total).toFixed(2) }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Pagado:</span>
                                <span class="text-xl font-bold text-green-600 dark:text-green-400">
                                    Bs. {{ parseFloat(suscripcion.monto_pagado).toFixed(2) }}
                                </span>
                            </div>

                            <div
                                class="border-t border-gray-200 dark:border-slate-700 pt-4 flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">Pendiente:</span>
                                <span class="text-xl font-bold" :class="{
                                    'text-green-600 dark:text-green-400': suscripcion.monto_pendiente <= 0,
                                    'text-red-600 dark:text-red-400': suscripcion.monto_pendiente > 0,
                                }">
                                    Bs. {{ parseFloat(suscripcion.monto_pendiente).toFixed(2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ACCIONES -->
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-3 border-b border-gray-200 dark:border-slate-700">
                            Acciones
                        </h3>

                        <div class="space-y-3">
                            <!-- Realizar pago al contado -->
                            <button
                                v-if="suscripcion.tipo_pago === 'contado' && suscripcion.estado_pago === 'pendiente'"
                                @click="realizarPago"
                                class="block w-full text-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                                Realizar Pago
                            </button>

                            <!-- Pagar más tarde -->
                            <button v-if="suscripcion.estado_pago === 'pendiente'" @click="pagarMasTarde"
                                class="block w-full text-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition">
                                Pagar Más Tarde
                            </button>

                            <!-- Crear plan a crédito -->
                            <Link v-if="suscripcion.tipo_pago === 'contado' && suscripcion.monto_pendiente > 0"
                                :href="route('plan-pagos.create')"
                                class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            Crear Plan a Crédito
                            </Link>

                            <!-- Editar suscripción -->
                            <Link :href="route('suscripciones.edit', suscripcion.id)"
                                class="block w-full text-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition">
                            Editar
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- HISTORIAL DE PAGOS AL CONTADO -->
                <div v-if="pagos.length > 0" class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 mb-6">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-3 border-b border-gray-200 dark:border-slate-700">
                        Historial de Pagos al Contado
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-slate-700">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">Fecha
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">Monto
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                        Método</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                        Estado</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                        Observaciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                                <tr v-for="pago in pagos" :key="pago.id"
                                    class="hover:bg-gray-50 dark:hover:bg-slate-700">
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{
                                        formatFecha(pago.fecha_abono) }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">Bs. {{
                                        parseFloat(pago.monto_abonado).toFixed(2) }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ pago.metodo_pago ===
                                        'efectivo' ?
                                        'Efectivo' : 'Tarjeta' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block px-2 py-1 rounded text-xs font-semibold" :class="{
                                            'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-100': pago.estado_pago,
                                            'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-100': !pago.estado_pago,
                                        }">
                                            {{ pago.estado_pago ? 'Pagado' : 'Pendiente' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ pago.observaciones
                                        || '-'
                                        }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PLANES A CRÉDITO -->
                <div v-if="planesPago.length > 0" class="space-y-6">
                    <div v-for="plan in planesPago" :key="plan.id"
                        class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
                        <div
                            class="flex justify-between items-start mb-4 pb-4 border-b border-gray-200 dark:border-slate-700">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    Plan a Crédito - {{ plan.cantidad_cuotas }} Cuotas
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Iniciado: {{ formatFecha(plan.fecha_inicio) }}
                                </p>
                            </div>
                            <span class="inline-block px-3 py-1 rounded-lg text-sm font-bold" :class="{
                                'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-100': plan.estado === 'completado',
                                'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-100': plan.estado === 'activo',
                                'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-100': plan.estado === 'cancelado',
                            }">
                                {{ plan.estado === 'completado' ? 'Completado' : plan.estado === 'activo' ? 'Activo' :
                                    'Cancelado' }}
                            </span>
                        </div>

                        <!-- CUOTAS -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-slate-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                            Cuota
                                        </th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                            Monto
                                        </th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                            Vencimiento</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                            Pagada
                                        </th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                            Estado
                                        </th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                                    <tr v-for="cuota in plan.cuotas" :key="cuota.id"
                                        class="hover:bg-gray-50 dark:hover:bg-slate-700">
                                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">#{{
                                            cuota.numero_cuota }}</td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">Bs. {{
                                            parseFloat(cuota.monto).toFixed(2) }}</td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{
                                            formatFecha(cuota.fecha_vencimiento) }}</td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ cuota.fecha_pago ?
                                            formatFecha(cuota.fecha_pago) : '-' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-block px-2 py-1 rounded text-xs font-semibold" :class="{
                                                'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-100': cuota.estado === 'pagado',
                                                'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-100': cuota.estado === 'pendiente',
                                                'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-100': cuota.estado === 'vencido',
                                            }">
                                                {{ cuota.estado === 'pagado' ? 'Pagada' : cuota.estado === 'pendiente' ?
                                                    'Pendiente' : 'Vencida' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <Link v-if="cuota.estado === 'pendiente'"
                                                :href="route('cuotas-pago.create', cuota.id)"
                                                class="inline-block px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-medium transition">
                                            Pagar
                                            </Link>
                                            <span v-else class="text-gray-400 text-xs">-</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- SIN PAGOS -->
                <div v-if="pagos.length === 0 && planesPago.length === 0"
                    class="bg-white dark:bg-slate-800 rounded-lg shadow p-12 text-center">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">No hay pagos registrados para esta suscripción</p>
                    <button v-if="suscripcion.tipo_pago === 'contado'" @click="realizarPago"
                        class="inline-block px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                        Registrar Pago
                    </button>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
```
