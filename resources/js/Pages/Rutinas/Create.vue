<script setup>
import { useForm, Link, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref } from "vue";

const props = defineProps({
    usuarios: Array,
    ejercicios: Array,
});

const form = useForm({
    usuario_id: "",
    nombre: "",
    descripcion: "",
    sesiones: [
        {
            numero_sesion: 1,
            descripcion: "",
            ejercicios: []
        }
    ],
});

const agregarSesion = () => {
    form.sesiones.push({
        numero_sesion: form.sesiones.length + 1,
        descripcion: "",
        ejercicios: []
    });
};

const eliminarSesion = (index) => {
    if (form.sesiones.length > 1) {
        form.sesiones.splice(index, 1);
        // Renumerar sesiones
        form.sesiones.forEach((sesion, idx) => {
            sesion.numero_sesion = idx + 1;
        });
    }
};

const agregarEjercicio = (sesionIndex) => {
    form.sesiones[sesionIndex].ejercicios.push({
        ejercicio_id: "",
        orden: form.sesiones[sesionIndex].ejercicios.length + 1,
        series: 3,
        repeticiones: 10,
        peso_estimado: null,
        descanso_segundos: 60,
        notas: ""
    });
};

const eliminarEjercicio = (sesionIndex, ejercicioIndex) => {
    form.sesiones[sesionIndex].ejercicios.splice(ejercicioIndex, 1);
    // Renumerar orden
    form.sesiones[sesionIndex].ejercicios.forEach((ej, idx) => {
        ej.orden = idx + 1;
    });
};

const getEjercicioNombre = (id) => {
    const ejercicio = props.ejercicios.find(e => e.id === parseInt(id));
    return ejercicio ? ejercicio.nombre : '';
};

const submit = () => {
    form.post(route('rutinas.store'));
};
</script>

<template>
    <AuthenticatedLayout>

        <Head title="Crear Rutina" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Crear Rutina
            </h2>
        </template>

        <div class="py-12 bg-gray-100 dark:bg-slate-900 min-h-screen">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h1 class="text-2xl font-bold mb-6">Nueva Rutina</h1>

                        <form @submit.prevent="submit" class="space-y-8">

                            <!-- Información Básica -->
                            <div class="bg-gray-50 dark:bg-slate-700 p-6 rounded-lg space-y-4">
                                <h3 class="text-lg font-semibold mb-4">Información Básica</h3>

                                <div>
                                    <InputLabel for="usuario_id" value="Cliente" />
                                    <select id="usuario_id" v-model="form.usuario_id"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-900 dark:text-white"
                                        required>
                                        <option value="">-- Seleccione un cliente --</option>
                                        <option v-for="u in props.usuarios" :key="u.id" :value="u.id">
                                            {{ u.nombre }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.usuario_id" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="nombre" value="Nombre de la Rutina" />
                                    <TextInput id="nombre" v-model="form.nombre" type="text" class="mt-1 block w-full"
                                        placeholder="Ej: Rutina de Fuerza - Semana 1" required />
                                    <InputError :message="form.errors.nombre" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="descripcion" value="Descripción" />
                                    <textarea id="descripcion" v-model="form.descripcion"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-900 dark:text-white"
                                        rows="3" placeholder="Objetivo y detalles generales de la rutina..."
                                        required></textarea>
                                    <InputError :message="form.errors.descripcion" class="mt-2" />
                                </div>
                            </div>

                            <!-- Sesiones -->
                            <div>
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold">Sesiones de Entrenamiento</h3>
                                    <button type="button" @click="agregarSesion"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-sm">
                                        + Agregar Sesión
                                    </button>
                                </div>

                                <div v-for="(sesion, sesionIndex) in form.sesiones" :key="sesionIndex"
                                    class="mb-6 border border-gray-300 dark:border-slate-600 rounded-lg p-6 bg-white dark:bg-slate-800">

                                    <!-- Header de Sesión -->
                                    <div class="flex justify-between items-start mb-4">
                                        <h4 class="text-md font-semibold text-indigo-600 dark:text-indigo-400">
                                            Sesión {{ sesion.numero_sesion }}
                                        </h4>
                                        <button v-if="form.sesiones.length > 1" type="button"
                                            @click="eliminarSesion(sesionIndex)"
                                            class="text-red-600 hover:text-red-800 text-sm">
                                            Eliminar Sesión
                                        </button>
                                    </div>

                                    <!-- Descripción de Sesión -->
                                    <div class="mb-4">
                                        <InputLabel :for="'sesion_desc_' + sesionIndex"
                                            value="Descripción de la Sesión" />
                                        <textarea :id="'sesion_desc_' + sesionIndex" v-model="sesion.descripcion"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-900 dark:text-white"
                                            rows="2" placeholder="Ej: Día de Pecho y Tríceps" required></textarea>
                                        <InputError :message="form.errors[`sesiones.${sesionIndex}.descripcion`]"
                                            class="mt-2" />
                                    </div>

                                    <!-- Ejercicios de la Sesión -->
                                    <div class="bg-gray-50 dark:bg-slate-700 p-4 rounded-lg">
                                        <div class="flex justify-between items-center mb-3">
                                            <h5 class="font-semibold text-sm">Ejercicios</h5>
                                            <button type="button" @click="agregarEjercicio(sesionIndex)"
                                                class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-xs">
                                                + Agregar Ejercicio
                                            </button>
                                        </div>

                                        <div v-if="sesion.ejercicios.length === 0"
                                            class="text-center text-gray-500 py-4 text-sm">
                                            No hay ejercicios. Haz clic en "Agregar Ejercicio" para comenzar.
                                        </div>

                                        <div v-for="(ejercicio, ejercicioIndex) in sesion.ejercicios"
                                            :key="ejercicioIndex"
                                            class="mb-4 p-4 bg-white dark:bg-slate-800 rounded border border-gray-200 dark:border-slate-600">

                                            <div class="flex justify-between items-start mb-3">
                                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                    Ejercicio {{ ejercicio.orden }}
                                                </span>
                                                <button type="button"
                                                    @click="eliminarEjercicio(sesionIndex, ejercicioIndex)"
                                                    class="text-red-600 hover:text-red-800 text-xs">
                                                    Eliminar
                                                </button>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <!-- Seleccionar Ejercicio -->
                                                <div class="md:col-span-2">
                                                    <InputLabel :for="'ejercicio_' + sesionIndex + '_' + ejercicioIndex"
                                                        value="Ejercicio" />
                                                    <select :id="'ejercicio_' + sesionIndex + '_' + ejercicioIndex"
                                                        v-model="ejercicio.ejercicio_id"
                                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-900 dark:text-white text-sm"
                                                        required>
                                                        <option value="">-- Seleccione un ejercicio --</option>
                                                        <optgroup
                                                            v-for="grupo in [...new Set(props.ejercicios.map(e => e.grupo_muscular))]"
                                                            :key="grupo" :label="grupo">
                                                            <option
                                                                v-for="ej in props.ejercicios.filter(e => e.grupo_muscular === grupo)"
                                                                :key="ej.id" :value="ej.id">
                                                                {{ ej.nombre }} ({{ ej.dificultad }})
                                                            </option>
                                                        </optgroup>
                                                    </select>
                                                    <InputError
                                                        :message="form.errors[`sesiones.${sesionIndex}.ejercicios.${ejercicioIndex}.ejercicio_id`]"
                                                        class="mt-2" />
                                                </div>

                                                <!-- Series -->
                                                <div>
                                                    <InputLabel :for="'series_' + sesionIndex + '_' + ejercicioIndex"
                                                        value="Series" />
                                                    <TextInput :id="'series_' + sesionIndex + '_' + ejercicioIndex"
                                                        v-model.number="ejercicio.series" type="number" min="1"
                                                        class="mt-1 block w-full" required />
                                                </div>

                                                <!-- Repeticiones -->
                                                <div>
                                                    <InputLabel :for="'reps_' + sesionIndex + '_' + ejercicioIndex"
                                                        value="Repeticiones" />
                                                    <TextInput :id="'reps_' + sesionIndex + '_' + ejercicioIndex"
                                                        v-model.number="ejercicio.repeticiones" type="number" min="1"
                                                        class="mt-1 block w-full" required />
                                                </div>

                                                <!-- Peso Estimado -->
                                                <div>
                                                    <InputLabel :for="'peso_' + sesionIndex + '_' + ejercicioIndex"
                                                        value="Peso Estimado (kg)" />
                                                    <TextInput :id="'peso_' + sesionIndex + '_' + ejercicioIndex"
                                                        v-model.number="ejercicio.peso_estimado" type="number"
                                                        step="0.5" min="0" class="mt-1 block w-full"
                                                        placeholder="Opcional" />
                                                </div>

                                                <!-- Descanso -->
                                                <div>
                                                    <InputLabel :for="'descanso_' + sesionIndex + '_' + ejercicioIndex"
                                                        value="Descanso (seg)" />
                                                    <TextInput :id="'descanso_' + sesionIndex + '_' + ejercicioIndex"
                                                        v-model.number="ejercicio.descanso_segundos" type="number"
                                                        min="0" class="mt-1 block w-full" placeholder="60" />
                                                </div>

                                                <!-- Notas -->
                                                <div class="md:col-span-2">
                                                    <InputLabel :for="'notas_' + sesionIndex + '_' + ejercicioIndex"
                                                        value="Notas" />
                                                    <textarea :id="'notas_' + sesionIndex + '_' + ejercicioIndex"
                                                        v-model="ejercicio.notas"
                                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-900 dark:text-white text-sm"
                                                        rows="2" placeholder="Indicaciones especiales..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex gap-3 justify-end pt-6 border-t border-gray-200 dark:border-slate-700">
                                <Link :href="route('rutinas.index')"
                                    class="px-6 py-2 rounded bg-gray-300 hover:bg-gray-400 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-gray-100 text-sm shadow-sm transition">
                                Cancelar
                                </Link>

                                <PrimaryButton :disabled="form.processing" class="px-6 py-2">
                                    {{ form.processing ? 'Creando...' : 'Crear Rutina Completa' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
