<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    pagos: Object,
});

// Memoizar cálculos para cada pago
const pagosConCalculos = computed(() => {
    return props.pagos.data?.map(item => ({
        ...item,
        porcentajePagado: Math.min(100, Math.floor((item.monto_abonado / item.monto_total_membresia) * 100)),
        fechaAbono: format(item.fecha_abono),
        cliente: item.cliente || (item.suscripcion?.usuario?.nombre) || 'N/A',
        membresia: item.membresia || (item.suscripcion?.membresia?.nombre) || 'N/A',
    })) || [];
});

// Formato de fecha DD-MM-YYYY
function format(fecha) {
    if (!fecha) return "";
    const d = new Date(fecha);
    return `${String(d.getDate()).padStart(2, "0")}-${String(
        d.getMonth() + 1
    ).padStart(2, "0")}-${d.getFullYear()}`;
}

function porcentaje(abono, total) {
    return Math.min(100, Math.floor((abono / total) * 100));
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                Pagos Registrados
            </h2>
        </template>

        <div class="max-w-7xl mx-auto mt-6 px-4">

            <!-- Botón -->
            <div class="flex justify-end mb-6">
                <Link
                    :href="route('pagos.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                >
                    Registrar pago
                </Link>
            </div>

            <!-- GRID -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <div
                    v-for="item in pagosConCalculos"
                    :key="item.id"
                    class="rounded-xl bg-white dark:bg-slate-800 shadow-md border border-gray-200 dark:border-slate-700 p-5"
                >
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                {{ item.cliente }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-300">
                                {{ item.membresia }}
                            </p>
                        </div>

                        <!-- BADGE ESTADO -->
                        <span
                            class="px-3 py-1 text-xs rounded-full font-semibold"
                            :class="{
                                'bg-green-100 text-green-700': item.estado_pago,
                                'bg-red-100 text-red-700': !item.estado_pago,
                            }"
                        >
                            {{ item.estado_pago ? 'Pagado' : 'Pendiente' }}
                        </span>
                    </div>

                    <!-- MONTOS -->
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        <p><strong>Total:</strong> Bs. {{ item.monto_total_membresia }}</p>
                        <p><strong>Abonado:</strong> Bs. {{ item.monto_abonado }}</p>
                        <p><strong>Método:</strong> {{ item.metodo_pago === 'efectivo' ? 'Efectivo' : 'Tarjeta' }}</p>
                    </div>

                    <!-- PROGRESO -->
                    <div class="mt-3">
                        <div class="w-full h-2 bg-gray-200 dark:bg-slate-700 rounded">
                            <div
                                class="h-full bg-indigo-600 rounded"
                                :style="{ width: item.porcentajePagado + '%' }"
                            ></div>
                        </div>
                        <p
                            class="mt-1 text-xs text-gray-500 dark:text-gray-400 font-medium"
                        >
                            {{ item.porcentajePagado }}%
                            pagado
                        </p>
                    </div>

                    <!-- FECHAS -->
                    <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                        <p>Fecha de Pago: {{ item.fechaAbono }}</p>
                        <p v-if="item.observaciones" class="mt-1"><strong>Nota:</strong> {{ item.observaciones }}</p>
                    </div>

                    <!-- BOTONES ACCIÓN -->
                    <div class="mt-4 flex gap-2 justify-end">
                        <!-- Agregar otro pago (si aún hay saldo pendiente) -->
                        <Link
                            v-if="!item.estado_pago"
                            :href="route('pagos.create')"
                            class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600"
                        >
                            Agregar pago
                        </Link>

                        <!-- Ver detalle -->
                        <Link
                            :href="route('pagos.show', item.id)"
                            class="px-3 py-1 text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 border border-indigo-600 dark:border-indigo-400 rounded"
                        >
                            Ver detalle
                        </Link>
                    </div>
                </div>
            </div>

            <!-- PAGINACIÓN -->
            <div v-if="pagos.meta?.links" class="mt-8 flex justify-center gap-2">
                <Link
                    v-for="link in pagos.meta.links"
                    :key="link.label"
                    :href="link.url || ''"
                    v-html="link.label"
                    class="px-3 py-1 rounded text-sm"
                    :class="{
                        'bg-indigo-600 text-white': link.active,
                        'bg-gray-200 dark:bg-slate-700 dark:text-gray-200': !link.active,
                    }"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
