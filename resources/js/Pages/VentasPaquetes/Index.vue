<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    ventas: Object,
});

const ventasConDatos = computed(() => {
    return props.ventas.data?.map(item => ({
        ...item,
        fechaFormato: formatFecha(item.fecha_compra),
    })) || [];
});

function formatFecha(fecha) {
    if (!fecha) return "";
    const d = new Date(fecha);
    return `${String(d.getDate()).padStart(2, "0")}-${String(d.getMonth() + 1).padStart(2, "0")}-${d.getFullYear()}`;
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                Ventas de Paquetes
            </h2>
        </template>

        <div class="max-w-7xl mx-auto mt-6 px-4">

            <div class="flex justify-end mb-6">
                <Link
                    :href="route('venta-paquetes.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600"
                >
                    Registrar Venta
                </Link>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-100 dark:bg-slate-700 border-b border-gray-200 dark:border-slate-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Cliente</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Paquete</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Sesiones</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Restantes</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Fecha</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                        <tr v-for="item in ventasConDatos" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                {{ item.usuario.nombre }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ item.paquete.nombre }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                {{ item.paquete.num_sesiones }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-bold"
                                    :class="{
                                        'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-100': item.sesiones_restantes > 0,
                                        'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-100': item.sesiones_restantes === 0,
                                    }"
                                >
                                    {{ item.sesiones_restantes }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ item.fechaFormato }}
                            </td>
                            <td class="px-6 py-4 text-sm flex gap-2">
                                <Link
                                    :href="route('venta-paquetes.edit', item.id)"
                                    class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium"
                                >
                                    Editar
                                </Link>
                                <Link
                                    :href="route('venta-paquetes.show', item.id)"
                                    class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium"
                                >
                                    Ver
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="ventas.meta?.links" class="mt-8 flex justify-center gap-2">
                <Link
                    v-for="link in ventas.meta.links"
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
