<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    cuota: Object,
    planPago: Object,
    suscripcion: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const isCliente = computed(() => user.value.is_clientes && !user.value.is_propietario && !user.value.is_secretaria);

const form = useForm({
    monto_pagado: props.cuota.monto,
    metodo_pago: "tarjeta",
    observaciones: "",
});

// Formatear fecha
function formatFecha(fecha) {
    if (!fecha) return '-';
    const date = new Date(fecha);
    const dia = String(date.getDate()).padStart(2, '0');
    const mes = String(date.getMonth() + 1).padStart(2, '0');
    const año = date.getFullYear();
    return `${dia}-${mes}-${año}`;
}

function enviarFormulario() {
    form.post(route("cuotas-pago.store", { cuotaPago: props.cuota.id }));
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Pagar Cuota
            </h2>
        </template>

        <div class="py-12 bg-gray-100 dark:bg-slate-900 min-h-screen">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- INFORMACIÓN DE LA SUSCRIPCIÓN -->
                        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 class="text-lg font-semibold mb-4">Información de la Suscripción</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Cliente:</span>
                                    <p class="font-medium">{{ suscripcion.usuario }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Membresía:</span>
                                    <p class="font-medium">{{ suscripcion.membresia }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Plan:</span>
                                    <p class="font-medium">{{ planPago.cantidad_cuotas }} cuotas</p>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Monto Total:</span>
                                    <p class="font-medium">Bs. {{ parseFloat(planPago.monto_total).toFixed(2) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- INFORMACIÓN DE LA CUOTA -->
                        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-slate-700">
                            <h3 class="text-lg font-semibold mb-4">Cuota a Pagar</h3>
                            <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                                <div class="grid grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Número:</span>
                                        <p class="font-bold text-lg">Cuota #{{ cuota.numero_cuota }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Monto:</span>
                                        <p class="font-bold text-lg text-blue-600 dark:text-blue-400">
                                            Bs. {{ parseFloat(cuota.monto).toFixed(2) }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Vencimiento:</span>
                                        <p class="font-medium">{{ formatFecha(cuota.fecha_vencimiento) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FORMULARIO DE PAGO -->
                        <form @submit.prevent="enviarFormulario" class="space-y-6">
                            <!-- Monto a Pagar -->
                            <div>
                                <InputLabel for="monto_pagado" value="Monto a Pagar (Bs.)" />
                                <TextInput id="monto_pagado" v-model="form.monto_pagado" type="number" step="0.01"
                                    class="mt-1 block w-full bg-gray-100 dark:bg-slate-700" readonly disabled />
                                <InputError class="mt-2" :message="form.errors.monto_pagado" />
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Monto fijo: Bs. {{ parseFloat(cuota.monto).toFixed(2) }}
                                </p>
                            </div>

                            <!-- Método de Pago -->
                            <div>
                                <InputLabel for="metodo_pago" value="Método de Pago" />
                                <select id="metodo_pago" v-model="form.metodo_pago"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white">
                                    <option v-if="!isCliente" value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.metodo_pago" />
                            </div>

                            <!-- Observaciones -->
                            <div>
                                <InputLabel for="observaciones" value="Observaciones (Opcional)" />
                                <textarea id="observaciones" v-model="form.observaciones"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white"
                                    rows="3"></textarea>
                                <InputError class="mt-2" :message="form.errors.observaciones" />
                            </div>

                            <!-- Botones -->
                            <div class="flex gap-3 justify-end pt-4 border-t border-gray-200 dark:border-slate-700">
                                <a :href="route('suscripciones.show', suscripcion.id)"
                                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-gray-100 text-sm shadow-sm">
                                    Cancelar
                                </a>

                                <button type="submit" :disabled="form.processing"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white rounded text-sm shadow-sm">
                                    Registrar Pago
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
