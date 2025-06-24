<template>
<modal :show="modelValue" @show="onShow">
 <div class="p-6">
    <h2 class="text-lg font-medium text-gray-900">
    Create New Folder
    </h2>
    <div class="mt-6">
        <InputLabel for="folderName" value="Folder Name"/>
        <TextInput type="text" id="folderName" name="folderName" placeholder="Folder Name" v-model="form.name"
        ref="folderNameInput"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:"
        :class="form.errors.name ? 'border-red-500 focus:border-red-500  focus:ring-red-500' : ''"
        @keyup.enter="createFolder"/>
        <InputError :message="form.errors.name"  class="mt-1 text-red-500 text-xs"/>
        </div>
        <div class="mt-6 flex items-center justify-end gap-x-6">
            <SecondaryButton @click="closeModal" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md">Cancel</SecondaryButton>
            <PrimaryButton  type="submit"  
             :class="{ 'opacity-25': form.processing }"
                               @click="createFolder" :disable="form.processing"
             class="bg-blue-600 text-white px-4 py-2 rounded-md">
                Create
            </PrimaryButton>
        </div>
 </div>
</modal>

</template>
<script setup>

import Modal from '@/components/Modal.vue'
import InputLabel from '../InputLabel.vue'
import TextInput from '@/components/TextInput.vue'
import InputError from '@/components/InputError.vue'
import PrimaryButton from '@/components/PrimaryButton.vue'
import SecondaryButton from '@/components/SecondaryButton.vue'
import { useForm } from '@inertiajs/vue3'
import {nextTick, ref} from 'vue'



const form = useForm({
    name: ''
})

//refs

const folderNameInput = ref(null);

//props
const {modelValue} = defineProps({
    modelValue: Boolean,
})

const emit = defineEmits(['update:modelValue'])

//methods
function onShow(){
    nextTick(()=>{
        folderNameInput.value.focus();
    })
}
function createFolder(){
    form.post(route('folder.create'),{
        preserveScroll:true,
        onSuccess:()=>{
            closeModal();
            form.reset();
            //show sucess notification
        },
        onError:()=>{
            //show error notification
            folderNameInput.value.focus();
        }
    })
}
function closeModal(){
   emit('update:modelValue');
   form.clearErrors();
   form.reset();
}
</script>
<style scoped></style>