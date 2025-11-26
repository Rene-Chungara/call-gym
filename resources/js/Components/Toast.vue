<script setup>
const props = defineProps({
    message: String,
    type: {
        type: String,
        default: 'success', // success | error
    },
});
</script>

<template>
    <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-2">
        <div v-if="message" class="fixed z-50 bottom-4 right-4 max-w-sm flex items-start space-x-3 px-4 py-3 rounded-lg shadow-lg
                bg-green-50 text-green-800 border border-green-200
                dark:bg-green-900 dark:text-green-100 dark:border-green-700"
            :class="type === 'error' && 'bg-red-50 text-red-800 border-red-200 dark:bg-red-900 dark:text-red-100 dark:border-red-700'">
            <div class="mt-0.5">
                <svg v-if="type === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>

                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M4.93 4.93l14.14 14.14" />
                </svg>
            </div>
            <div class="text-sm font-medium">
                {{ message }}
            </div>
        </div>
    </transition>
</template>
