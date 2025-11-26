<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    paquetes: Object,
});

const paquetesConDatos = computed(() => {
    return props.paquetes.data?.map(item => ({
        ...item,
    })) || [];
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                Paquetes de Sesiones
            </h2>
        </template>

        <div class="max-w-7xl mx-auto mt-6 px-4">

            <div class="flex justify-end mb-6">
                <Link
                    :href="route('paquetes.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600"
                >
                    Crear Paquete
                </Link>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <div
                    v-for="item in paquetesConDatos"
                    :key="item.id"
                    class="rounded-lg bg-white dark:bg-slate-800 shadow-md border border-gray-200 dark:border-slate-700 p-6 hover:shadow-lg transition"
                >
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            {{ item.nombre }}
                        </h3>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Sesiones:</span>
                            <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                {{ item.num_sesiones }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Precio:</span>
                            <span class="text-lg font-bold text-green-600 dark:text-green-400">
                                Bs. {{ parseFloat(item.precio).toFixed(2) }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-slate-700">
                            <span class="text-xs text-gray-600 dark:text-gray-400">Por sesión:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                Bs. {{ (item.precio / item.num_sesiones).toFixed(2) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex gap-2 justify-end">
                        <Link
                            :href="route('paquetes.edit', item.id)"
                            class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600"
                        >
                            Editar
                        </Link>

                        <Link
                            :href="route('paquetes.show', item.id)"
                            class="px-3 py-1 text-sm text-indigo-600 border border-indigo-600 rounded hover:bg-indigo-50 dark:text-indigo-400 dark:border-indigo-400 dark:hover:bg-slate-700"
                        >
                            Ver
                        </Link>
                    </div>
                </div>
            </div>

            <div v-if="paquetes.meta?.links" class="mt-8 flex justify-center gap-2">
                <Link
                    v-for="link in paquetes.meta.links"
                    :key="link.label"
                    :href="link.url || ''"
                    v-html="link.label"
                    class="px-3 py-1 rounded text-sm"
                    :class="{
                        'bg-indigo-600 text-white': link.active,
                        'bg-gray-200 dark:bg-slate-700 dark:text-gray-200': !link.active,
                    }"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
