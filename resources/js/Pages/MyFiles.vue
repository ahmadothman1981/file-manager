<template>
<AuthenticatedLayout>
  <table class="min-w-full  ">
   
    <thead class="bg-grey-100 border-b ">
        <tr >
            <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                Name
            </th>
              <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
               Owner
            </th>
              <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                Last Modified
            </th>
              <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
               Size
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="file of files.data" :key="file.id" @dblclick="openFolder(file)">
            <td class="bg-white border-b transition duration-300 ease-in-out hover:bg-gray-100 ">{{file.name}}</td>
            <td class="bg-white border-b transition duration-300 ease-in-out hover:bg-gray-100 ">{{file.owner}}</td>
            <td class="bg-white border-b transition duration-300 ease-in-out hover:bg-gray-100 ">{{file.updated_at}}</td>
            <td class="bg-white border-b transition duration-300 ease-in-out hover:bg-gray-100 ">{{file.size}}</td>
          
        </tr>
        </tbody>
  </table>
   <div v-if="!files.data.length" class="bg-white shadow-md rounded-md p-8 text-center">
      <p class="text-center text-gray-500">No files found</p>
      </div>
</AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';
import {router} from '@inertiajs/vue3'
//props
const {files} = defineProps({
    files: Object,
});
//methods 
function openFolder(file){
  if(!file.is_folder){
    return;
  }
  router.visit(route('myFiles',{folder:file.path}))
}
</script>
<style></style>