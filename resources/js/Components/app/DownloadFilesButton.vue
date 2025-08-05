<template>
<PrimaryButton @click="download" class="mr-3 ml-3">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-1">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
</svg>

        Download
</PrimaryButton>


</template>
<script setup>
import { usePage } from "@inertiajs/vue3"
import PrimaryButton from "../PrimaryButton.vue";
import { httpGet } from "@/Helper/http-helper";
import { showErrorDialog } from "@/event-bus";


//uses
const page = usePage();

// const form = useForm({
//     all: null ,
//     ids: [],
//     parent_id: null
// });
//refs

//props
const props = defineProps({
    all: {
        type: Boolean,
        required:false,
        default: false
    },
    ids: {
        type:Array,
        required:false
    }
})

//methods
function download()
{
   if(!props.all && props.ids.length === 0)
   {
    showErrorDialog('Please select at least one file to download.');
    return;
   }
   const p = new URLSearchParams();
   const parentId = page.props.folder?.id;
   if (parentId) {
       p.append('parent_id', parentId);
   }
    if (props.all) {
        p.append('all' , 1);
    } else {
        for (let id of props.ids) {
            p.append('ids[]', id);
        }
    }
   //form.parent_id = page.props.folder.id ;
   httpGet(route('file.download') + '?' + p.toString())
        .then(res => {
            if (!res.url) {
                showErrorDialog(res.message || 'An error occurred while preparing the download.');
                return;
            }
            const a = document.createElement('a');
            a.download = res.fileName;
            a.href = res.url;
            document.body.appendChild(a);
            a.click();
            a.remove();
        }).catch(err => {
            console.error(err);
            showErrorDialog('An error occurred during the download request.');
        });
}



</script>
<style scoped></style>
