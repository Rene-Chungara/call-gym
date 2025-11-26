<template>
    <div>
        <form :action="route('pagos.stripe')" method="POST" @submit.prevent="submitPaymentForm">
            <input type="hidden" name="suscripcion_id" :value="suscripcion_id">
            <input type="hidden" name="monto_total" :value="monto_total">
            <input type="hidden" name="monto_abono" :value="monto_abono">

            <button type="submit" class="w-full bg-indigo-600 text-white rounded px-4 py-2">
                Pagar con Stripe
            </button>
        </form>
    </div>
</template>

<script setup>
defineProps({
    suscripcion_id: Number,
    monto_total: Number,
    monto_abono: Number
});

const submitPaymentForm = () => {
    // Aquí puedes agregar más lógica si es necesario
    console.log('Formulario enviado');
}
</script>
