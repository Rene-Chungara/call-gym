<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    suscripciones: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const canManage = computed(() => user.value.is_propietario || user.value.is_secretaria);

const deleteSuscripcion = (id) => {
    if (confirm('¿Estás seguro de eliminar esta suscripción?')) {
        router.delete(route('suscripciones.destroy', id));
    }
};

const getEstadoBadge = (estado) => {
    // estado es 1 (activo) o 0 (inactivo)
    const badges = {
        1: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800',
        0: 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
    };
    return badges[estado] || 'bg-gray-50 text-gray-700 border-gray-200';
};

const getEstadoTexto = (estado) => {
    return estado === 1 ? 'Activa' : 'Inactiva';
};

const getDiasRestantes = (fechaFin) => {
    if (!fechaFin) return 0;
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    const fin = new Date(fechaFin);
    fin.setHours(0, 0, 0, 0);
    const diff = Math.ceil((fin - hoy) / (1000 * 60 * 60 * 24));
    return diff;
};
</script>

<template>

    <Head title="Suscripciones" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">
                    {{ canManage ? 'Gestión de Suscripciones' : 'Mi Suscripción' }}
                </h2>
                <Link v-if="canManage" :href="route('suscripciones.create')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-medium rounded-lg shadow-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Suscripción
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Table View -->
                <div
                    class="bg-white dark:bg-slate-800 shadow-sm border border-gray-200 dark:border-slate-700 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                            <thead class="bg-gray-50 dark:bg-slate-700/50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Usuario
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Membresía
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Periodo
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Días Restantes
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                                <tr v-for="suscripcion in suscripciones.data" :key="suscripcion.id"
                                    class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-10 w-10 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
                                                <span class="text-slate-700 dark:text-slate-300 font-semibold text-sm">
                                                    {{ suscripcion.usuario.nombre.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ suscripcion.usuario.nombre }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ suscripcion.usuario.email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ suscripcion.membresia.nombre }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ new Date(suscripcion.fecha_inicio).toLocaleDateString('es-ES') }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ new Date(suscripcion.fecha_fin).toLocaleDateString('es-ES') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="['px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border', getEstadoBadge(suscripcion.estado)]">
                                            {{ getEstadoTexto(suscripcion.estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div v-if="suscripcion.estado === 1" class="text-sm">
                                            <span :class="[
                                                'font-semibold',
                                                getDiasRestantes(suscripcion.fecha_fin) > 15 ? 'text-emerald-600 dark:text-emerald-400' :
                                                    getDiasRestantes(suscripcion.fecha_fin) > 7 ? 'text-amber-600 dark:text-amber-400' :
                                                        'text-red-600 dark:text-red-400'
                                            ]">
                                                {{ getDiasRestantes(suscripcion.fecha_fin) }} días
                                            </span>
                                        </div>
                                        <div v-else class="text-sm text-gray-400 dark:text-gray-500">
                                            -
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link :href="route('suscripciones.show', suscripcion.id)"
                                                class="text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200"
                                                title="Ver detalles">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            </Link>
                                            <Link v-if="canManage" :href="route('suscripciones.edit', suscripcion.id)"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200"
                                                title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-if="suscripciones.data.length === 0" class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                            No hay suscripciones
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            {{ canManage ? 'Comienza creando una nueva suscripción' : 'Aún no tienes una suscripción activa' }}
                        </p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="suscripciones.links.length > 3"
                        class="bg-gray-50 dark:bg-slate-700/50 px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Mostrando <span class="font-medium">{{ suscripciones.from }}</span> a <span
                                    class="font-medium">{{ suscripciones.to }}</span> de <span class="font-medium">{{
                                        suscripciones.total }}</span> resultados
                            </div>
                            <div class="flex gap-2">
                                <template v-for="(link, index) in suscripciones.links" :key="index">
                                    <Link v-if="link.url" :href="link.url" v-html="link.label" :class="[
                                        'px-3 py-2 text-sm rounded-lg transition',
                                        link.active
                                            ? 'bg-slate-800 text-white dark:bg-slate-600'
                                            : 'bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 border border-gray-300 dark:border-slate-600'
                                    ]" />
                                    <span v-else v-html="link.label" :class="[
                                        'px-3 py-2 text-sm rounded-lg transition opacity-50 cursor-not-allowed',
                                        'bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-slate-600'
                                    ]"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
