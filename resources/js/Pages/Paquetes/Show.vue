<script setup>
import { Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    paquete: Object,
});

function confirmarEliminar() {
    if (confirm("¿Estás seguro de que deseas eliminar este paquete?")) {
        const form = new FormData();
        form.append("_method", "DELETE");
        fetch(route("paquetes.destroy", props.paquete.id), {
            method: "POST",
            body: form,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
        }).then(() => {
            window.location.href = route("paquetes.index");
        });
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    Detalle de Paquete
                </h2>
                <Link
                    :href="route('paquetes.index')"
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
                            {{ props.paquete.nombre }}
                        </h1>
                    </div>

                    <div class="grid grid-cols-3 gap-6 mb-6">
                        <div class="bg-indigo-50 dark:bg-slate-700 p-6 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Sesiones</p>
                            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                                {{ props.paquete.num_sesiones }}
                            </p>
                        </div>

                        <div class="bg-green-50 dark:bg-slate-700 p-6 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Precio Total</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                                Bs. {{ parseFloat(props.paquete.precio).toFixed(2) }}
                            </p>
                        </div>

                        <div class="bg-blue-50 dark:bg-slate-700 p-6 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Por Sesión</p>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                Bs. {{ (props.paquete.precio / props.paquete.num_sesiones).toFixed(2) }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end">
                        <Link
                            :href="route('paquetes.edit', props.paquete.id)"
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
