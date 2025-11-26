<script setup>
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    asistencia: Object,
    ventasPaquetes: Array,
    rutinaSesiones: Array,
});

const form = useForm({
    venta_paquete_id: props.asistencia.venta_paquete_id,
    rutina_sesion_id: props.asistencia.rutina_sesion_id,
    numero_sesion: props.asistencia.numero_sesion,
    fecha_asistencia: props.asistencia.fecha_asistencia,
    hora_entrada: props.asistencia.hora_entrada ? props.asistencia.hora_entrada.substring(0, 5) : "",
    hora_salida: props.asistencia.hora_salida ? props.asistencia.hora_salida.substring(0, 5) : "",
    estado: props.asistencia.estado,
    observaciones: props.asistencia.observaciones,
});

const filteredRutinaSesiones = computed(() => {
    if (!form.venta_paquete_id) return [];

    const venta = props.ventasPaquetes.find(v => v.id === form.venta_paquete_id);
    if (!venta) return [];

    return props.rutinaSesiones.filter(sesion => sesion.rutina.usuario_id === venta.usuario_id);
});

const submit = () => {
    form.put(route("asistencia-sesion.update", props.asistencia.id));
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                Editar Asistencia
            </h2>
        </template>

        <div class="max-w-4xl mx-auto mt-6 px-4">
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Usuario / Paquete (Readonly in Edit usually, but let's keep it disabled) -->
                        <div class="md:col-span-2">
                            <InputLabel for="venta_paquete_id" value="Usuario (Paquete)" />
                            <select id="venta_paquete_id" v-model="form.venta_paquete_id"
                                class="mt-1 block w-full border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-gray-300 bg-gray-100 dark:bg-slate-800 cursor-not-allowed rounded-md shadow-sm"
                                disabled>
                                <option v-for="venta in ventasPaquetes" :key="venta.id" :value="venta.id">
                                    {{ venta.usuario.nombre }} - {{ venta.paquete.nombre }}
                                </option>
                            </select>
                        </div>

                        <!-- Rutina Sesión -->
                        <div class="md:col-span-2">
                            <InputLabel for="rutina_sesion_id" value="Rutina Asignada" />
                            <select id="rutina_sesion_id" v-model="form.rutina_sesion_id"
                                class="mt-1 block w-full border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Ninguna (Sesión Libre)</option>
                                <option v-for="sesion in filteredRutinaSesiones" :key="sesion.id" :value="sesion.id">
                                    {{ sesion.rutina.nombre }} - Sesión {{ sesion.numero_sesion }}: {{
                                        sesion.descripcion }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.rutina_sesion_id" />
                        </div>

                        <!-- Número de Sesión -->
                        <div>
                            <InputLabel for="numero_sesion" value="Número de Sesión" />
                            <TextInput id="numero_sesion" type="number" class="mt-1 block w-full"
                                v-model="form.numero_sesion" required min="1" />
                            <InputError class="mt-2" :message="form.errors.numero_sesion" />
                        </div>

                        <!-- Fecha -->
                        <div>
                            <InputLabel for="fecha_asistencia" value="Fecha" />
                            <TextInput id="fecha_asistencia" type="date" class="mt-1 block w-full"
                                v-model="form.fecha_asistencia" required />
                            <InputError class="mt-2" :message="form.errors.fecha_asistencia" />
                        </div>

                        <!-- Hora Entrada -->
                        <div>
                            <InputLabel for="hora_entrada" value="Hora Entrada" />
                            <TextInput id="hora_entrada" type="time" class="mt-1 block w-full"
                                v-model="form.hora_entrada" />
                            <InputError class="mt-2" :message="form.errors.hora_entrada" />
                        </div>

                        <!-- Hora Salida -->
                        <div>
                            <InputLabel for="hora_salida" value="Hora Salida" />
                            <TextInput id="hora_salida" type="time" class="mt-1 block w-full"
                                v-model="form.hora_salida" />
                            <InputError class="mt-2" :message="form.errors.hora_salida" />
                        </div>

                        <!-- Estado -->
                        <div>
                            <InputLabel for="estado" value="Estado" />
                            <select id="estado" v-model="form.estado"
                                class="mt-1 block w-full border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required>
                                <option value="asistio">Asistió</option>
                                <option value="no_asistio">No Asistió (Falta)</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.estado" />
                        </div>

                        <!-- Observaciones -->
                        <div class="md:col-span-2">
                            <InputLabel for="observaciones" value="Observaciones / Notas del Entrenador" />
                            <textarea id="observaciones" v-model="form.observaciones"
                                class="mt-1 block w-full border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="3"></textarea>
                            <InputError class="mt-2" :message="form.errors.observaciones" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <Link :href="route('asistencia-sesion.index')"
                            class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                        Cancelar
                        </Link>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Actualizar Asistencia
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
