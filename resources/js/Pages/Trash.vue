<template>
<AuthenticatedLayout>
<nav class="flex items-center justify-end p-1 mb-3">
 
  <div>
   <RestoreFilesButton  :all-selected = "allSelected" :selected-ids="selectedIds"/>
  </div>
</nav>

<div class="flex-1 overflow-auto">

  <table class="min-w-full  ">
  
    <thead class="bg-grey-100 border-b ">
        <tr >
           <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left w-[30px] max-w-[30px] pr-0">
                <Checkbox @change="onSelectAllChange" v-model:checked="allSelected"/>
            </th>
            <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                Name
            </th>
              <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
               Path
            </th>
           
        </tr>
        </thead>
        <tbody>
        <tr v-for="file of allFiles.data" :key="file.id" @click="$event => toggleFileSelect(file)">
          <td   class="bg-white border-b transition duration-300 ease-in-out hover:bg-blue-100 w-[30px] max-w-[30px] pr-0"
          :class="(selected[file.id] || allSelected) ? 'bg-blue-50' : 'bg-white'">
            <Checkbox @change="$event => onSelectCheckbox(file)" v-model="selected[file.id]" :checked="selected[file.id] || allSelected "/>
            </td>
            <td class="bg-white border-b transition duration-300 ease-in-out hover:bg-gray-100 flex items-center"><FileIcon :file="file"/> {{file.name}}</td>
            <td class="bg-white border-b transition duration-300 ease-in-out hover:bg-gray-100 ">{{file.path}}</td>
        </tr>
        </tbody>
  </table>
  
   <div v-if="!allFiles.data.length" class="bg-white shadow-md rounded-md p-8 text-center">
      <p class="text-center text-gray-500">No files found</p>
      </div>
      <div ref="loadMoreIntersect"></div>
      </div>
</AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';
import Checkbox from '../Components/Checkbox.vue'
import FileIcon from '../Components/app/FileIcon.vue'
import RestoreFilesButton from '@/Components/app/RestoreFilesButton.vue';
import { httpGet } from '../Helper/http-helper'
import { onMounted , onUpdated, ref } from 'vue';
import { computed } from 'vue';

//props
const props = defineProps({
    files: Object,
    folder:Object,
    ancestors:Object
});
//refs 
const allSelected = ref(false);
const selected = ref({});
const loadMoreIntersect = ref(null);
const allFiles = ref({
      data:props.files.data ,
     next: props.files.links.next 
    });
  //computed 
  const selectedIds = computed(() => Object.entries(selected.value).filter(entry => entry[1]).map(entry => entry[0]))
//methods 

function loadMore(){
  console.log("load more");
   console.log(props.files.links.next);
   if(allFiles.value.next == null){
    return ;
   }
   httpGet(allFiles.value.next).then(res => {
      allFiles.value.data = [...allFiles.value.data , ...res.data];
      allFiles.value.next = res.links.next;
   })

}
function onSelectAllChange()
{
  allFiles.value.data.forEach(file => {
    selected.value[file.id] = allSelected.value;
     });
}
function toggleFileSelect(file){
  selected.value[file.id] = !selected.value[file.id];
  onSelectCheckbox(file);
}
function onSelectCheckbox(file){
  if(!selected.value[file.id]){
    allSelected.value = false;
  }else{
    let checked = true;
    for(let file of allFiles.value.data){
      if(!selected.value[file.id]){
        checked = false;
        break;
      }
    
    }
    allSelected.value = checked;
  }
}
function onDelete(){
  selected.value = {};
  allSelected.value = false;
}
onUpdated(() => {
  allFiles.value = {
    data:props.files.data,
    next:props.files.links.next
  };
})
onMounted(() => {
  //  allFiles.value = {
  //   data:props.files.data,
  //   next:props.files.links.next
  // };
  const observer = new IntersectionObserver((entries) => entries.forEach(entry => entry.isIntersecting && loadMore()), { 
    rootMargin:'-250px 0px 0px'
   })
   observer.observe(loadMoreIntersect.value)
})
</script>
<style></style>