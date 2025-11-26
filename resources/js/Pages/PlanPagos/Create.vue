<script setup>
import { ref, computed } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    suscripciones: Array,
});

const form = useForm({
    suscripcion_id: '',
    monto_total: '',
    cantidad_cuotas: 4,
    fechas: [],
});

// Suscripción seleccionada
const suscripcionSeleccionada = computed(() => {
    return props.suscripciones.find(s => s.id == form.suscripcion_id);
});

// Monto por cuota
const montoPorCuota = computed(() => {
    if (!form.monto_total || !form.cantidad_cuotas) return 0;
    return (parseFloat(form.monto_total) / parseInt(form.cantidad_cuotas)).toFixed(2);
});

// Actualizar cantidad de cuotas
function actualizarCuotas() {
    const cantidad = parseInt(form.cantidad_cuotas);
    form.fechas = Array(cantidad).fill(null).map(() => '');
}

// Generar fechas automáticas
function generarFechas() {
    const fechas = [];
    const hoy = new Date();
    
    for (let i = 0; i < parseInt(form.cantidad_cuotas); i++) {
        const fecha = new Date(hoy);
        fecha.setDate(fecha.getDate() + ((i + 1) * 7));
        fechas.push(fecha.toISOString().split('T')[0]);
    }
    
    form.fechas = fechas;
}

// Actualizar monto total desde suscripción
function actualizarMontoTotal() {
    if (suscripcionSeleccionada.value) {
        form.monto_total = suscripcionSeleccionada.value.membresia.precio;
    }
}

// Cambiar fecha de cuota
function cambiarFecha(index, fecha) {
    form.fechas[index] = fecha;
}

// Validar antes de enviar
function validarFormulario() {
    if (!form.suscripcion_id) {
        alert('Selecciona una suscripción');
        return false;
    }
    if (!form.monto_total || parseFloat(form.monto_total) <= 0) {
        alert('Ingresa un monto válido');
        return false;
    }
    if (form.fechas.some(f => !f)) {
        alert('Completa todas las fechas de vencimiento');
        return false;
    }
    return true;
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Crear Plan de Pagos" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Crear Plan de Pagos a Crédito
            </h2>
        </template>

        <div class="py-12 bg-gray-100 dark:bg-slate-900 min-h-screen">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <form @submit.prevent="validarFormulario() && form.post(route('plan-pagos.store'))" class="space-y-6">

                            <!-- Selección de suscripción -->
                            <div>
                                <InputLabel for="suscripcion_id" value="Seleccionar Suscripción" />
                                <select
                                    id="suscripcion_id"
                                    v-model="form.suscripcion_id"
                                    @change="actualizarMontoTotal"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white"
                                >
                                    <option value="">-- Seleccione una suscripción --</option>
                                    <option v-for="s in props.suscripciones" :key="s.id" :value="s.id">
                                        {{ s.usuario.nombre }} — {{ s.membresia.nombre }} (Bs. {{ s.membresia.precio }})
                                    </option>
                                </select>
                                <InputError :message="form.errors.suscripcion_id" class="mt-2" />
                            </div>

                            <!-- Monto total -->
                            <div>
                                <InputLabel for="monto_total" value="Monto Total (Bs.)" />
                                <TextInput
                                    id="monto_total"
                                    v-model="form.monto_total"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    class="mt-1 block w-full"
                                    placeholder="Ingrese el monto total"
                                />
                                <InputError :message="form.errors.monto_total" class="mt-2" />
                            </div>

                            <!-- Cantidad de cuotas -->
                            <div>
                                <InputLabel for="cantidad_cuotas" value="Cantidad de Cuotas" />
                                <select
                                    id="cantidad_cuotas"
                                    v-model="form.cantidad_cuotas"
                                    @change="actualizarCuotas"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white"
                                >
                                    <option v-for="n in 12" :key="n" :value="n">{{ n }} cuota{{ n > 1 ? 's' : '' }}</option>
                                </select>
                                <InputError :message="form.errors.cantidad_cuotas" class="mt-2" />
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Monto por cuota: <span class="font-bold">Bs. {{ montoPorCuota }}</span>
                                </p>
                            </div>

                            <!-- Generar fechas automáticas -->
                            <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                                <button
                                    type="button"
                                    @click="generarFechas"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm"
                                >
                                    Generar fechas automáticas (semanales)
                                </button>
                            </div>

                            <!-- Fechas de vencimiento -->
                            <div>
                                <InputLabel value="Fechas de Vencimiento de Cuotas" />
                                <div class="space-y-3 mt-3">
                                    <div v-for="(fecha, index) in form.fechas" :key="index" class="flex items-center gap-3">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400 w-20">
                                            Cuota {{ index + 1 }}:
                                        </span>
                                        <input
                                            type="date"
                                            :value="fecha"
                                            @input="cambiarFecha(index, $event.target.value)"
                                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white"
                                        />
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            Bs. {{ montoPorCuota }}
                                        </span>
                                    </div>
                                </div>
                                <InputError :message="form.errors['fechas.*']" class="mt-2" />
                            </div>

                            <!-- Resumen -->
                            <div class="bg-indigo-50 dark:bg-slate-700 p-4 rounded-lg border border-indigo-200 dark:border-slate-600">
                                <h3 class="font-semibold text-indigo-900 dark:text-indigo-100 mb-3">Resumen del Plan</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-700 dark:text-gray-300">Monto Total:</span>
                                        <span class="font-bold">Bs. {{ (parseFloat(form.monto_total) || 0).toFixed(2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-700 dark:text-gray-300">Cantidad de Cuotas:</span>
                                        <span class="font-bold">{{ form.cantidad_cuotas }}</span>
                                    </div>
                                    <div class="border-t border-indigo-200 dark:border-slate-600 pt-2 mt-2 flex justify-between">
                                        <span class="text-gray-700 dark:text-gray-300">Por Cuota:</span>
                                        <span class="font-bold text-indigo-600 dark:text-indigo-400">Bs. {{ montoPorCuota }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex gap-3 justify-end">
                                <Link
                                    :href="route('suscripciones.index')"
                                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-gray-100 text-sm shadow-sm"
                                >
                                    Cancelar
                                </Link>

                                <PrimaryButton
                                    :disabled="form.processing || !form.suscripcion_id"
                                >
                                    Crear Plan de Pagos
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
