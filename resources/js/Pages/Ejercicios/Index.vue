<script setup>
import { computed, ref, watch } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    ejercicios: Object,
    gruposMusculares: Array,
    filters: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const canManage = computed(() => {
    return user.value.is_propietario || user.value.is_instructor;
});

const ejerciciosList = computed(() => {
    return props.ejercicios.data || [];
});

// Filtros
const search = ref(props.filters?.search || '');
const grupoMuscular = ref(props.filters?.grupo_muscular || '');
const dificultad = ref(props.filters?.dificultad || '');

// Custom debounce function
const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const handleSearch = debounce(() => {
    applyFilters();
}, 300);

const applyFilters = () => {
    router.get(route('ejercicios.index'), {
        search: search.value,
        grupo_muscular: grupoMuscular.value,
        dificultad: dificultad.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

watch([grupoMuscular, dificultad], () => {
    applyFilters();
});

const getDificultadColor = (dificultad) => {
    switch (dificultad) {
        case 'principiante': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'intermedio': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'avanzado': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                Ejercicios
            </h2>
        </template>

        <div class="max-w-7xl mx-auto mt-6 px-4">

            <!-- Filtros y Acciones -->
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">

                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                    <!-- Búsqueda -->
                    <input v-model="search" @input="handleSearch" type="text" placeholder="Buscar ejercicio..."
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-slate-900 dark:border-slate-700 dark:text-white w-full sm:w-64">

                    <!-- Filtro Grupo Muscular -->
                    <select v-model="grupoMuscular"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-slate-900 dark:border-slate-700 dark:text-white w-full sm:w-48">
                        <option value="">Todos los grupos</option>
                        <option v-for="grupo in gruposMusculares" :key="grupo" :value="grupo">{{ grupo }}</option>
                    </select>

                    <!-- Filtro Dificultad -->
                    <select v-model="dificultad"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-slate-900 dark:border-slate-700 dark:text-white w-full sm:w-40">
                        <option value="">Todas las dificultades</option>
                        <option value="principiante">Principiante</option>
                        <option value="intermedio">Intermedio</option>
                        <option value="avanzado">Avanzado</option>
                    </select>
                </div>

                <div v-if="canManage">
                    <Link :href="route('ejercicios.create')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 transition flex items-center gap-2 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Crear Ejercicio
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <div v-for="item in ejerciciosList" :key="item.id"
                    class="rounded-lg bg-white dark:bg-slate-800 shadow-md border border-gray-200 dark:border-slate-700 p-5 hover:shadow-lg transition flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                {{ item.nombre }}
                            </h3>
                            <span
                                :class="['text-xs px-2 py-1 rounded-full capitalize', getDificultadColor(item.dificultad)]">
                                {{ item.dificultad }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                            <span class="font-semibold">Grupo:</span> {{ item.grupo_muscular }}
                        </p>

                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-4 line-clamp-3">
                            {{ item.descripcion }}
                        </p>
                    </div>

                    <div class="flex gap-2 justify-end mt-4">
                        <Link v-if="canManage" :href="route('ejercicios.edit', item.id)"
                            class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600">
                        Editar
                        </Link>

                        <Link :href="route('ejercicios.show', item.id)"
                            class="px-3 py-1 text-sm text-indigo-600 border border-indigo-600 rounded hover:bg-indigo-50 dark:text-indigo-400 dark:border-indigo-400 dark:hover:bg-slate-700">
                        Ver
                        </Link>
                    </div>
                </div>
            </div>

            <div v-if="ejercicios.links && ejercicios.links.length > 3" class="mt-8 flex justify-center gap-2">
                <template v-for="(link, index) in ejercicios.links" :key="index">
                    <Link v-if="link.url" :href="link.url" v-html="link.label" class="px-3 py-1 rounded text-sm" :class="{
                        'bg-indigo-600 text-white': link.active,
                        'bg-gray-200 dark:bg-slate-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-slate-600': !link.active,
                    }" />
                    <span v-else v-html="link.label"
                        class="px-3 py-1 rounded text-sm bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-gray-600 cursor-not-allowed border border-gray-200 dark:border-slate-700"></span>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
