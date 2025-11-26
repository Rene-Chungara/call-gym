<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Link } from "@inertiajs/vue3";

const props = defineProps({
    suscripcion: Object,
    usuarios: Array,
    membresias: Array,
});

const form = useForm({
    usuario_id: props.suscripcion.usuario_id,
    membresia_id: props.suscripcion.membresia_id,
    fecha_inicio: props.suscripcion.fecha_inicio,
    estado: props.suscripcion.estado,
});
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                Editar Suscripción
            </h2>
        </template>

        <div class="max-w-3xl mx-auto mt-6 bg-white dark:bg-slate-800 p-6 rounded-lg shadow border
            border-gray-200 dark:border-slate-700">

            <!-- SELECT USUARIO -->
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Usuario</label>
            <select
                v-model="form.usuario_id"
                class="w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded"
            >
                <option v-for="u in props.usuarios" :value="u.id">
                    {{ u.nombre }}
                </option>
            </select>

            <!-- SELECT MEMBRESÍA -->
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1 mt-4">Membresía</label>
            <select
                v-model="form.membresia_id"
                class="w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded"
            >
                <option v-for="m in props.membresias" :value="m.id">
                    {{ m.nombre }} ({{ m.duracion_dias }} días)
                </option>
            </select>

            <!-- FECHA INICIO -->
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1 mt-4">Fecha inicio</label>
            <input
                type="date"
                v-model="form.fecha_inicio"
                class="w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded"
            />

            <!-- ESTADO -->
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1 mt-4">Estado</label>
            <select
                v-model="form.estado"
                class="w-full border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded"
            >
                <option :value="1">Activa</option>
                <option :value="0">Inactiva</option>
            </select>

            <!-- BOTONES -->
            <div class="flex justify-end gap-4 mt-6">
                <Link
                    :href="route('suscripciones.index')"
                    class="px-4 py-2 rounded bg-gray-200 dark:bg-slate-700 dark:text-gray-200"
                >
                    Cancelar
                </Link>

                <button
                    @click="form.put(route('suscripciones.update', props.suscripcion.id))"
                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                >
                    Guardar cambios
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
