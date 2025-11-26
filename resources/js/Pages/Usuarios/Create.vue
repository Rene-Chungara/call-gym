<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    nombre: '',
    email: '',
    password: '',
    password_confirmation: '',
    is_propietario: false,
    is_secretaria: false,
    is_instructor: false,
    is_clientes: true,
});

function submit() {
    form.post(route('usuarios.store'));
}
</script>

<template>
    <AuthenticatedLayout>

        <Head title="Nuevo usuario" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                Nuevo usuario
            </h2>
        </template>

        <div class="py-12 bg-gray-100 dark:bg-slate-900 min-h-screen">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Nombre -->
                            <div>
                                <label class="block text-sm font-medium mb-1">
                                    Nombre
                                </label>
                                <input v-model="form.nombre" type="text"
                                    class="mt-1 w-full border border-gray-300 dark:border-slate-600 rounded px-3 py-2 text-sm bg-white dark:bg-slate-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                <div v-if="form.errors.nombre" class="text-red-500 text-xs mt-1">
                                    {{ form.errors.nombre }}
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium mb-1">
                                    Email
                                </label>
                                <input v-model="form.email" type="email"
                                    class="mt-1 w-full border border-gray-300 dark:border-slate-600 rounded px-3 py-2 text-sm bg-white dark:bg-slate-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <!-- Passwords -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">
                                        Contraseña
                                    </label>
                                    <input v-model="form.password" type="password"
                                        class="mt-1 w-full border border-gray-300 dark:border-slate-600 rounded px-3 py-2 text-sm bg-white dark:bg-slate-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                    <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">
                                        {{ form.errors.password }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">
                                        Confirmar contraseña
                                    </label>
                                    <input v-model="form.password_confirmation" type="password"
                                        class="mt-1 w-full border border-gray-300 dark:border-slate-600 rounded px-3 py-2 text-sm bg-white dark:bg-slate-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                </div>
                            </div>

                            <!-- Roles -->
                            <div class="border-t border-gray-200 dark:border-slate-700 pt-4">
                                <p class="text-sm font-medium mb-2">Roles</p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" v-model="form.is_propietario"
                                            class="rounded border-gray-300 dark:border-slate-600 dark:bg-slate-900" />
                                        <span>Propietario (Admin)</span>
                                    </label>

                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" v-model="form.is_secretaria"
                                            class="rounded border-gray-300 dark:border-slate-600 dark:bg-slate-900" />
                                        <span>Secretaria</span>
                                    </label>

                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" v-model="form.is_instructor"
                                            class="rounded border-gray-300 dark:border-slate-600 dark:bg-slate-900" />
                                        <span>Instructor</span>
                                    </label>

                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" v-model="form.is_clientes"
                                            class="rounded border-gray-300 dark:border-slate-600 dark:bg-slate-900" />
                                        <span>Cliente</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-end space-x-3 pt-2">
                                <Link :href="route('usuarios.index')"
                                    class="px-4 py-2 text-sm rounded border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700">
                                Cancelar
                                </Link>

                                <button type="submit"
                                    class="px-4 py-2 text-sm rounded bg-blue-600 hover:bg-blue-700 text-white shadow-sm"
                                    :disabled="form.processing">
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
