<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import { Chart as ChartJS, ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement, LineElement, PointElement, Title } from 'chart.js';
import { Doughnut, Bar, Line } from 'vue-chartjs';

// Registrar componentes de Chart.js
ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement, LineElement, PointElement, Title);

const page = usePage();
const user = computed(() => page.props.auth.user);

const props = defineProps({
    stats: Object,
    charts: Object,
});

// Determinar mensaje de bienvenida según rol
const welcomeMessage = computed(() => {
    if (user.value.is_propietario) return 'Panel de Control - Propietario';
    if (user.value.is_instructor) return 'Panel de Instructor';
    if (user.value.is_secretaria) return 'Panel de Secretaría';
    if (user.value.is_clientes) return 'Mi Panel Personal';
    return 'Dashboard';
});

// Configuración de gráficos
const membresiasChartData = computed(() => {
    if (!props.charts?.membresiasPopulares) return null;
    return {
        labels: props.charts.membresiasPopulares.map(m => m.nombre),
        datasets: [{
            label: 'Suscripciones',
            data: props.charts.membresiasPopulares.map(m => m.total),
            backgroundColor: [
                'rgba(99, 102, 241, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)',
            ],
        }]
    };
});

const asistenciasChartData = computed(() => {
    if (!props.charts?.asistenciasUltimos7Dias) return null;
    return {
        labels: props.charts.asistenciasUltimos7Dias.map(a => new Date(a.fecha).toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric' })),
        datasets: [{
            label: 'Asistencias',
            data: props.charts.asistenciasUltimos7Dias.map(a => a.total),
            borderColor: 'rgb(99, 102, 241)',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            tension: 0.4,
        }]
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'bottom',
        }
    }
};
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                {{ welcomeMessage }}
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Mensaje de Bienvenida -->
                <div
                    class="bg-gradient-to-r from-slate-700 to-slate-900 dark:from-slate-800 dark:to-slate-950 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold mb-2">
                                ¡Bienvenido, {{ user.nombre || user.name }}!
                            </h1>
                            <p class="text-slate-200 dark:text-slate-300">
                                <template v-if="user.is_propietario">
                                    Gestiona tu gimnasio desde aquí
                                </template>
                                <template v-else-if="user.is_instructor">
                                    Administra rutinas y asistencias de tus clientes
                                </template>
                                <template v-else-if="user.is_secretaria">
                                    Gestiona membresías y suscripciones
                                </template>
                                <template v-else-if="user.is_clientes">
                                    Revisa tu progreso y suscripción
                                </template>
                            </p>
                        </div>
                        <div class="hidden md:block">
                            <svg class="w-20 h-20 text-white opacity-30" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- ESTADÍSTICAS PARA PROPIETARIO -->
                <template v-if="user.is_propietario && stats">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        <!-- Total Usuarios -->
                        <div
                            class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Usuarios</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                                        {{ stats.totalUsuarios || 0 }}
                                    </p>
                                </div>
                                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Suscripciones Activas -->
                        <div
                            class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Suscripciones</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                                        {{ stats.suscripcionesActivas || 0 }}
                                    </p>
                                </div>
                                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                    <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Total Rutinas -->
                        <div
                            class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Rutinas</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                                        {{ stats.totalRutinas || 0 }}
                                    </p>
                                </div>
                                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Asistencias Hoy -->
                        <div
                            class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Asistencias Hoy</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                                        {{ stats.asistenciasHoy || 0 }}
                                    </p>
                                </div>
                                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                                    <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Total Ingresos -->
                        <div
                            class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Ingresos</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                                        ${{ (stats.totalIngresos || 0).toLocaleString() }}
                                    </p>
                                </div>
                                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos -->
                    <div v-if="charts" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Membresías Populares -->
                        <div
                            class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                Membresías Más Populares
                            </h3>
                            <div class="h-64">
                                <Doughnut v-if="membresiasChartData" :data="membresiasChartData"
                                    :options="chartOptions" />
                            </div>
                        </div>

                        <!-- Asistencias Últimos 7 Días -->
                        <div
                            class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                Asistencias Últimos 7 Días
                            </h3>
                            <div class="h-64">
                                <Line v-if="asistenciasChartData" :data="asistenciasChartData"
                                    :options="chartOptions" />
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Exportación -->
                    <div
                        class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Exportar Reportes
                        </h3>
                        <div class="flex gap-4">
                            <button
                                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Exportar PDF
                            </button>
                            <button
                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Exportar Excel
                            </button>
                        </div>
                    </div>
                </template>

                <!-- ACCESOS RÁPIDOS PARA TODOS -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Accesos Rápidos
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Propietario -->
                        <template v-if="user.is_propietario">
                            <a :href="route('usuarios.index')"
                                class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition border border-gray-200 dark:border-slate-600">
                                <svg class="w-6 h-6 text-slate-600 dark:text-slate-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span class="font-medium text-gray-900 dark:text-gray-100">Gestionar Usuarios</span>
                            </a>

                            <a :href="route('suscripciones.index')"
                                class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition border border-gray-200 dark:border-slate-600">
                                <svg class="w-6 h-6 text-slate-600 dark:text-slate-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium text-gray-900 dark:text-gray-100">Ver Suscripciones</span>
                            </a>

                            <a :href="route('rutinas.index')"
                                class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition border border-gray-200 dark:border-slate-600">
                                <svg class="w-6 h-6 text-slate-600 dark:text-slate-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="font-medium text-gray-900 dark:text-gray-100">Gestionar Rutinas</span>
                            </a>
                        </template>

                        <!-- Instructor -->
                        <template v-else-if="user.is_instructor">
                            <a :href="route('rutinas.index')"
                                class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition border border-gray-200 dark:border-slate-600">
                                <svg class="w-6 h-6 text-slate-600 dark:text-slate-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="font-medium text-gray-900 dark:text-gray-100">Mis Rutinas</span>
                            </a>

                            <a :href="route('asistencia-sesion.create')"
                                class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition border border-gray-200 dark:border-slate-600">
                                <svg class="w-6 h-6 text-slate-600 dark:text-slate-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="font-medium text-gray-900 dark:text-gray-100">Registrar Asistencia</span>
                            </a>

                            <a :href="route('ejercicios.index')"
                                class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition border border-gray-200 dark:border-slate-600">
                                <svg class="w-6 h-6 text-slate-600 dark:text-slate-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span class="font-medium text-gray-900 dark:text-gray-100">Ver Ejercicios</span>
                            </a>
                        </template>

                        <!-- Secretaria -->
                        <template v-else-if="user.is_secretaria">
                            <a :href="route('suscripciones.index')"
                                class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition border border-gray-200 dark:border-slate-600">
                                <svg class="w-6 h-6 text-slate-600 dark:text-slate-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium text-gray-900 dark:text-gray-100">Gestionar
                                    Suscripciones</span>
                            </a>

                            <a :href="route('membresias.index')"
                                class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition border border-gray-200 dark:border-slate-600">
                                <svg class="w-6 h-6 text-slate-600 dark:text-slate-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <span class="font-medium text-gray-900 dark:text-gray-100">Ver Membresías</span>
                            </a>

                            <a :href="route('venta-paquetes.index')"
                                class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition border border-gray-200 dark:border-slate-600">
                                <svg class="w-6 h-6 text-slate-600 dark:text-slate-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <span class="font-medium text-gray-900 dark:text-gray-100">Ventas de Paquetes</span>
                            </a>
                        </template>

                        <!-- Cliente -->
                        <template v-else-if="user.is_clientes">
                            <a :href="route('membresias.catalogo')"
                                class="flex items-center gap-3 p-4 bg-gradient-to-r from-slate-700 to-slate-900 dark:from-slate-800 dark:to-slate-950 text-white rounded-lg hover:from-slate-800 hover:to-slate-950 dark:hover:from-slate-900 dark:hover:to-black transition shadow-lg hover:shadow-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <span class="font-semibold">Comprar Membresía</span>
                            </a>

                            <a :href="route('rutinas.mi-progreso')"
                                class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition border border-gray-200 dark:border-slate-600">
                                <svg class="w-6 h-6 text-slate-600 dark:text-slate-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span class="font-medium text-gray-900 dark:text-gray-100">Ver Mi Progreso</span>
                            </a>

                            <a :href="route('suscripciones.index')"
                                class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition border border-gray-200 dark:border-slate-600">
                                <svg class="w-6 h-6 text-slate-600 dark:text-slate-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium text-gray-900 dark:text-gray-100">Mi Suscripción</span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
