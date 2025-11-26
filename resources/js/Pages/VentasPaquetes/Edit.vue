<script setup>
import { computed } from "vue";
import { useForm, Link, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    venta: Object,
    usuarios: Array,
    paquetes: Array,
});

const form = useForm({
    usuario_id: props.venta.usuario_id,
    paquete_id: props.venta.paquete_id,
    sesiones_restantes: props.venta.sesiones_restantes,
    fecha_compra: props.venta.fecha_compra,
});

const paqueteSeleccionado = computed(() => {
    return props.paquetes.find(p => p.id == form.paquete_id);
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Editar Venta de Paquete" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Editar Venta de Paquete
            </h2>
        </template>

        <div class="py-12 bg-gray-100 dark:bg-slate-900 min-h-screen">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h1 class="text-2xl font-bold mb-6">Editar Venta de Paquete</h1>

                        <form @submit.prevent="form.put(route('venta-paquetes.update', props.venta.id))" class="space-y-6">

                            <div>
                                <InputLabel for="usuario_id" value="Seleccionar Cliente" />
                                <select
                                    id="usuario_id"
                                    v-model="form.usuario_id"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white"
                                >
                                    <option value="">-- Seleccione un cliente --</option>
                                    <option v-for="u in props.usuarios" :key="u.id" :value="u.id">
                                        {{ u.nombre }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.usuario_id" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="paquete_id" value="Seleccionar Paquete" />
                                <select
                                    id="paquete_id"
                                    v-model="form.paquete_id"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-white"
                                >
                                    <option value="">-- Seleccione un paquete --</option>
                                    <option v-for="p in props.paquetes" :key="p.id" :value="p.id">
                                        {{ p.nombre }} - {{ p.num_sesiones }} sesiones
                                    </option>
                                </select>
                                <InputError :message="form.errors.paquete_id" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="sesiones_restantes" value="Sesiones Restantes" />
                                <TextInput
                                    id="sesiones_restantes"
                                    v-model="form.sesiones_restantes"
                                    type="number"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.sesiones_restantes" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="fecha_compra" value="Fecha de Compra" />
                                <TextInput
                                    id="fecha_compra"
                                    v-model="form.fecha_compra"
                                    type="date"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.fecha_compra" class="mt-2" />
                            </div>

                            <div v-if="paqueteSeleccionado" class="bg-indigo-50 dark:bg-slate-700 p-4 rounded-lg">
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Información</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Total de sesiones:</span>
                                        <span class="font-bold">{{ paqueteSeleccionado.num_sesiones }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Restantes:</span>
                                        <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ form.sesiones_restantes }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Utilizadas:</span>
                                        <span class="font-bold text-orange-600 dark:text-orange-400">{{ paqueteSeleccionado.num_sesiones - form.sesiones_restantes }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-3 justify-end">
                                <Link
                                    :href="route('venta-paquetes.index')"
                                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-gray-100 text-sm shadow-sm"
                                >
                                    Cancelar
                                </Link>

                                <PrimaryButton :disabled="form.processing">
                                    Actualizar
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
