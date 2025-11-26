<script setup>
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    rutina: Object,
    ejercicios: Array,
    proximaSesion: Number,
});

const form = useForm({
    rutina_id: props.rutina.id,
    numero_sesion: props.proximaSesion,
    descripcion: `Sesión ${props.proximaSesion}`,
    ejercicios: [
        {
            ejercicio_id: "",
            series: 3,
            repeticiones: 10,
            peso_estimado: null,
            descanso_segundos: 60,
        }
    ],
});

const addEjercicio = () => {
    form.ejercicios.push({
        ejercicio_id: "",
        series: 3,
        repeticiones: 10,
        peso_estimado: null,
        descanso_segundos: 60,
    });
};

const removeEjercicio = (index) => {
    form.ejercicios.splice(index, 1);
};

const submit = () => {
    form.post(route("rutina-sesion.store"));
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                Agregar Sesión a Rutina: {{ rutina.nombre }}
            </h2>
        </template>

        <div class="max-w-5xl mx-auto mt-6 px-4">
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6">
                <form @submit.prevent="submit">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <InputLabel for="numero_sesion" value="Número de Sesión" />
                            <TextInput id="numero_sesion" type="number" class="mt-1 block w-full"
                                v-model="form.numero_sesion" required />
                            <InputError class="mt-2" :message="form.errors.numero_sesion" />
                        </div>

                        <div>
                            <InputLabel for="descripcion" value="Descripción / Nombre de la Sesión" />
                            <TextInput id="descripcion" type="text" class="mt-1 block w-full" v-model="form.descripcion"
                                required placeholder="Ej: Día 1 - Pecho y Tríceps" />
                            <InputError class="mt-2" :message="form.errors.descripcion" />
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-slate-700 pt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Ejercicios de la Sesión
                            </h3>
                            <SecondaryButton @click="addEjercicio" type="button">
                                + Agregar Ejercicio
                            </SecondaryButton>
                        </div>

                        <div v-if="form.ejercicios.length === 0"
                            class="text-center py-8 text-gray-500 dark:text-gray-400">
                            No hay ejercicios agregados. Haz clic en "+ Agregar Ejercicio".
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="(item, index) in form.ejercicios" :key="index"
                                class="p-4 border border-gray-200 dark:border-slate-700 rounded-lg bg-gray-50 dark:bg-slate-900">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-sm font-bold text-gray-500 dark:text-gray-400">Ejercicio #{{ index
                                        + 1
                                        }}</span>
                                    <button type="button" @click="removeEjercicio(index)"
                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        Eliminar
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                    <div class="md:col-span-4">
                                        <InputLabel :for="'ejercicio_' + index" value="Ejercicio" />
                                        <select :id="'ejercicio_' + index" v-model="item.ejercicio_id"
                                            class="mt-1 block w-full border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            required>
                                            <option value="" disabled>Seleccionar...</option>
                                            <option v-for="ej in ejercicios" :key="ej.id" :value="ej.id">
                                                {{ ej.nombre }} ({{ ej.grupo_muscular }})
                                            </option>
                                        </select>
                                        <InputError :message="form.errors[`ejercicios.${index}.ejercicio_id`]" />
                                    </div>

                                    <div class="md:col-span-2">
                                        <InputLabel :for="'series_' + index" value="Series" />
                                        <TextInput :id="'series_' + index" type="number" class="mt-1 block w-full"
                                            v-model="item.series" required min="1" />
                                        <InputError :message="form.errors[`ejercicios.${index}.series`]" />
                                    </div>

                                    <div class="md:col-span-2">
                                        <InputLabel :for="'reps_' + index" value="Reps" />
                                        <TextInput :id="'reps_' + index" type="number" class="mt-1 block w-full"
                                            v-model="item.repeticiones" required min="1" />
                                        <InputError :message="form.errors[`ejercicios.${index}.repeticiones`]" />
                                    </div>

                                    <div class="md:col-span-2">
                                        <InputLabel :for="'peso_' + index" value="Peso (kg)" />
                                        <TextInput :id="'peso_' + index" type="number" step="0.5"
                                            class="mt-1 block w-full" v-model="item.peso_estimado"
                                            placeholder="Opcional" />
                                        <InputError :message="form.errors[`ejercicios.${index}.peso_estimado`]" />
                                    </div>

                                    <div class="md:col-span-2">
                                        <InputLabel :for="'descanso_' + index" value="Descanso (s)" />
                                        <TextInput :id="'descanso_' + index" type="number" class="mt-1 block w-full"
                                            v-model="item.descanso_segundos" placeholder="Seg" />
                                        <InputError :message="form.errors[`ejercicios.${index}.descanso_segundos`]" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.ejercicios" />
                    </div>

                    <div class="flex items-center justify-end mt-8 gap-4">
                        <Link :href="route('rutinas.show', rutina.id)"
                            class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                        Cancelar
                        </Link>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Guardar Sesión
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
