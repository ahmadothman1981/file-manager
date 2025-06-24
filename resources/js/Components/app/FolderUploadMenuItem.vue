<template>
 <MenuItem v-slot="{ active }">
                        <a  :class="[
                active ? 'bg-gray-100' : '',
                'block rounded-md px-4 py-2 text-sm text-gray-700',
              ]">Upload Folder</a>
                    </MenuItem>
</template>
<script setup>
import { Menu, MenuButton, MenuItems, MenuItem} from '@headlessui/vue'
</script>

<style scoped>

</style>