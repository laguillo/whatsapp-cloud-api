<template>
<button type="button" class="btn" :onclick="'modal_card_'+id+'.showModal()'">Editar</button>
<dialog :id="'modal_card_'+id" class="modal">
  <div class="modal-box w-11/12 max-w-5xl" style="height: 90%;">
    <h3 class="font-bold text-lg">Card #{{ id+1 }}</h3>
    <div>
        <Buttons v-model:model="model.components.buttons" />
    </div>
    <div class="modal-action">
        <button type="button" class="btn" :onclick="'modal_card_'+id+'.close()'">Cerrar</button>
    </div>
  </div>
</dialog>
</template>
<script setup>
import { defineModel } from 'vue'
import Buttons from './Buttons.vue'

const model = defineModel('model')
const props = defineProps(['id'])
</script>
