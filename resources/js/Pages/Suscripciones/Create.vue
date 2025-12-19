<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Link, router } from "@inertiajs/vue3";

const props = defineProps({
    usuarios: Array,
    membresias: Array,
});

const form = useForm({
    usuario_id: "",
    membresia_id: "",
    fecha_inicio: "",
    tipo_pago: "contado",
    cantidad_cuotas: 4,
    fechas: [],
    montos: [],
});

// Membresía seleccionada
const membresiasSeleccionada = computed(() => {
    return props.membresias.find(m => m.id == form.membresia_id);
});

// Monto por cuota
const montoPorCuota = computed(() => {
    if (!membresiasSeleccionada.value || !form.cantidad_cuotas) return 0;
    return (membresiasSeleccionada.value.precio / parseInt(form.cantidad_cuotas)).toFixed(2);
});

// Actualizar cantidad de cuotas
function actualizarCuotas() {
    const cantidad = parseInt(form.cantidad_cuotas);
    const monto = parseFloat(montoPorCuota.value);

    // Inicializar fechas vacías
    form.fechas = Array(cantidad).fill('');

    // Inicializar montos con el valor calculado
    form.montos = Array(cantidad).fill(monto);

    // Generar fechas automáticas
    generarFechas();
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

// Cambiar fecha de cuota
function cambiarFecha(index, fecha) {
    form.fechas[index] = fecha;
}

// Cambiar monto de cuota
function cambiarMonto(index, monto) {
    form.montos[index] = monto;
}

// Validar antes de enviar
function validarFormulario() {
    if (!form.usuario_id) {
        alert('Selecciona un usuario');
        return false;
    }
    if (!form.membresia_id) {
        alert('Selecciona una membresía');
        return false;
    }
    if (!form.fecha_inicio) {
        alert('Selecciona una fecha de inicio');
        return false;
    }
    if (form.tipo_pago === 'credito') {
        if (form.fechas.some(f => !f)) {
            alert('Completa todas las fechas de vencimiento');
            return false;
        }
        if (form.montos.some(m => !m || parseFloat(m) <= 0)) {
            alert('Completa todos los montos de cuotas');
            return false;
        }
    }
    return true;
}

// Enviar formulario
function enviarFormulario() {
    if (!validarFormulario()) return;

    // Preparar datos según el tipo de pago
    let datosEnviar = {
        usuario_id: form.usuario_id,
        membresia_id: form.membresia_id,
        fecha_inicio: form.fecha_inicio,
        tipo_pago: form.tipo_pago,
    };

    // Solo incluir fechas y montos si es pago a crédito
    if (form.tipo_pago === 'credito') {
        // Asegurar que los montos sean números
        const montosNumeros = [];
        for (let i = 0; i < form.montos.length; i++) {
            const monto = parseFloat(form.montos[i]);
            if (!isNaN(monto) && monto > 0) {
                montosNumeros.push(monto);
            }
        }

        // Asegurar que las fechas sean válidas
        const fechasValidas = [];
        for (let i = 0; i < form.fechas.length; i++) {
            if (form.fechas[i] && form.fechas[i].trim() !== '') {
                fechasValidas.push(form.fechas[i]);
            }
        }

        datosEnviar.cantidad_cuotas = form.cantidad_cuotas;
        datosEnviar.fechas = fechasValidas;
        datosEnviar.montos = montosNumeros;

        console.log('DEBUG - Datos a enviar (CRÉDITO):', datosEnviar);
    } else {
        console.log('DEBUG - Datos a enviar (CONTADO):', datosEnviar);
    }

    // Actualizar los campos del formulario con los datos preparados
    Object.keys(datosEnviar).forEach(key => {
        form[key] = datosEnviar[key];
    });

    // Usar form.post() para aprovechar el manejo automático de Inertia
    form.post(route('suscripciones.store'), {
        preserveState: false,
        preserveScroll: false,
        onSuccess: () => {
            console.log('Suscripción creada exitosamente - redirigiendo...');
        },
        onError: (errors) => {
            console.error('Errores al crear suscripción:', errors);
        }
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                Nueva Suscripción
            </h2>
        </template>

        <div class="py-12 bg-gray-100 dark:bg-slate-900 min-h-screen">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <form @submit.prevent="enviarFormulario" class="space-y-6">

                            <!-- INFORMACIÓN BÁSICA -->
                            <div class="border-b border-gray-200 dark:border-slate-700 pb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Información
                                    Básica</h3>

                                <!-- Usuario -->
                                <div class="mb-4">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Usuario</label>
                                    <select v-model="form.usuario_id"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white">
                                        <option value="">-- Seleccione un usuario --</option>
                                        <option v-for="u in props.usuarios" :key="u.id" :value="u.id">
                                            {{ u.nombre }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Membresía -->
                                <div class="mb-4">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Membresía</label>
                                    <select v-model="form.membresia_id"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white">
                                        <option value="">-- Seleccione una membresía --</option>
                                        <option v-for="m in props.membresias" :key="m.id" :value="m.id">
                                            {{ m.nombre }} - Bs. {{ m.precio }} ({{ m.duracion_dias }} días)
                                        </option>
                                    </select>
                                </div>

                                <!-- Fecha de Inicio -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha
                                        de
                                        Inicio</label>
                                    <input type="date" v-model="form.fecha_inicio"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white" />
                                </div>
                            </div>

                            <!-- TIPO DE PAGO -->
                            <div class="border-b border-gray-200 dark:border-slate-700 pb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tipo de Pago
                                </h3>

                                <div class="flex gap-6">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" v-model="form.tipo_pago" value="contado"
                                            class="w-4 h-4 text-indigo-600" />
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">Al Contado</span>
                                    </label>

                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" v-model="form.tipo_pago" value="credito"
                                            class="w-4 h-4 text-indigo-600" />
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">A Crédito</span>
                                    </label>
                                </div>
                            </div>

                            <!-- CONFIGURACIÓN DE CRÉDITO (Si aplica) -->
                            <div v-if="form.tipo_pago === 'credito'"
                                class="border-b border-gray-200 dark:border-slate-700 pb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Configuración de
                                    Cuotas
                                </h3>

                                <!-- Cantidad de cuotas -->
                                <div class="mb-4">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cantidad
                                        de
                                        Cuotas</label>
                                    <select v-model="form.cantidad_cuotas" @change="actualizarCuotas"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white">
                                        <option v-for="n in 12" :key="n" :value="n">{{ n }} cuota{{ n > 1 ? 's' : '' }}
                                        </option>
                                    </select>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        Monto por cuota: <span class="font-bold">Bs. {{ montoPorCuota }}</span>
                                    </p>
                                </div>

                                <!-- Generar fechas automáticas -->
                                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg mb-4">
                                    <button type="button" @click="generarFechas"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm">
                                        Generar fechas automáticas (semanales)
                                    </button>
                                </div>

                                <!-- Fechas y montos de cuotas -->
                                <div class="space-y-3">
                                    <div v-for="(fecha, index) in form.fechas" :key="index"
                                        class="flex gap-3 items-end">
                                        <div class="flex-1">
                                            <label
                                                class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                                Cuota {{ index + 1 }} - Vencimiento
                                            </label>
                                            <input type="date" :value="fecha"
                                                @input="cambiarFecha(index, $event.target.value)"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white" />
                                        </div>

                                        <div class="w-32">
                                            <label
                                                class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                                Monto (Bs.)
                                            </label>
                                            <input type="number" step="0.01" :value="form.montos[index]"
                                                @input="cambiarMonto(index, $event.target.value)"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Resumen de crédito -->
                                <div
                                    class="bg-indigo-50 dark:bg-slate-700 p-4 rounded-lg mt-4 border border-indigo-200 dark:border-slate-600">
                                    <h4 class="font-semibold text-indigo-900 dark:text-indigo-100 mb-2">Resumen del Plan
                                    </h4>
                                    <div class="space-y-1 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-700 dark:text-gray-300">Monto Total:</span>
                                            <span class="font-bold">Bs. {{ (membresiasSeleccionada?.precio ||
                                                0).toFixed(2)
                                                }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-700 dark:text-gray-300">Cuotas:</span>
                                            <span class="font-bold">{{ form.cantidad_cuotas }}</span>
                                        </div>
                                        <div
                                            class="border-t border-indigo-200 dark:border-slate-600 pt-1 mt-1 flex justify-between">
                                            <span class="text-gray-700 dark:text-gray-300">Por Cuota:</span>
                                            <span class="font-bold text-indigo-600 dark:text-indigo-400">Bs. {{
                                                montoPorCuota
                                                }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BOTONES -->
                            <div class="flex gap-3 justify-end">
                                <Link :href="route('suscripciones.index')"
                                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-gray-100 text-sm shadow-sm">
                                    Cancelar
                                </Link>

                                <button type="submit" :disabled="form.processing"
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white rounded text-sm shadow-sm">
                                    Crear Suscripción
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
