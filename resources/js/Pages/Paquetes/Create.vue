<script setup>
import { useForm, Link, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const form = useForm({
    nombre: "",
    precio: "",
    num_sesiones: "",
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Crear Paquete" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Crear Paquete
            </h2>
        </template>

        <div class="py-12 bg-gray-100 dark:bg-slate-900 min-h-screen">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h1 class="text-2xl font-bold mb-6">Nuevo Paquete</h1>

                        <form @submit.prevent="form.post(route('paquetes.store'))" class="space-y-6">

                            <div>
                                <InputLabel for="nombre" value="Nombre del Paquete" />
                                <TextInput
                                    id="nombre"
                                    v-model="form.nombre"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Ej: Paquete 10 Sesiones"
                                />
                                <InputError :message="form.errors.nombre" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="num_sesiones" value="Número de Sesiones" />
                                    <TextInput
                                        id="num_sesiones"
                                        v-model="form.num_sesiones"
                                        type="number"
                                        class="mt-1 block w-full"
                                        placeholder="Ej: 10"
                                    />
                                    <InputError :message="form.errors.num_sesiones" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="precio" value="Precio (Bs.)" />
                                    <TextInput
                                        id="precio"
                                        v-model="form.precio"
                                        type="number"
                                        step="0.01"
                                        class="mt-1 block w-full"
                                        placeholder="Ej: 500"
                                    />
                                    <InputError :message="form.errors.precio" class="mt-2" />
                                </div>
                            </div>

                            <div class="bg-indigo-50 dark:bg-slate-700 p-4 rounded-lg">
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Precio por sesión: <span class="font-bold">Bs. {{ form.precio && form.num_sesiones ? (form.precio / form.num_sesiones).toFixed(2) : '0.00' }}</span>
                                </p>
                            </div>

                            <div class="flex gap-3 justify-end">
                                <Link
                                    :href="route('paquetes.index')"
                                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-gray-100 text-sm shadow-sm"
                                >
                                    Cancelar
                                </Link>

                                <PrimaryButton :disabled="form.processing">
                                    Crear Paquete
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
