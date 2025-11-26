<script setup>
import { computed } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    asistencia: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const canManage = computed(() => {
    return user.value.is_propietario || user.value.is_instructor;
});

const deleteAsistencia = () => {
    if (confirm("¿Estás seguro de eliminar este registro de asistencia?")) {
        router.delete(route("asistencia-sesion.destroy", props.asistencia.id));
    }
};

const getEstadoColor = (estado) => {
    switch (estado) {
        case 'asistio': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'no_asistio': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        case 'cancelada': return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const formatTime = (time) => {
    if (!time) return '-';
    return time.substring(0, 5);
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                Detalle de Asistencia
            </h2>
        </template>

        <div class="max-w-4xl mx-auto mt-6 px-4">
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                {{ asistencia.venta_paquete?.usuario?.nombre || 'Usuario' }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Paquete: {{ asistencia.venta_paquete?.paquete?.nombre || 'N/A' }}
                            </p>
                        </div>
                        <div class="flex gap-2" v-if="canManage">
                            <Link :href="route('asistencia-sesion.edit', asistencia.id)"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600">
                            Editar
                            </Link>
                            <DangerButton @click="deleteAsistencia">
                                Eliminar
                            </DangerButton>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Fecha y Hora
                            </h4>
                            <p class="mt-1 text-lg text-gray-900 dark:text-gray-200">
                                {{ asistencia.fecha_asistencia }} <br>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ formatTime(asistencia.hora_entrada) }} - {{ formatTime(asistencia.hora_salida) }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Estado
                            </h4>
                            <div class="mt-1">
                                <span
                                    :class="['px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full capitalize', getEstadoColor(asistencia.estado)]">
                                    {{ asistencia.estado.replace('_', ' ') }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Rutina / Sesión
                            </h4>
                            <p class="mt-1 text-lg text-gray-900 dark:text-gray-200">
                                <span v-if="asistencia.rutina_sesion">
                                    {{ asistencia.rutina_sesion.rutina.nombre }} - Sesión {{
                                        asistencia.rutina_sesion.numero_sesion }}
                                </span>
                                <span v-else>
                                    Sesión General #{{ asistencia.numero_sesion }}
                                </span>
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Observaciones
                            </h4>
                            <p class="mt-1 text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                {{ asistencia.observaciones || 'Sin observaciones.' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-slate-700 px-6 py-4 flex justify-start">
                    <Link :href="route('asistencia-sesion.index')"
                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                    &larr; Volver a la lista
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
