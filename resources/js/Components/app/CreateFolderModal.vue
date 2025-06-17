<template>

<modal :show="modelValue" @show="onShow" maxWidth="sm">
    <div class="p-6">
       
            <h2 class="text-lg font-medium text-gray-900">
                Create New Folder
            </h2>
            <div class="mt-6">
                <InputLabel for="folderName" value="FolderName" />
                <TextInput  type="text" id="folderName" v-model="form.name"
                ref="folderNameInput"
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                 :class="form.errors.name ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                placeholder="Folder Name"
                 @keyup.enter="createFolder"
                 />
                 <InputError :message="form.errors.name" class="mt-1 text-red-500 text-sm" />
            </div>
        </div>
        <div class="mt-6 flex justify-end">
          <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
          <PrimaryButton 
          class="ml-3" :class="{'opacity-25 ' : form.processing}"
           @click="createFolder" :disable="form.processing">
           Create</PrimaryButton>
         
        </div>
   
</modal>




</template>

<script setup>
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { nextTick, ref } from 'vue';



//ref 
const folderNameInput = ref(null);

const form =useForm({
    name: ''
})

//props
const {modelValue} = defineProps({
    modelValue: Boolean
})
const emit = defineEmits(['update:modelValue']);

//methods
function onShow(){
   nextTick(() => {
    folderNameInput.value.focus();
   });
}
function createFolder(){
 form.post(route('folder.create'),
{
    preserveScroll: true,
    onSuccess: () => {
        closeModal();
        form.reset();
        //show success notification 
    },
    onError: () => {
        folderNameInput.value.focus();
        //show error notification
    }

});
}
function closeModal(){
    emit('update:modelValue');
    form.clearErrors();
    form.reset();
}
</script>
<style></style>