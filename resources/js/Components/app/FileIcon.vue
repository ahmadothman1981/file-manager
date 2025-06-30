<template>
<span class="w-6 h-6 inline-flex items-center justify-center mr-2">
    <svg v-if="file.is_folder" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path d="M19.5 21a3 3 0 0 0 3-3v-4.5a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3V18a3 3 0 0 0 3 3h15ZM1.5 10.146V6a3 3 0 0 1 3-3h5.379a2.25 2.25 0 0 1 1.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 0 1 3 3v1.146A4.483 4.483 0 0 0 19.5 9h-15a4.483 4.483 0 0 0-3 1.146Z" />
</svg>
<template v-else>
<img class="max-w-full" v-if="isImage(file)" :src="'/images/icons/photo.png'">
<img class="max-w-full" v-else-if="isPDF(file)" :src="`/images/icons/pdf.png`">
  <img class="max-w-full" v-else-if="isAudio(file)" :src="`/images/icons/speaker-filled-audio-tool.png`">
  <img class="max-w-full" v-else-if="isVideo(file)" :src="`/images/icons/multimedia.png`">
 <img class="max-w-full" v-else-if="isWord(file)" :src="`/images/icons/word.png`">
 <img class="max-w-full" v-else-if="isExcel(file)" :src="`/images/icons/excel.png`">
  <img class="max-w-full" v-else-if="isZip(file)" :src="`/images/icons/zip.png`">
 <img class="max-w-full" v-else-if="isText(file)" :src="`/images/icons/text-format.png`">
 <img class="max-w-full" v-else :src="`/images/icon/attach-file.png`">
</template>
</span>

</template>
<script setup>
import{ isImage} from '../../Helper/file-helper'
import{ isPDF} from '../../Helper/file-helper'
import{ isAudio} from '../../Helper/file-helper'
import{ isVideo} from '../../Helper/file-helper'
import{ isWord} from '../../Helper/file-helper'
import{ isExcel} from '../../Helper/file-helper'
import{ isZip} from '../../Helper/file-helper'
import{ isText} from '../../Helper/file-helper'
//props
const {file} = defineProps({
    file: Object
})
</script>
