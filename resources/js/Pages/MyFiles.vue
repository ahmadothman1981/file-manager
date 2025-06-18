<template>
<AuthenticatedLayout>
   <table  class="min-w-full">
    <div v-if="!files.data.length"  class="py-10 text-center">
        <p>No files found.</p>
    </div>
    <thead class="bg-gray-50 boarder-b">
       <tr>
        <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
            Name
        </th>
        <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
            Owner
        </th>
        <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
            Size
        </th>
        <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
            Created at
        </th>
        <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
            Updated at
        </th>
       </tr>
    </thead>
    <tbody>
        <tr v-for="file of files.data" :key="file.id" class="bg-white border-b transition duration-300 ease-in-out hover:bg-gray-100">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 ">
                {{file.name}}
            </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 ">
                {{file.owner}}
            </td>
         <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 ">
              {{file.size}}
            </td>
     
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 ">
              {{file.created_at}} 
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 ">
                {{file.updated_at}}
            </td>
        </tr>
            
    </tbody>
   </table>

        

</AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';

const {files} = defineProps({
    files : Object
});

</script>
<style></style>