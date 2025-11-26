<script setup>
import { useForm, Link, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    usuarios: Array,
});

const form = useForm({
    usuario_id: "",
    fecha: new Date().toISOString().split('T')[0],
    peso: "",
    medidas: "",
    observaciones: "",
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Registrar Seguimiento" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Registrar Seguimiento
            </h2>
        </template>

        <div class="py-12 bg-gray-100 dark:bg-slate-900 min-h-screen">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h1 class="text-2xl font-bold mb-6">Nuevo Seguimiento</h1>

                        <form @submit.prevent="form.post(route('seguimientos.store'))" class="space-y-6">

                            <div>
                                <InputLabel for="usuario_id" value="Seleccionar Cliente" />
                                <select
                                    id="usuario_id"
                                    v-model="form.usuario_id"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white"
                                >
                                    <option value="">-- Seleccione un cliente --</option>
                                    <option v-for="u in props.usuarios" :key="u.id" :value="u.id">
                                        {{ u.nombre }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.usuario_id" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="fecha" value="Fecha" />
                                <TextInput
                                    id="fecha"
                                    v-model="form.fecha"
                                    type="date"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.fecha" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="peso" value="Peso (kg)" />
                                    <TextInput
                                        id="peso"
                                        v-model="form.peso"
                                        type="number"
                                        step="0.1"
                                        class="mt-1 block w-full"
                                        placeholder="Ej: 75.5"
                                    />
                                    <InputError :message="form.errors.peso" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="medidas" value="Medidas (cm)" />
                                    <TextInput
                                        id="medidas"
                                        v-model="form.medidas"
                                        type="number"
                                        step="0.1"
                                        class="mt-1 block w-full"
                                        placeholder="Ej: 85.5"
                                    />
                                    <InputError :message="form.errors.medidas" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <InputLabel for="observaciones" value="Observaciones" />
                                <textarea
                                    id="observaciones"
                                    v-model="form.observaciones"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white"
                                    rows="4"
                                    placeholder="Notas sobre el progreso..."
                                ></textarea>
                                <InputError :message="form.errors.observaciones" class="mt-2" />
                            </div>

                            <div class="flex gap-3 justify-end">
                                <Link
                                    :href="route('seguimientos.index')"
                                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-gray-100 text-sm shadow-sm"
                                >
                                    Cancelar
                                </Link>

                                <PrimaryButton :disabled="form.processing">
                                    Registrar
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
