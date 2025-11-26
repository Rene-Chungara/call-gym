<script setup>
import { ref, computed } from "vue";
import { useForm, Link, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    suscripciones: Array,
    suscripcionSeleccionada: Object,
});

const form = useForm({
    suscripcion_id: props.suscripcionSeleccionada?.id || "",
    monto_abonado: props.suscripcionSeleccionada?.precio ? String(props.suscripcionSeleccionada.precio) : "",
    metodo_pago: "efectivo",
    observaciones: "",
});

// Obtener suscripción seleccionada
const suscripcionSeleccionada = computed(() => {
    return props.suscripciones.find((x) => x.id == form.suscripcion_id);
});

// Selección automática del precio
function updateMontoTotal() {
    let s = suscripcionSeleccionada.value;
    if (s) {
        form.monto_abonado = String(s.precio);
    }
}

// Mostrar el monto total de la membresía
const montoTotal = computed(() => {
    const s = suscripcionSeleccionada.value;
    return s ? s.precio : 0;
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Registrar Pago" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Registrar Nuevo Pago
            </h2>
        </template>

        <div class="py-12 bg-gray-100 dark:bg-slate-900 min-h-screen">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h1 class="text-2xl font-bold mb-6">Registrar Pago</h1>

                        <form @submit.prevent="form.post(route('pagos.store'))" class="space-y-6">

                            <!-- Selección de suscripción -->
                            <div>
                                <InputLabel for="suscripcion_id" value="Seleccionar Suscripción" />
                                <select
                                    id="suscripcion_id"
                                    v-model="form.suscripcion_id"
                                    @change="updateMontoTotal"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white"
                                >
                                    <option value="">-- Seleccione una suscripción --</option>
                                    <option v-for="s in props.suscripciones" :key="s.id" :value="s.id">
                                        {{ s.usuario }} — {{ s.membresia }} (Bs. {{ s.precio }})
                                    </option>
                                </select>
                                <InputError :message="form.errors.suscripcion_id" class="mt-2" />
                            </div>

                            <!-- Monto total -->
                            <div>
                                <InputLabel for="monto_total" value="Monto Total (Bs.)" />
                                <div class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md bg-gray-100 dark:bg-slate-600 text-gray-900 dark:text-gray-100">
                                    Bs. {{ montoTotal.toFixed(2) }}
                                </div>
                            </div>

                            <!-- Monto a abonar -->
                            <div>
                                <InputLabel for="monto_abonado" value="Monto a Abonar (Bs.)" />
                                <TextInput
                                    id="monto_abonado"
                                    v-model="form.monto_abonado"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    :max="montoTotal"
                                    class="mt-1 block w-full"
                                    placeholder="Ingrese el monto a pagar"
                                />
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Máximo permitido: <span class="font-bold">Bs. {{ montoTotal.toFixed(2) }}</span>
                                </p>
                                <InputError :message="form.errors.monto_abonado" class="mt-2" />
                            </div>

                            <!-- Método de pago -->
                            <div>
                                <InputLabel for="metodo_pago" value="Método de Pago" />
                                <select
                                    id="metodo_pago"
                                    v-model="form.metodo_pago"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white"
                                >
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta de Crédito</option>
                                </select>
                                <InputError :message="form.errors.metodo_pago" class="mt-2" />
                            </div>

                            <!-- Observaciones (opcional) -->
                            <div>
                                <InputLabel for="observaciones" value="Observaciones (Opcional)" />
                                <textarea
                                    id="observaciones"
                                    v-model="form.observaciones"
                                    rows="3"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white"
                                    placeholder="Notas sobre el pago..."
                                ></textarea>
                                <InputError :message="form.errors.observaciones" class="mt-2" />
                            </div>

                            <!-- Resumen -->
                            <div class="bg-indigo-50 dark:bg-slate-700 p-4 rounded-lg border border-indigo-200 dark:border-slate-600">
                                <h3 class="font-semibold text-indigo-900 dark:text-indigo-100 mb-3">Resumen del Pago</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-700 dark:text-gray-300">Monto Total:</span>
                                        <span class="font-bold">Bs. {{ montoTotal.toFixed(2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-700 dark:text-gray-300">Monto a Abonar:</span>
                                        <span class="font-bold text-green-600 dark:text-green-400">Bs. {{ (parseFloat(form.monto_abonado) || 0).toFixed(2) }}</span>
                                    </div>
                                    <div class="border-t border-indigo-200 dark:border-slate-600 pt-2 mt-2 flex justify-between">
                                        <span class="text-gray-700 dark:text-gray-300">Método:</span>
                                        <span class="font-bold">{{ form.metodo_pago === 'efectivo' ? '💵 Efectivo' : '💳 Tarjeta' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex gap-3 justify-end">
                                <Link
                                    :href="route('pagos.index')"
                                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-gray-100 text-sm shadow-sm"
                                >
                                    Cancelar
                                </Link>

                                <PrimaryButton
                                    :disabled="form.processing || !form.suscripcion_id"
                                >
                                    {{ form.metodo_pago === 'efectivo' ? 'Registrar Pago en Efectivo' : 'Pagar con Tarjeta' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

