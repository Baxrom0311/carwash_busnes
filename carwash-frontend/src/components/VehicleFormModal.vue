<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  show: Boolean,
  vehicle: Object
})
const emit = defineEmits(['close', 'save'])

const form = ref({
  plate_number: '',
  brand: '',
  model: '',
  color: '',
  owner_name: '',
  owner_phone: ''
})

watch(() => props.vehicle, (newVal) => {
  if (newVal) {
    // API Resursdan kelgan 'plateNumber'ni 'plate_number'ga o'giramiz
    form.value = {
      ...newVal,
      plate_number: newVal.plateNumber
    }
  } else {
    form.value = {
      plate_number: '', brand: '', model: '', color: '',
      owner_name: '', owner_phone: ''
    }
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
  <v-dialog :model-value="show" persistent max-width="500px">
    <v-card>
      <v-card-title>
        <span class="text-h5">{{ vehicle ? 'Mashinani Tahrirlash' : 'Yangi Mashina Qo\'shish' }}</span>
      </v-card-title>

      <v-card-text>
        <v-container>
          <v-form @submit.prevent="save">
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="form.plate_number"
                  label="Davlat raqami"
                  variant="outlined"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="form.brand"
                  label="Markasi (Brend)"
                  variant="outlined"
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="form.model"
                  label="Modeli"
                  variant="outlined"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="form.color"
                  label="Rangi"
                  variant="outlined"
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="form.owner_name"
                  label="Egasining ismi"
                  variant="outlined"
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="form.owner_phone"
                  label="Egasining telefoni"
                  variant="outlined"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-container>
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
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
/* Maxsus stillar kerak emas */
</style>
