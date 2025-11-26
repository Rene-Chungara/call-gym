<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    rutinas: Object,
    filters: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const canManage = computed(() => user.value.is_propietario || user.value.is_instructor);

const search = ref(props.filters?.search || '');

const searchRutinas = () => {
    router.get(route('rutinas.index'), { search: search.value }, {
        preserveState: true,
        replace: true,
    });
};

const deleteRutina = (id) => {
    if (confirm('¿Estás seguro de eliminar esta rutina?')) {
        router.delete(route('rutinas.destroy', id));
    }
};
</script>

<template>

    <Head title="Rutinas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">
                    {{ canManage ? 'Gestión de Rutinas' : 'Mis Rutinas' }}
                </h2>
                <Link v-if="canManage" :href="route('rutinas.create')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-medium rounded-lg shadow-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Rutina
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Search Bar -->
                <div
                    class="mb-6 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-4">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <input v-model="search" @keyup.enter="searchRutinas" type="text"
                                placeholder="Buscar por nombre, descripción o usuario..."
                                class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-slate-500 dark:bg-slate-900 dark:text-white" />
                        </div>
                        <button @click="searchRutinas"
                            class="px-6 py-2 bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-lg transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Buscar
                        </button>
                    </div>
                </div>

                <!-- Table View -->
                <div
                    class="bg-white dark:bg-slate-800 shadow-sm border border-gray-200 dark:border-slate-700 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                            <thead class="bg-gray-50 dark:bg-slate-700/50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Rutina
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Usuario Asignado
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Descripción
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                                <tr v-for="rutina in rutinas.data" :key="rutina.id"
                                    class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-10 w-10 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ rutina.nombre }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ rutina.usuario?.nombre || 'Sin asignar' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                            {{ rutina.descripcion || 'Sin descripción' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link :href="route('rutinas.show', rutina.id)"
                                                class="text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200"
                                                title="Ver detalles">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            </Link>
                                            <Link v-if="canManage" :href="route('rutinas.edit', rutina.id)"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200"
                                                title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            </Link>
                                            <button v-if="canManage" @click="deleteRutina(rutina.id)"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200"
                                                title="Eliminar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-if="rutinas.data.length === 0" class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                            No hay rutinas
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            {{ canManage ? 'Comienza creando una nueva rutina' : 'Aún no tienes rutinas asignadas' }}
                        </p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="rutinas.links.length > 3"
                        class="bg-gray-50 dark:bg-slate-700/50 px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Mostrando <span class="font-medium">{{ rutinas.from }}</span> a <span
                                    class="font-medium">{{
                                        rutinas.to }}</span> de <span class="font-medium">{{ rutinas.total }}</span>
                                resultados
                            </div>
                            <div class="flex gap-2">
                                <template v-for="(link, index) in rutinas.links" :key="index">
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
