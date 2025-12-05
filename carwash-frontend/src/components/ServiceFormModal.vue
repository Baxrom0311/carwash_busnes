<script setup>
import { ref, watch } from 'vue'

// Komponent qabul qiladigan 'props' (kiruvchi ma'lumotlar) o'zgarmaydi
const props = defineProps({
  show: Boolean,
  service: Object
})

// Komponentdan tashqariga yuboriladigan 'events' (hodisalar) o'zgarmaydi
const emit = defineEmits(['close', 'save'])

const form = ref({
  name: '',
  price: 0,
  description: '',
  is_active: true
})

// watch funksiyasi ham deyarli o'zgarmaydi
watch(() => props.service, (newVal) => {
  if (newVal) {
    form.value = { ...newVal, is_active: newVal.isActive }
  } else {
    form.value = { name: '', price: 0, description: '', is_active: true }
  }
})

function save() {
  emit('save', form.value)
}

function close() {
  emit('close')
}
</script>

<template>
  <!-- v-dialog - bu Vuetify'ning modal oyna komponenti -->
  <!-- :model-value="show" - bu 'show' prop'i orqali modalni ochib-yopadi -->
  <!-- persistent - chetini bosganda yopilmaydigan qiladi -->
  <v-dialog :model-value="show" persistent max-width="500px">

    <!-- v-card - modal oynaning "tanasi" -->
    <v-card>
      <!-- v-card-title - sarlavha -->
      <v-card-title>
        <span class="text-h5">{{ service ? 'Xizmatni Tahrirlash' : 'Yangi Xizmat Qo\'shish' }}</span>
      </v-card-title>

      <!-- v-card-text - asosiy kontent (forma) -->
      <v-card-text>
        <v-container>
          <!-- v-form - Vuetify formasi -->
          <v-form @submit.prevent="save">
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="form.name"
                  label="Nomi"
                  variant="outlined"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="form.price"
                  label="Narxi"
                  type="number"
                  variant="outlined"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-textarea
                  v-model="form.description"
                  label="Tavsifi"
                  variant="outlined"
                  rows="3"
                ></v-textarea>
              </v-col>
              <v-col cols="12">
                <!-- v-switch - chiroyli "aktiv/noaktiv" o'chirgich -->
                <v-switch
                  v-model="form.is_active"
                  color="primary"
                  label="Aktiv"
                ></v-switch>
              </v-col>
            </v-row>
          </v-form>
        </v-container>
      </v-card-text>

      <!-- v-card-actions - tugmalar uchun joy -->
      <v-card-actions>
        <v-spacer></v-spacer> <!-- Tugmalarni o'ngga suradi -->
        <v-btn color="blue-darken-1" variant="text" @click="close">
          Bekor Qilish
        </v-btn>
        <v-btn color="blue-darken-1" variant="text" @click="save">
          Saqlash
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>
/* Endi bizga maxsus stillar umuman kerak emas! */
</style>
