<template>
<Modal :show="show" max-width="md">
    <div class="p-6">
        <h2 class="text-2xl mb-2 text-red-600 font-semibold">Please Confirm</h2>
        <p>{{ message }}</p>

        <div class="mt-6 flex justify-end">
            <SecondaryButton @click="$event => emit('cancel')"> Cancel </SecondaryButton>
            <DangerButton class="ml-3" @click="$event => emit('confirm')">Confirm</DangerButton>
        </div>
    </div>

</Modal>

</template>
<script setup>
import Modal from "@/components/Modal.vue";
import SecondaryButton from "@/components/SecondaryButton.vue";
import DangerButton from "@/components/DangerButton.vue";

const emit = defineEmits(['confirm','cancel'])
const props = defineProps({
    show:Boolean,
    message:String
})


</script>
<style scoped></style>