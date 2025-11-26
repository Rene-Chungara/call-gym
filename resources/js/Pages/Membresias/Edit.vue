<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    membresia: Object,
});

const form = useForm({
    nombre: props.membresia.nombre,
    precio: props.membresia.precio,
    duracion_dias: props.membresia.duracion_dias,
});

function submit() {
    form.put(route('membresias.update', props.membresia.id));
}
</script>

<template>
    <AuthenticatedLayout>

        <Head title="Editar membresía" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Editar membresía
            </h2>
        </template>

        <div class="py-12 bg-gray-100 dark:bg-slate-900 min-h-screen">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium mb-1">Nombre</label>
                                <input v-model="form.nombre" type="text"
                                    class="mt-1 w-full border border-gray-300 dark:border-slate-600 rounded px-3 py-2 text-sm bg-white dark:bg-slate-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                <div v-if="form.errors.nombre" class="text-red-500 text-xs mt-1">
                                    {{ form.errors.nombre }}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Precio</label>
                                    <input v-model="form.precio" type="number" step="0.01"
                                        class="mt-1 w-full border border-gray-300 dark:border-slate-600 rounded px-3 py-2 text-sm bg-white dark:bg-slate-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                    <div v-if="form.errors.precio" class="text-red-500 text-xs mt-1">
                                        {{ form.errors.precio }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">
                                        Duración (días)
                                    </label>
                                    <input v-model="form.duracion_dias" type="number"
                                        class="mt-1 w-full border border-gray-300 dark:border-slate-600 rounded px-3 py-2 text-sm bg-white dark:bg-slate-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                    <div v-if="form.errors.duracion_dias" class="text-red-500 text-xs mt-1">
                                        {{ form.errors.duracion_dias }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3 pt-2">
                                <Link :href="route('membresias.index')"
                                    class="px-4 py-2 text-sm rounded border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700">
                                Volver
                                </Link>

                                <button type="submit"
                                    class="px-4 py-2 text-sm rounded bg-blue-600 hover:bg-blue-700 text-white shadow-sm"
                                    :disabled="form.processing">
                                    Actualizar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
