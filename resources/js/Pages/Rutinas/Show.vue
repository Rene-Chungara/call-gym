<script setup>
import { Link, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    rutina: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const canManage = computed(() => {
    return user.value.is_propietario || user.value.is_instructor || user.value.is_secretaria;
});

const getDificultadColor = (dificultad) => {
    switch (dificultad) {
        case 'principiante': return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800';
        case 'intermedio': return 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800';
        case 'avanzado': return 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-800';
        default: return 'bg-gray-50 text-gray-700 border-gray-200';
    }
};
</script>

<template>
    <AuthenticatedLayout>

        <Head :title="'Rutina: ' + rutina.nombre" />

        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">
                    Detalles de Rutina
                </h2>
                <div class="flex gap-3" v-if="canManage">
                    <Link :href="route('rutinas.edit', rutina.id)"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8 bg-gray-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Header Card -->
                <div
                    class="bg-white dark:bg-slate-800 shadow-sm border border-gray-200 dark:border-slate-700 rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                            {{ rutina.nombre }}
                                        </h1>
                                        <div
                                            class="flex items-center gap-2 mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span class="font-medium">{{ rutina.usuario.nombre }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg p-4">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                        {{ rutina.descripcion }}
                                    </p>
                                </div>
                            </div>

                            <Link :href="route('rutinas.index')"
                                class="ml-4 inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-200 rounded-lg transition text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Sesiones -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Sesiones de Entrenamiento
                        </h2>
                        <span
                            class="px-3 py-1 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-full text-sm font-medium">
                            {{ rutina.sesiones.length }} sesión(es)
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div v-for="sesion in rutina.sesiones" :key="sesion.id"
                            class="bg-white dark:bg-slate-800 shadow-sm border border-gray-200 dark:border-slate-700 rounded-lg overflow-hidden">

                            <!-- Sesión Header -->
                            <div
                                class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 border-b border-gray-200 dark:border-slate-700 px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex items-center justify-center w-10 h-10 bg-indigo-600 text-white rounded-lg font-bold text-sm shadow-sm">
                                            {{ sesion.numero_sesion }}
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ sesion.descripcion }}
                                            </h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                                {{ sesion.rutina_sesion_ejercicios.length }} ejercicio(s)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ejercicios -->
                            <div class="p-6">
                                <div class="space-y-3">
                                    <div v-for="ejercicioData in sesion.rutina_sesion_ejercicios"
                                        :key="ejercicioData.id"
                                        class="group hover:bg-gray-50 dark:hover:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg p-4 transition">

                                        <div class="flex items-start gap-4">
                                            <!-- Orden -->
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="flex items-center justify-center w-8 h-8 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg font-semibold text-sm border border-gray-300 dark:border-slate-600">
                                                    {{ ejercicioData.orden }}
                                                </div>
                                            </div>

                                            <!-- Info -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-4 mb-3">
                                                    <div class="flex-1">
                                                        <h4
                                                            class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                            {{ ejercicioData.ejercicio.nombre }}
                                                        </h4>
                                                        <div class="flex flex-wrap gap-2">
                                                            <span
                                                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-md border border-blue-200 dark:border-blue-800">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                                </svg>
                                                                {{ ejercicioData.ejercicio.grupo_muscular }}
                                                            </span>
                                                            <span
                                                                :class="['inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-md border', getDificultadColor(ejercicioData.ejercicio.dificultad)]">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                                </svg>
                                                                {{ ejercicioData.ejercicio.dificultad }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Stats Grid -->
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                                    <div
                                                        class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg p-3">
                                                        <div
                                                            class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                                            </svg>
                                                            Series
                                                        </div>
                                                        <p
                                                            class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                                            {{ ejercicioData.series }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg p-3">
                                                        <div
                                                            class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                            </svg>
                                                            Reps
                                                        </div>
                                                        <p
                                                            class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                                            {{ ejercicioData.repeticiones }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg p-3">
                                                        <div
                                                            class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                                            </svg>
                                                            Peso (kg)
                                                        </div>
                                                        <p
                                                            class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                                                            {{ ejercicioData.peso_estimado ?? '-' }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg p-3">
                                                        <div
                                                            class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            Descanso
                                                        </div>
                                                        <p class="text-lg font-bold text-amber-600 dark:text-amber-400">
                                                            {{ ejercicioData.descanso_segundos ?? '-' }}<span
                                                                class="text-xs font-normal">s</span>
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Notas -->
                                                <div v-if="ejercicioData.notas"
                                                    class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                                                    <div class="flex items-start gap-2">
                                                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <p class="text-sm text-amber-800 dark:text-amber-200">
                                                            {{ ejercicioData.notas }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="sesion.rutina_sesion_ejercicios.length === 0"
                                        class="text-center py-12 text-gray-500 dark:text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="text-sm">No hay ejercicios en esta sesión</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="rutina.sesiones.length === 0"
                            class="bg-white dark:bg-slate-800 shadow-sm border border-gray-200 dark:border-slate-700 rounded-lg p-12 text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">No hay sesiones en esta rutina</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
