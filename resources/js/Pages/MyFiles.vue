<template>
    <AuthenticatedLayout>
        <div v-if="!files.data || !files.data.length" class="py-10 text-center">
            <p>No files found.</p>
        </div>

        <table v-else class="min-w-full table-auto">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">Name</th>
                    <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">Owner</th>
                    <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">Size</th>
                    <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">Created at</th>
                    <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">Updated at</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="file in files.data"
                    :key="file.id"
                    @click="openFolder(file)"
                    role="button"
                    :aria-label="`Open folder ${file.name}`"
                    class="bg-white border-b transition duration-300 ease-in-out hover:bg-gray-100 cursor-pointer"
                >
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ file.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ file.owner }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ formatBytes(file.size) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ formatDate(file.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ formatDate(file.updated_at) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    files: Object,
});

// Format file size
function formatBytes(bytes) {
    if (!bytes) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Format date
function formatDate(dateString) {
    return new Date(dateString).toLocaleString();
}

// Open folder
function openFolder(file) {
    if (!file.is_folder) return;
    router.visit(route('myFiles', { folder: file.path }));
}
</script>
<style></style>