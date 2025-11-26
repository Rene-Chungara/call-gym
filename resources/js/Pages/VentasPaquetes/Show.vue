<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    venta: Object,
});

function formatFecha(fecha) {
    if (!fecha) return "";
    const d = new Date(fecha);
    return `${String(d.getDate()).padStart(2, "0")}-${String(d.getMonth() + 1).padStart(2, "0")}-${d.getFullYear()}`;
}

const porcentajeUsado = computed(() => {
    const total = props.venta.paquete.num_sesiones;
    const usado = total - props.venta.sesiones_restantes;
    return Math.floor((usado / total) * 100);
});

function confirmarEliminar() {
    if (confirm("¿Estás seguro de que deseas eliminar esta venta?")) {
        const form = new FormData();
        form.append("_method", "DELETE");
        fetch(route("venta-paquetes.destroy", props.venta.id), {
            method: "POST",
            body: form,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
        }).then(() => {
            window.location.href = route("venta-paquetes.index");
        });
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    Detalle de Venta
                </h2>
                <Link
                    :href="route('venta-paquetes.index')"
                    class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm"
                >
                    Volver
                </Link>
            </div>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-8 mb-6">

                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            {{ props.venta.usuario.nombre }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            Paquete: {{ props.venta.paquete.nombre }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div class="bg-blue-50 dark:bg-slate-700 p-6 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Sesiones Totales</p>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                {{ props.venta.paquete.num_sesiones }}
                            </p>
                        </div>

                        <div class="bg-green-50 dark:bg-slate-700 p-6 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Sesiones Restantes</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                                {{ props.venta.sesiones_restantes }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-slate-700 p-6 rounded-lg mb-6">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Progreso de Uso</h3>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Utilización</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ porcentajeUsado }}%</span>
                        </div>
                        <div class="w-full h-3 bg-gray-200 dark:bg-slate-600 rounded-full overflow-hidden">
                            <div
                                class="h-full bg-indigo-600 transition-all duration-500"
                                :style="{ width: porcentajeUsado + '%' }"
                            ></div>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                            {{ props.venta.paquete.num_sesiones - props.venta.sesiones_restantes }} de {{ props.venta.paquete.num_sesiones }} sesiones utilizadas
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 dark:bg-slate-700 p-6 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Precio Total</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                Bs. {{ parseFloat(props.venta.paquete.precio).toFixed(2) }}
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-slate-700 p-6 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Fecha de Compra</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ formatFecha(props.venta.fecha_compra) }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end">
                        <Link
                            :href="route('venta-paquetes.edit', props.venta.id)"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-sm font-medium"
                        >
                            Editar
                        </Link>

                        <button
                            @click="confirmarEliminar"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600 text-sm font-medium"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
