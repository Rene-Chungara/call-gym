<script setup>
import { computed, ref, watch } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    asistencias: Object,
    filters: Object, // Recibir filtros desde el backend si se pasan (opcional, pero buena práctica)
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const canManage = computed(() => {
    return user.value.is_propietario || user.value.is_instructor || user.value.is_secretaria;
});

const asistenciasList = computed(() => {
    return props.asistencias.data || [];
});

// Filtros reactivos
const search = ref('');
const fecha = ref('');

// Custom debounce function to avoid lodash dependency
const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

// Función de búsqueda con debounce
const handleSearch = debounce(() => {
    router.get(route('asistencia-sesion.index'), {
        search: search.value,
        fecha: fecha.value
    }, {
        preserveState: true,
        replace: true
    });
}, 300);

// Watch para fecha
watch(fecha, () => {
    handleSearch();
});

const getEstadoColor = (estado) => {
    switch (estado) {
        case 'asistio': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'no_asistio': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        case 'cancelada': return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                Asistencia a Sesiones
            </h2>
        </template>

        <div class="max-w-7xl mx-auto mt-6 px-4">

            <!-- Barra de Herramientas (Filtros y Botón) -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">

                <!-- Filtros -->
                <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                    <input v-model="search" @input="handleSearch" type="text" placeholder="Buscar usuario..."
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-slate-900 dark:border-slate-700 dark:text-white w-full sm:w-64">

                    <input v-model="fecha" type="date"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-slate-900 dark:border-slate-700 dark:text-white w-full sm:w-auto">
                </div>

                <!-- Botón Registrar -->
                <div v-if="canManage">
                    <Link :href="route('asistencia-sesion.create')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Registrar Asistencia
                    </Link>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-800 shadow-md rounded-lg overflow-hidden border border-gray-200 dark:border-slate-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-700">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Usuario
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Rutina / Sesión
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Horario
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                            <tr v-if="asistenciasList.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-12 w-12 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        No se encontraron registros de asistencia.
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="item in asistenciasList" :key="item.id"
                                class="hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ item.fecha_asistencia }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-medium">
                                    {{ item.venta_paquete?.usuario?.nombre || 'Usuario Eliminado' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <div v-if="item.rutina_sesion" class="flex flex-col">
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{
                                            item.rutina_sesion.rutina?.nombre }}</span>
                                        <span class="text-xs">Sesión {{ item.rutina_sesion.numero_sesion }}</span>
                                    </div>
                                    <div v-else>
                                        Sesión General #{{ item.numero_sesion }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <div v-if="item.hora_entrada">
                                        {{ item.hora_entrada }} <span v-if="item.hora_salida">- {{ item.hora_salida
                                            }}</span>
                                    </div>
                                    <div v-else class="text-gray-400 italic">
                                        -
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize', getEstadoColor(item.estado)]">
                                        {{ item.estado.replace('_', ' ') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link :href="route('asistencia-sesion.show', item.id)"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                    Ver
                                    </Link>
                                    <Link v-if="canManage" :href="route('asistencia-sesion.edit', item.id)"
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                    Editar
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="asistencias.meta?.links"
                    class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex justify-center gap-2">
                    <Link v-for="link in asistencias.meta.links" :key="link.label" :href="link.url || ''"
                        v-html="link.label" class="px-3 py-1 rounded text-sm transition" :class="{
                            'bg-indigo-600 text-white': link.active,
                            'bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:text-gray-200 dark:hover:bg-slate-600': !link.active,
                            'opacity-50 cursor-not-allowed': !link.url
                        }" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
