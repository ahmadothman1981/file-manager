<template>
    <nav class="min-w-[200px]">
       
                <Link class="h-[80px] px-3 flex items-center gap-3" :href="route('myFiles')">
                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                     LaravelStorage
                </Link>
               
        
        <div class="px-3">
            <CreateNewDropdown />
            <div class="py-3">
                <NavLink :href="route('myFiles')" :active="route().current('myFiles')" >My Files</NavLink>
                <NavLink href="/" >Upload Files</NavLink>
                <NavLink href="/">Upload Folders</NavLink>
                <NavLink href="/">Trash</NavLink>
            </div>
        </div>
    </nav>
</template>
<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import CreateNewDropdown from './CreateNewDropdown.vue';
import NavLink from '../NavLink.vue';
import { Link } from '@inertiajs/vue3';


</script>
<style></style>