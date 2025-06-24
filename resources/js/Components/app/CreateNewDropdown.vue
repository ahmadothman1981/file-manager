<template>
   <Menu as="div" class="relative block text-left">
       
            <MenuButton
                class="inline-flex w-full justify-center rounded-md px-4 py-2 text-sm font-medium text-gray-800 hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
            >
               Create New

               
            </MenuButton>
       

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <MenuItems
class="flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"            >
                 <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                      <a  href="#" @click.prevent="showCreateFolderModal"
                       :class="[
                active ? 'bg-gray-100' : '',
                'block rounded-md px-4 py-2 text-sm text-gray-700',
              ]"> New Folder</a>
                    </MenuItem>
                 </div>
                <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                        <a  :class="[
                active ? 'bg-gray-100' : '',
                'block rounded-md px-4 py-2 text-sm text-gray-700',
              ]">Upload File</a>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                        <a  :class="[
                active ? 'bg-gray-100' : '',
                'block rounded-md px-4 py-2 text-sm text-gray-700',
              ]">Upload Folder</a>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
    <CreateFolderModal v-model="createFolderModal"/>
</template>
<script setup>
   import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
   import CreateFolderModal from '@/components/app/CreateFolderModal.vue'
    import { ref } from 'vue'

//refs
const createFolderModal = ref(false);

//methods
function showCreateFolderModal(){
    createFolderModal.value = true;
console.log(createFolderModal.value);

}
</script>
<style></style>