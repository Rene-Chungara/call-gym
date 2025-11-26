<script setup>
import { useForm, Link, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { computed, watch, ref } from "vue";

const props = defineProps({
    ventasPaquetes: Array,
    rutinas: Array,
});

const form = useForm({
    venta_paquete_id: "",
    rutina_sesion_id: null,
    numero_sesion: 1,
    fecha_asistencia: new Date().toISOString().split('T')[0],
    hora_entrada: "",
    hora_salida: "",
    estado: "asistio",
    observaciones: "",
});

// Usuario seleccionado basado en el paquete
const usuarioSeleccionado = computed(() => {
    if (!form.venta_paquete_id) return null;
    const venta = props.ventasPaquetes.find(v => v.id === parseInt(form.venta_paquete_id));
    return venta?.usuario;
});

// Rutinas del usuario seleccionado
const rutinasDelUsuario = computed(() => {
    if (!usuarioSeleccionado.value) return [];
    return props.rutinas.filter(r => r.usuario_id === usuarioSeleccionado.value.id);
});

// Sesiones de la rutina seleccionada
const sesionesDisponibles = computed(() => {
    if (!form.rutina_sesion_id) return [];
    const rutina = rutinasDelUsuario.value.find(r =>
        r.sesiones.some(s => s.id === parseInt(form.rutina_sesion_id))
    );
    return rutina?.sesiones || [];
});

// Información del paquete seleccionado
const paqueteInfo = computed(() => {
    if (!form.venta_paquete_id) return null;
    return props.ventasPaquetes.find(v => v.id === parseInt(form.venta_paquete_id));
});

// Resetear rutina cuando cambia el paquete
watch(() => form.venta_paquete_id, () => {
    form.rutina_sesion_id = null;
    form.numero_sesion = 1;
});

const submit = () => {
    form.post(route('asistencia-sesion.store'));
};
</script>

<template>
    <AuthenticatedLayout>

        <Head title="Registrar Asistencia" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">
                Registrar Asistencia
            </h2>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-slate-900 min-h-screen">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-slate-800 shadow-sm border border-gray-200 dark:border-slate-700 rounded-lg">
                    <div class="p-6">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Nueva Asistencia</h1>

                        <form @submit.prevent="submit" class="space-y-6">

                            <!-- Seleccionar Paquete/Usuario -->
                            <div>
                                <InputLabel for="venta_paquete_id" value="Cliente y Paquete" />
                                <select id="venta_paquete_id" v-model="form.venta_paquete_id"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-900 dark:text-white"
                                    required>
                                    <option value="">-- Seleccione un cliente --</option>
                                    <option v-for="vp in ventasPaquetes" :key="vp.id" :value="vp.id">
                                        {{ vp.usuario.nombre }} - {{ vp.paquete.nombre }} ({{ vp.sesiones_restantes }}
                                        sesiones
                                        restantes)
                                    </option>
                                </select>
                                <InputError :message="form.errors.venta_paquete_id" class="mt-2" />

                                <!-- Info del paquete -->
                                <div v-if="paqueteInfo"
                                    class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                    <div class="flex items-center gap-2 text-sm">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-blue-800 dark:text-blue-200">
                                            <strong>{{ paqueteInfo.sesiones_utilizadas || 0 }}</strong> sesiones
                                            utilizadas de
                                            <strong>{{ paqueteInfo.paquete.num_sesiones }}</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Seleccionar Rutina (opcional) -->
                            <div v-if="usuarioSeleccionado && rutinasDelUsuario.length > 0"
                                class="bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 rounded-lg p-4">
                                <h3
                                    class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Rutina Programada (Opcional)
                                </h3>

                                <div class="space-y-3">
                                    <!-- Seleccionar Rutina -->
                                    <div>
                                        <InputLabel for="rutina_id" value="Seleccionar Rutina" />
                                        <select id="rutina_id" v-model="form.rutina_sesion_id"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-900 dark:text-white">
                                            <option :value="null">-- Sin rutina asignada --</option>
                                            <optgroup v-for="rutina in rutinasDelUsuario" :key="rutina.id"
                                                :label="rutina.nombre">
                                                <option v-for="sesion in rutina.sesiones" :key="sesion.id"
                                                    :value="sesion.id">
                                                    Sesión {{ sesion.numero_sesion }}: {{ sesion.descripcion }}
                                                </option>
                                            </optgroup>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Si seleccionas una sesión de rutina, se marcará como completada
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Número de Sesión (si no hay rutina) -->
                            <div v-if="!form.rutina_sesion_id">
                                <InputLabel for="numero_sesion" value="Número de Sesión" />
                                <TextInput id="numero_sesion" v-model.number="form.numero_sesion" type="number" min="1"
                                    class="mt-1 block w-full" required />
                                <InputError :message="form.errors.numero_sesion" class="mt-2" />
                            </div>

                            <!-- Fecha y Horas -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <InputLabel for="fecha_asistencia" value="Fecha" />
                                    <TextInput id="fecha_asistencia" v-model="form.fecha_asistencia" type="date"
                                        class="mt-1 block w-full" required />
                                    <InputError :message="form.errors.fecha_asistencia" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="hora_entrada" value="Hora Entrada" />
                                    <TextInput id="hora_entrada" v-model="form.hora_entrada" type="time"
                                        class="mt-1 block w-full" />
                                    <InputError :message="form.errors.hora_entrada" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="hora_salida" value="Hora Salida" />
                                    <TextInput id="hora_salida" v-model="form.hora_salida" type="time"
                                        class="mt-1 block w-full" />
                                    <InputError :message="form.errors.hora_salida" class="mt-2" />
                                </div>
                            </div>

                            <!-- Estado -->
                            <div>
                                <InputLabel for="estado" value="Estado" />
                                <select id="estado" v-model="form.estado"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-900 dark:text-white"
                                    required>
                                    <option value="asistio">Asistió</option>
                                    <option value="no_asistio">No Asistió</option>
                                    <option value="cancelada">Cancelada</option>
                                </select>
                                <InputError :message="form.errors.estado" class="mt-2" />
                            </div>

                            <!-- Observaciones -->
                            <div>
                                <InputLabel for="observaciones" value="Observaciones" />
                                <textarea id="observaciones" v-model="form.observaciones"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-900 dark:text-white"
                                    rows="3" placeholder="Notas adicionales..."></textarea>
                                <InputError :message="form.errors.observaciones" class="mt-2" />
                            </div>

                            <!-- Botones -->
                            <div class="flex gap-3 justify-end pt-4 border-t border-gray-200 dark:border-slate-700">
                                <Link :href="route('asistencia-sesion.index')"
                                    class="px-6 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-200 text-sm font-medium transition">
                                Cancelar
                                </Link>

                                <PrimaryButton :disabled="form.processing" class="px-6 py-2">
                                    {{ form.processing ? 'Registrando...' : 'Registrar Asistencia' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
