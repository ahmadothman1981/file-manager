<template>
<PrimaryButton @click="download" >

        Download
</PrimaryButton>

<ConfirmationDialog :show="showDeleteDialog" message="Are you sure you want to delete all files?" @cancel="onDeleteCancel" @confirm="onDeleteConfirm">
</ConfirmationDialog>
</template>
<script setup>
import ConfirmationDialog from "@/components/ConfirmationDialog.vue"
import { ref } from 'vue';
import { useForm, usePage} from "@inertiajs/vue3"
import { showErrorDialog } from "@/event-bus";
import PrimaryButton from "../PrimaryButton.vue";


//uses
const page = usePage();
const deleteFilesForm = useForm({
    all: null ,
    ids: [],
    parent_id: null
});
//refs
const showDeleteDialog = ref(false);
//props
const props = defineProps({
    deleteAll: {
        type: Boolean,
        required:false,
        default: false
    },
    deleteIds: {
        type:Array,
        required:false
    }
})
const emit = defineEmits(['deleted'])
//methods
function onDeleteClick()
{
    if(!props.deleteAll && !props.deleteIds.length)
    {
        showErrorDialog('No files selected');
        return 
    }
    showDeleteDialog.value = true;
    //console.log("DELETE");
}
function onDeleteCancel()
{
    showDeleteDialog.value = false;
}
function onDeleteConfirm()
{
    deleteFilesForm.parent_id = page.props.folder?.id || null;
    if(props.deleteAll){
        deleteFilesForm.all = true;
    }else{
        deleteFilesForm.ids = props.deleteIds;
    }
    deleteFilesForm.delete(route('file.destroy'),{
        onSuccess: () => {
            showDeleteDialog.value = false;
            emit('deleted');
            //success notification later
        }
    });
   // console.log("this is deleting message" , props.deleteIds);

}
</script>
<style scoped></style>

