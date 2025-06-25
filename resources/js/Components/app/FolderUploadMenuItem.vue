<template>
 <MenuItem v-slot="{ active }">
                        <a href="#" :class="[
                active ? 'bg-gray-100' : '',
                'block rounded-md px-4 py-2 text-sm text-gray-700 relative',
              ]">Upload Folder
              <input type="file" @change="onChange" class="absolute left-0 top-0 bottom-0 right-0 cursor-pointer"
               multiple  directory webkitdirectory/>
              </a>
                    </MenuItem>
</template>
<script setup>
import { Menu, MenuButton, MenuItems, MenuItem} from '@headlessui/vue'
import {emitter , FILE_UPLOAD_STARTED} from '../../event-bus.js'

function onChange(e){
  emitter.emit(FILE_UPLOAD_STARTED , e.target.files)
}
</script>

<style scoped>

</style>