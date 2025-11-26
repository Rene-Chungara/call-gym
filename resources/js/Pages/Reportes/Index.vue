<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    reportes: Object,
    filtros: Object,
});

const fechaInicio = ref(props.filtros.fecha_inicio || '');
const fechaFin = ref(props.filtros.fecha_fin || '');
const tipoReporte = ref(props.filtros.tipo || 'todos');

const aplicarFiltros = () => {
    router.get(route('reportes.index'), {
        fecha_inicio: fechaInicio.value,
        fecha_fin: fechaFin.value,
        tipo: tipoReporte.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const descargarPDF = () => {
    const params = new URLSearchParams({
        fecha_inicio: fechaInicio.value,
        fecha_fin: fechaFin.value,
        tipo: tipoReporte.value,
    }).toString();

    window.open(route('reportes.exportar-pdf') + '?' + params, '_blank');
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(value);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};
</script>

<template>

    <Head title="Reportes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    Reportes y Estadísticas
                </h2>
                <button @click="descargarPDF"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Exportar PDF
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

                <!-- Filtros -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Filtros de Reporte</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha
                                Inicio</label>
                            <input type="date" v-model="fechaInicio"
                                class="w-full rounded-md border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-lime-500 focus:ring-lime-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha
                                Fin</label>
                            <input type="date" v-model="fechaFin"
                                class="w-full rounded-md border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-lime-500 focus:ring-lime-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de
                                Datos</label>
                            <select v-model="tipoReporte"
                                class="w-full rounded-md border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-lime-500 focus:ring-lime-500">
                                <option value="todos">Todos los datos</option>
                                <option value="suscripciones">Solo Suscripciones</option>
                                <option value="paquetes">Solo Paquetes</option>
                                <option value="ingresos">Solo Ingresos</option>
                            </select>
                        </div>
                        <div>
                            <button @click="aplicarFiltros"
                                class="w-full px-4 py-2 bg-slate-800 hover:bg-slate-700 dark:bg-slate-600 dark:hover:bg-slate-500 text-white rounded-lg transition">
                                Filtrar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Resumen Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nuevas Suscripciones</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                            {{ reportes.resumen.totalSuscripciones }}
                        </p>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Paquetes Vendidos</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                            {{ reportes.resumen.totalPaquetesVendidos }}
                        </p>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ingresos Suscripciones</p>
                        <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">
                            {{ formatCurrency(reportes.resumen.ingresosSuscripciones) }}
                        </p>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ingresos Paquetes</p>
                        <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">
                            {{ formatCurrency(reportes.resumen.ingresosPaquetes) }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Últimas Suscripciones -->
                    <div v-if="tipoReporte === 'todos' || tipoReporte === 'suscripciones'"
                        class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Suscripciones del Periodo
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                                <thead class="bg-gray-50 dark:bg-slate-700/50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Usuario</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Membresía</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Fecha</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                                    <tr v-for="(item, index) in reportes.usuariosSuscritos" :key="index">
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ item.nombre }}
                                            <div class="text-xs text-gray-500">{{ item.email }}</div>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{
                                            item.membresia }}</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                            {{ formatDate(item.fecha_inicio) }}</td>
                                    </tr>
                                    <tr v-if="reportes.usuariosSuscritos.length === 0">
                                        <td colspan="3"
                                            class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No hay datos disponibles</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Últimos Paquetes -->
                    <div v-if="tipoReporte === 'todos' || tipoReporte === 'paquetes'"
                        class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Paquetes Vendidos</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                                <thead class="bg-gray-50 dark:bg-slate-700/50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Usuario</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Paquete</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Precio</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                                    <tr v-for="(item, index) in reportes.paquetesAdquiridos" :key="index">
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{
                                            item.usuario }}</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{
                                            item.paquete }}</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                            {{ formatCurrency(item.precio) }}</td>
                                    </tr>
                                    <tr v-if="reportes.paquetesAdquiridos.length === 0">
                                        <td colspan="3"
                                            class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No hay datos disponibles</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
