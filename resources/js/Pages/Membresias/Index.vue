<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  membresias: Object, // paginator
});

const showDeleteModal = ref(false);
const selected = ref(null);

function confirmDelete(membresia) {
  selected.value = membresia;
  showDeleteModal.value = true;
}

function deleteMembresia() {
  if (!selected.value) return;

  router.delete(route('membresias.destroy', selected.value.id), {
    onFinish: () => {
      showDeleteModal.value = false;
      selected.value = null;
    },
  });
}
</script>

<template>
  <AuthenticatedLayout>

    <Head title="Membresías" />

    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
        Membresías
      </h2>
    </template>

    <div class="py-12 bg-gray-100 dark:bg-slate-900 min-h-screen">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-800 shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="flex justify-between items-center mb-4">
              <h1 class="text-2xl font-bold">Membresías</h1>

              <Link :href="route('membresias.create')"
                class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white text-sm shadow-sm">
              Nueva membresía
              </Link>
            </div>

            <div class="overflow-x-auto">
              <table
                class="min-w-full text-sm bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded">
                <thead>
                  <tr class="border-b bg-gray-50 dark:bg-slate-700">
                    <th class="px-4 py-2 text-left">Nombre</th>
                    <th class="px-4 py-2 text-left">Precio</th>
                    <th class="px-4 py-2 text-left">Duración (días)</th>
                    <th class="px-4 py-2 text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="m in membresias.data" :key="m.id"
                    class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800">
                    <td class="px-4 py-2">{{ m.nombre }}</td>
                    <td class="px-4 py-2">{{ m.precio }}</td>
                    <td class="px-4 py-2">{{ m.duracion_dias }}</td>

                    <td class="px-4 py-2 text-right space-x-3">
                      <Link :href="route('membresias.edit', m.id)"
                        class="text-blue-600 dark:text-blue-400 hover:underline">
                      Editar
                      </Link>

                      <button type="button" class="text-red-600 dark:text-red-400 hover:underline"
                        @click="confirmDelete(m)">
                        Eliminar
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="mt-4 flex justify-between text-sm">
              <div>
                Mostrando {{ membresias.from }} - {{ membresias.to }} de
                {{ membresias.total }}
              </div>
              <div class="space-x-2">
                <Link v-if="membresias.prev_page_url" :href="membresias.prev_page_url"
                  class="px-3 py-1 border rounded border-gray-300 dark:border-slate-600">
                « Anterior
                </Link>
                <Link v-if="membresias.next_page_url" :href="membresias.next_page_url"
                  class="px-3 py-1 border rounded border-gray-300 dark:border-slate-600">
                Siguiente »
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal :show="showDeleteModal" title="Eliminar membresía" @close="showDeleteModal = false">
      <p class="text-sm mb-4">
        ¿Seguro que deseas eliminar la membresía
        <span class="font-semibold">{{ selected?.nombre }}</span>?
        Esta acción no se puede deshacer.
      </p>

      <div class="flex justify-end space-x-2">
        <button type="button"
          class="px-4 py-2 text-sm rounded border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700"
          @click="showDeleteModal = false">
          Cancelar
        </button>

        <button type="button" class="px-4 py-2 text-sm rounded bg-red-600 hover:bg-red-700 text-white"
          @click="deleteMembresia">
          Eliminar
        </button>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>
