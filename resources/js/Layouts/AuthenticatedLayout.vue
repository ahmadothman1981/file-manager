<template>
    <div class="h-screen bg-gray-50 flex w-full gap-4">
      <Navigation />
      <main @drop.prevent="handleDrop"  @dragover.prevent="onDragOver" @dragleave.prevent="onDragLeave"
       class="flex flex-col flex-1 px-4 overflow-hidden"  :class="dragOver ? 'dropzone' : ''">
       <template v-if="dragOver">
          <div class="bg-white shadow-md rounded-md p-8 text-center">
            <p class="text-center text-gray-500">Drop files here to upload</p>
          </div>
       </template>
       <template v-else >
        <div class="flex items-center justify-between w-full">
          <SearchForm />
          <UserSettingsDropdown />
        </div>
      
        <div class="flex-1 flex flex-col overflow-hidden">
            <slot />
        </div>
        </template>
      </main>
    </div>
    <ErrorDialog />
    <FormProgress :form="fileUploadForm"/>
    <Notification />
  </template>
<script setup>
import Navigation from '../Components/app/Navigation.vue';
import SearchForm from '../Components/app/SearchForm.vue';
import UserSettingsDropdown from '../Components/app/UserSettingsDropdown.vue';
import FormProgress from '../Components/app/FormProgress.vue';
import Notification from '../Components/Notification.vue';
import { ref , onMounted } from 'vue'
import {emitter , FILE_UPLOAD_STARTED , showErrorDialog, showSuccessNotification } from '../event-bus.js'
import { useForm, usePage } from '@inertiajs/vue3';
import ErrorDialog from '../Components/app/ErrorDialog.vue'

const page = usePage();

const fileUploadForm = useForm({
  files: [],
  parent_id: null,
  relative_paths: [],
  
});
//refs
const dragOver = ref(false);


//methods 

function onDragOver(){
  dragOver.value = true;
}
function onDragLeave(){
  dragOver.value = false;
}
function handleDrop(ev){
    dragOver.value = false;
    const files = ev.dataTransfer.files;
   
    if(!files.length){
      return;
    }
    uploadFiles(files)
}
function uploadFiles(files)
{
 
  fileUploadForm.parent_id = page.props.folder.id;
  fileUploadForm.files = files;
  fileUploadForm.relative_paths = [...files].map(file => file.webkitRelativePath);
  
  fileUploadForm.post(route('file.store') , {
    onSuccess: () => {
      showSuccessNotification(`${files.length} files have been uploaded`)
    },
    onError: errors => {
      let message = '';
      if(Object.keys(errors).length > 0)
      {
        message = Object.values(errors).join(' ');
      }else{
        message = 'File upload failed please try again'
      }  
      showErrorDialog(message)
  },
  onFinish: () => 
  {
    fileUploadForm.clearErrors();
    fileUploadForm.reset();
  }
  });
}

onMounted(() => {
  emitter.on(FILE_UPLOAD_STARTED ,uploadFiles)
})


</script>

<style scoped>
.dropzone{
  width: 100%;
  height: 100%;
  color: #8d8d8d;
  border: 2px dashed #8d8d8d;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;

}

  
</style>
