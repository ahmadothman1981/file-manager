<template>
   <Modal :show="show" class="max-w-md">
        <div class="p-6">
            <h2 class="text-2xl text-red-500 font-semibold  mb-2">Error</h2>
            <p>{{message}}</p>
            <div class="mt-6 flex justify-end">
                <PrimaryButton @click="close">
                    Close
                </PrimaryButton>
            </div>
            </div>
   </Modal>
</template>
<script setup>
import { emitter , SHOW_ERROR_DIALOG } from '../../event-bus.js';
import { onMounted , ref } from 'vue';
import Modal from '../Modal.vue';
import PrimaryButton from '../PrimaryButton.vue';

const show = ref(false);
const message = ref('');

const emit = defineEmits(['close']);


function close()
{
    show.value = false;
    message.value = '';
}
onMounted(() => {
  emitter.on(SHOW_ERROR_DIALOG , ({message : msg}) => {
    show.value = true;
    message.value = msg;
  })
})
</script>