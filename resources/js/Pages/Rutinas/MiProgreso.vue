<script setup>
import { Link, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { computed } from "vue";

const props = defineProps({
    rutina: Object,
    sesionesCompletadas: Array,
});

const sesionCompletada = (sesionId) => {
    return props.sesionesCompletadas.includes(sesionId);
};

const progreso = computed(() => {
    if (!props.rutina || !props.rutina.sesiones) return 0;
    const total = props.rutina.sesiones.length;
    const completadas = props.sesionesCompletadas.length;
    return total > 0 ? Math.round((completadas / total) * 100) : 0;
});
</script>

<template>
    <AuthenticatedLayout>

        <Head title="Mi Progreso" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">
                Mi Progreso de Entrenamiento
            </h2>
        </template>

        <div class="py-8 bg-gray-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Sin Rutina -->
                <div v-if="!rutina"
                    class="bg-white dark:bg-slate-800 shadow-sm border border-gray-200 dark:border-slate-700 rounded-lg p-12 text-center">
                    <svg class="w-20 h-20 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        No tienes una rutina asignada
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Contacta con tu entrenador para que te asigne una rutina personalizada
                    </p>
                </div>

                <!-- Con Rutina -->
                <template v-else>
                    <!-- Header con Progreso -->
                    <div
                        class="bg-white dark:bg-slate-800 shadow-sm border border-gray-200 dark:border-slate-700 rounded-lg p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                    {{ rutina.nombre }}
                                </h1>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ rutina.descripcion }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-4xl font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ progreso }}%
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ sesionesCompletadas.length }} de {{ rutina.sesiones.length }} completadas
                                </div>
                            </div>
                        </div>

                        <!-- Barra de Progreso -->
                        <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-3 rounded-full transition-all duration-500"
                                :style="{ width: progreso + '%' }"></div>
                        </div>
                    </div>

                    <!-- Sesiones -->
                    <div class="space-y-4">
                        <div v-for="sesion in rutina.sesiones" :key="sesion.id" :class="[
                            'bg-white dark:bg-slate-800 shadow-sm border rounded-lg overflow-hidden transition',
                            sesionCompletada(sesion.id)
                                ? 'border-emerald-300 dark:border-emerald-700'
                                : 'border-gray-200 dark:border-slate-700'
                        ]">

                            <!-- Sesión Header -->
                            <div :class="[
                                'px-6 py-4 border-b flex items-center justify-between',
                                sesionCompletada(sesion.id)
                                    ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800'
                                    : 'bg-gray-50 dark:bg-slate-700/50 border-gray-200 dark:border-slate-700'
                            ]">
                                <div class="flex items-center gap-3">
                                    <div :class="[
                                        'flex items-center justify-center w-10 h-10 rounded-lg font-bold text-sm shadow-sm',
                                        sesionCompletada(sesion.id)
                                            ? 'bg-emerald-600 text-white'
                                            : 'bg-gray-300 dark:bg-slate-600 text-gray-700 dark:text-gray-300'
                                    ]">
                                        {{ sesion.numero_sesion }}
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ sesion.descripcion }}
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            {{ sesion.rutina_sesion_ejercicios.length }} ejercicio(s)
                                        </p>
                                    </div>
                                </div>

                                <!-- Estado -->
                                <div v-if="sesionCompletada(sesion.id)"
                                    class="flex items-center gap-2 text-emerald-700 dark:text-emerald-300">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="font-semibold text-sm">Completada</span>
                                </div>
                                <div v-else class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-semibold text-sm">Pendiente</span>
                                </div>
                            </div>

                            <!-- Ejercicios -->
                            <div class="p-6">
                                <div class="space-y-3">
                                    <div v-for="ejercicioData in sesion.rutina_sesion_ejercicios"
                                        :key="ejercicioData.id"
                                        class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg border border-gray-200 dark:border-slate-600">

                                        <!-- Orden -->
                                        <div class="flex-shrink-0">
                                            <div
                                                class="flex items-center justify-center w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-lg font-semibold text-sm">
                                                {{ ejercicioData.orden }}
                                            </div>
                                        </div>

                                        <!-- Info -->
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                {{ ejercicioData.ejercicio.nombre }}
                                            </h4>

                                            <!-- Stats -->
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                                <div class="text-center">
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Series
                                                    </div>
                                                    <div class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                                        {{ ejercicioData.series }}
                                                    </div>
                                                </div>

                                                <div class="text-center">
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Reps
                                                    </div>
                                                    <div class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                                        {{ ejercicioData.repeticiones }}
                                                    </div>
                                                </div>

                                                <div class="text-center">
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Peso (kg)
                                                    </div>
                                                    <div
                                                        class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                                                        {{ ejercicioData.peso_estimado ?? '-' }}
                                                    </div>
                                                </div>

                                                <div class="text-center">
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Descanso
                                                    </div>
                                                    <div class="text-lg font-bold text-amber-600 dark:text-amber-400">
                                                        {{ ejercicioData.descanso_segundos ?? '-' }}<span
                                                            class="text-xs">s</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Notas -->
                                            <div v-if="ejercicioData.notas"
                                                class="mt-3 p-2 bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-400 rounded text-sm text-amber-800 dark:text-amber-200">
                                                {{ ejercicioData.notas }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
