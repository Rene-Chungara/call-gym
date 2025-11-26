<script setup>
import { computed } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    ejercicio: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const canManage = computed(() => {
    return user.value.is_propietario || user.value.is_instructor;
});

const deleteEjercicio = () => {
    if (confirm("¿Estás seguro de eliminar este ejercicio?")) {
        router.delete(route("ejercicios.destroy", props.ejercicio.id));
    }
};

const getDificultadColor = (dificultad) => {
    switch (dificultad) {
        case 'principiante': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'intermedio': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'avanzado': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                Detalle del Ejercicio
            </h2>
        </template>

        <div class="max-w-4xl mx-auto mt-6 px-4">
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                {{ ejercicio.nombre }}
                            </h3>
                            <span
                                :class="['text-sm px-3 py-1 rounded-full capitalize font-medium', getDificultadColor(ejercicio.dificultad)]">
                                {{ ejercicio.dificultad }}
                            </span>
                        </div>
                        <div class="flex gap-2" v-if="canManage">
                            <Link :href="route('ejercicios.edit', ejercicio.id)"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600">
                            Editar
                            </Link>
                            <DangerButton @click="deleteEjercicio">
                                Eliminar
                            </DangerButton>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Grupo Muscular
                            </h4>
                            <p class="mt-1 text-lg text-gray-900 dark:text-gray-200">
                                {{ ejercicio.grupo_muscular }}
                            </p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Equipo Requerido
                            </h4>
                            <p class="mt-1 text-lg text-gray-900 dark:text-gray-200">
                                {{ ejercicio.equipo_requerido || 'Ninguno' }}
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Descripción / Instrucciones
                            </h4>
                            <div
                                class="mt-2 prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                {{ ejercicio.descripcion || 'Sin descripción disponible.' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-slate-700 px-6 py-4 flex justify-start">
                    <Link :href="route('ejercicios.index')"
                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                    &larr; Volver a la lista
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
