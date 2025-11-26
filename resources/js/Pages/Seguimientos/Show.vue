<script setup>
import { Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    seguimiento: Object,
});

function formatFecha(fecha) {
    if (!fecha) return "";
    const d = new Date(fecha);
    return `${String(d.getDate()).padStart(2, "0")}-${String(d.getMonth() + 1).padStart(2, "0")}-${d.getFullYear()}`;
}

function confirmarEliminar() {
    if (confirm("¿Estás seguro de que deseas eliminar este seguimiento?")) {
        const form = new FormData();
        form.append("_method", "DELETE");
        fetch(route("seguimientos.destroy", props.seguimiento.id), {
            method: "POST",
            body: form,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
        }).then(() => {
            window.location.href = route("seguimientos.index");
        });
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    Detalle de Seguimiento
                </h2>
                <Link
                    :href="route('seguimientos.index')"
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
                            {{ props.seguimiento.usuario.nombre }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            Fecha: {{ formatFecha(props.seguimiento.fecha) }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 dark:bg-slate-700 p-6 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Peso</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                {{ props.seguimiento.peso }} kg
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-slate-700 p-6 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Medidas</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                {{ props.seguimiento.medidas }} cm
                            </p>
                        </div>
                    </div>

                    <div v-if="props.seguimiento.observaciones" class="bg-gray-50 dark:bg-slate-700 p-6 rounded-lg mb-6">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                            Observaciones
                        </h3>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                            {{ props.seguimiento.observaciones }}
                        </p>
                    </div>

                    <div class="flex gap-3 justify-end">
                        <Link
                            :href="route('seguimientos.edit', props.seguimiento.id)"
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
