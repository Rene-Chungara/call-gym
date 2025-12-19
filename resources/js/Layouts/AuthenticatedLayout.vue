<script setup>
import { ref, computed, onMounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

const showingNavigationDropdown = ref(false);
const isDark = ref(false);

const page = usePage();
const user = computed(() => page.props.auth.user);

// Permisos por rol
const isPropietario = computed(() => user.value?.is_propietario);
const isInstructor = computed(() => user.value?.is_instructor);
const isSecretaria = computed(() => user.value?.is_secretaria);
const isCliente = computed(() => user.value?.is_clientes);

const displayName = computed(() => user.value?.nombre || user.value?.name || "Usuario");
const displayEmail = computed(() => user.value?.email || "");

onMounted(() => {
    const savedTheme = localStorage.getItem("theme");
    isDark.value = savedTheme === "dark";
    if (isDark.value) {
        document.documentElement.classList.add("dark");
    }
});

const toggleTheme = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add("dark");
        localStorage.setItem("theme", "dark");
    } else {
        document.documentElement.classList.remove("dark");
        localStorage.setItem("theme", "light");
    }
};

// Menús agrupados
const menuGroups = computed(() => {
    if (isPropietario.value) {
        return [
            {
                name: 'Gestión',
                items: [
                    { name: 'Dashboard', route: 'dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
                    { name: 'Reportes', route: 'reportes.index', icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
                    { name: 'Usuarios', route: 'usuarios.index', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
                ]
            },
            {
                name: 'Membresías',
                items: [
                    { name: 'Membresías', route: 'membresias.index', icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
                    { name: 'Suscripciones', route: 'suscripciones.index', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
                ]
            },
            {
                name: 'Entrenamiento',
                items: [
                    { name: 'Rutinas', route: 'rutinas.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
                    { name: 'Ejercicios', route: 'ejercicios.index', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
                    { name: 'Asistencias', route: 'asistencia-sesion.index', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
                ]
            },
            {
                name: 'Paquetes',
                items: [
                    { name: 'Paquetes', route: 'paquetes.index', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
                    { name: 'Ventas', route: 'venta-paquetes.index', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' },
                ]
            },
        ];
    } else if (isInstructor.value) {
        return [
            {
                name: 'Entrenamiento',
                items: [
                    { name: 'Dashboard', route: 'dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
                    { name: 'Rutinas', route: 'rutinas.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
                    { name: 'Ejercicios', route: 'ejercicios.index', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
                    { name: 'Asistencias', route: 'asistencia-sesion.index', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
                ]
            },
        ];
    } else if (isSecretaria.value) {
        return [
            {
                name: 'Administración',
                items: [
                    { name: 'Dashboard', route: 'dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
                    { name: 'Membresías', route: 'membresias.index', icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
                    { name: 'Suscripciones', route: 'suscripciones.index', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
                    { name: 'Paquetes', route: 'paquetes.index', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
                    { name: 'Ventas', route: 'venta-paquetes.index', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' },
                ]
            },
        ];
    } else if (isCliente.value) {
        return [
            {
                name: 'Mi Área',
                items: [
                    { name: 'Dashboard', route: 'dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
                    { name: 'Comprar Membresía', route: 'membresias.catalogo', icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
                    { name: 'Mi Progreso', route: 'rutinas.mi-progreso', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
                    { name: 'Mi Suscripción', route: 'suscripciones.index', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
                ]
            },
        ];
    }
    return [];
    return [];
});

// Global Search Logic
const searchQuery = ref('');
const searchResults = ref([]);
const showResults = ref(false);
let searchTimeout = null;

const handleSearch = () => {
    if (searchTimeout) clearTimeout(searchTimeout);

    if (searchQuery.value.length < 2) {
        searchResults.value = [];
        return;
    }

    searchTimeout = setTimeout(async () => {
        try {
            const response = await axios.get(route('global.search'), {
                params: { query: searchQuery.value }
            });
            // Convert object to array if needed, though controller returns array-like object or array
            searchResults.value = Object.values(response.data);
            showResults.value = true;
        } catch (error) {
            console.error('Error searching:', error);
        }
    }, 300);
};
</script>

<template>
    <div
        class="min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors duration-200 selection:bg-lime-500 selection:text-white">
        <!-- NAVBAR -->
        <nav
            class="bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 sticky top-0 z-50 shadow-sm transition-colors duration-200">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">

                    <!-- LEFT SIDE -->
                    <div class="flex items-center gap-8">
                        <!-- LOGO -->
                        <Link :href="route('dashboard')" class="flex items-center gap-3">
                            <img src="/img/image.png" alt="MAROMBA Training Center" class="h-10 w-auto" />
                            <span class="text-xl font-bold text-gray-900 dark:text-white hidden sm:block">MAROMBA</span>
                        </Link>

                        <!-- DESKTOP NAV LINKS - Agrupados -->
                        <div class="hidden lg:flex items-center gap-1">
                            <template v-for="group in menuGroups" :key="group.name">
                                <Dropdown align="left" width="48">
                                    <template #trigger>
                                        <button
                                            class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-lime-600 dark:hover:text-lime-400 hover:bg-lime-50 dark:hover:bg-slate-700 rounded-lg transition">
                                            {{ group.name }}
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </template>
                                    <template #content>
                                        <Link v-for="item in group.items" :key="item.route" :href="route(item.route)"
                                            class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-lime-50 dark:hover:bg-slate-700 hover:text-lime-700 dark:hover:text-lime-400 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    :d="item.icon" />
                                            </svg>
                                            {{ item.name }}
                                        </Link>
                                    </template>
                                </Dropdown>
                            </template>
                        </div>
                    </div>

                    <!-- SEARCH BAR -->
                    <div class="hidden md:flex flex-1 max-w-lg mx-8 relative">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" v-model="searchQuery" @input="handleSearch" @focus="showResults = true"
                                @blur="setTimeout(() => showResults = false, 200)"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg leading-5 bg-gray-50 dark:bg-slate-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:border-lime-500 sm:text-sm transition duration-150 ease-in-out"
                                placeholder="Buscar usuarios, rutinas, suscripciones..." />

                            <!-- Search Results Dropdown -->
                            <div v-if="showResults && searchResults.length > 0"
                                class="absolute mt-1 w-full bg-white dark:bg-slate-800 shadow-xl rounded-lg py-2 text-base ring-1 ring-black ring-opacity-5 overflow-hidden focus:outline-none sm:text-sm z-50 border border-gray-100 dark:border-slate-700">
                                <div v-for="(result, index) in searchResults" :key="index">
                                    <Link :href="result.url"
                                        class="group cursor-pointer select-none relative py-3 pl-4 pr-4 hover:bg-lime-50 dark:hover:bg-slate-700 block transition border-b border-gray-50 dark:border-slate-700/50 last:border-0">
                                        <div class="flex items-center justify-between">
                                            <span
                                                class="block truncate font-semibold text-gray-900 dark:text-gray-100 group-hover:text-lime-700 dark:group-hover:text-lime-400">
                                                {{ result.title }}
                                            </span>
                                            <span
                                                class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-slate-900 px-2 py-0.5 rounded-full border border-gray-200 dark:border-slate-600">
                                                {{ result.type }}
                                            </span>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 truncate">
                                            {{ result.subtitle }}
                                        </div>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT SIDE -->
                    <div class="hidden md:flex items-center gap-3">
                        <!-- Theme Toggle -->
                        <button @click="toggleTheme"
                            class="p-2 rounded-lg hover:bg-lime-50 dark:hover:bg-slate-700 text-gray-600 dark:text-gray-300 hover:text-lime-600 dark:hover:text-lime-400 transition-colors"
                            title="Cambiar tema">
                            <svg v-if="isDark" class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg v-else class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        <!-- User Dropdown -->
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-lime-50 dark:hover:bg-slate-700 transition-colors">
                                    <div
                                        class="w-8 h-8 bg-gradient-to-br from-lime-500 to-emerald-600 rounded-full flex items-center justify-center shadow-sm">
                                        <span class="text-white text-sm font-semibold">
                                            {{ displayName.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                    <div class="text-left hidden lg:block">
                                        <div class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ displayName
                                        }}</div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">
                                    Perfil
                                </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Cerrar sesión
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>

                    <!-- MOBILE MENU BUTTON -->
                    <div class="md:hidden flex items-center gap-2">
                        <button @click="toggleTheme"
                            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                            <svg v-if="isDark" class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg v-else class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                            <svg v-if="!showingNavigationDropdown" class="w-6 h-6 text-gray-600 dark:text-gray-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg v-else class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- MOBILE MENU -->
            <div v-if="showingNavigationDropdown"
                class="lg:hidden border-t border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 transition-colors duration-200">
                <div class="px-4 py-3 space-y-3">
                    <template v-for="group in menuGroups" :key="group.name">
                        <div>
                            <div
                                class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                {{ group.name }}
                            </div>
                            <Link v-for="item in group.items" :key="item.route" :href="route(item.route)"
                                class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        :d="item.icon" />
                                </svg>
                                {{ item.name }}
                            </Link>
                        </div>
                    </template>
                </div>

                <!-- User Info Mobile -->
                <div class="border-t border-gray-200 dark:border-slate-700 px-4 py-3">
                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-slate-600 to-slate-800 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold">{{ displayName.charAt(0).toUpperCase() }}</span>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ displayName }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ displayEmail }}</div>
                        </div>
                    </div>
                    <Link :href="route('profile.edit')"
                        class="block px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                        Perfil
                    </Link>
                    <Link :href="route('logout')" method="post" as="button"
                        class="block w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                        Cerrar sesión
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header
            class="bg-white dark:bg-slate-800 shadow-sm border-b border-gray-200 dark:border-slate-700 transition-colors duration-200"
            v-if="$slots.header">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
