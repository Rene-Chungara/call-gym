<script setup>
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import TextArea from "@/Components/TextArea.vue"; // Assuming this exists, if not I'll use textarea
import { Link } from "@inertiajs/vue3";

const form = useForm({
    nombre: "",
    descripcion: "",
    grupo_muscular: "",
    dificultad: "principiante",
    equipo_requerido: "",
});

const submit = () => {
    form.post(route("ejercicios.store"));
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                Crear Nuevo Ejercicio
            </h2>
        </template>

        <div class="max-w-4xl mx-auto mt-6 px-4">
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div>
                            <InputLabel for="nombre" value="Nombre del Ejercicio" />
                            <TextInput id="nombre" type="text" class="mt-1 block w-full" v-model="form.nombre" required
                                autofocus />
                            <InputError class="mt-2" :message="form.errors.nombre" />
                        </div>

                        <!-- Grupo Muscular -->
                        <div>
                            <InputLabel for="grupo_muscular" value="Grupo Muscular" />
                            <TextInput id="grupo_muscular" type="text" class="mt-1 block w-full"
                                v-model="form.grupo_muscular" required placeholder="Ej: Pecho, Espalda, Piernas" />
                            <InputError class="mt-2" :message="form.errors.grupo_muscular" />
                        </div>

                        <!-- Dificultad -->
                        <div>
                            <InputLabel for="dificultad" value="Dificultad" />
                            <select id="dificultad" v-model="form.dificultad"
                                class="mt-1 block w-full border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="principiante">Principiante</option>
                                <option value="intermedio">Intermedio</option>
                                <option value="avanzado">Avanzado</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.dificultad" />
                        </div>

                        <!-- Equipo Requerido -->
                        <div>
                            <InputLabel for="equipo_requerido" value="Equipo Requerido (Opcional)" />
                            <TextInput id="equipo_requerido" type="text" class="mt-1 block w-full"
                                v-model="form.equipo_requerido" placeholder="Ej: Mancuernas, Barra, Ninguno" />
                            <InputError class="mt-2" :message="form.errors.equipo_requerido" />
                        </div>

                        <!-- Descripción -->
                        <div class="md:col-span-2">
                            <InputLabel for="descripcion" value="Descripción / Instrucciones" />
                            <textarea id="descripcion" v-model="form.descripcion"
                                class="mt-1 block w-full border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="4"></textarea>
                            <InputError class="mt-2" :message="form.errors.descripcion" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <Link :href="route('ejercicios.index')"
                            class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                        Cancelar
                        </Link>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Guardar Ejercicio
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
