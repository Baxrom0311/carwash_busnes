<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  show: Boolean,
  user: Object
})
const emit = defineEmits(['close', 'save'])

const form = ref({
  name: '',
  phone: '',
  email: '',
  password: '',
  role: 'worker'
})

// Rol tanlash uchun variantlar ro'yxati
const roleItems = [
  { title: 'Menejer', value: 'manager' },
  { title: 'Kassir', value: 'cashier' },
  { title: 'Ishchi', value: 'worker' },
]

watch(() => props.user, (newVal) => {
  if (newVal) {
    form.value = {
      name: newVal.name,
      phone: newVal.phone,
      email: newVal.email,
      password: '',
      role: newVal.role
    }
  } else {
    form.value = { name: '', phone: '', email: '', password: '', role: 'worker' }
  }
})

function save() {
  const dataToSend = { ...form.value }
  if (!dataToSend.password) {
    delete dataToSend.password
  }
  emit('save', dataToSend)
}

function close() {
  emit('close')
}
</script>

<template>
  <v-dialog :model-value="show" persistent max-width="500px">
    <v-card>
      <v-card-title>
        <span class="text-h5">{{ user ? 'Xodimni Tahrirlash' : 'Yangi Xodim Qo\'shish' }}</span>
      </v-card-title>

      <v-card-text>
        <v-container>
          <v-form @submit.prevent="save">
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="form.name"
                  label="Ismi"
                  variant="outlined"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="form.phone"
                  label="Telefon raqami"
                  variant="outlined"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="form.email"
                  label="Email (ixtiyoriy)"
                  type="email"
                  variant="outlined"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="form.password"
                  label="Parol (yangi yoki o'zgartirish uchun)"
                  type="password"
                  variant="outlined"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <!-- v-select - bu ochiladigan ro'yxat komponenti -->
                <v-select
                  v-model="form.role"
                  :items="roleItems"
                  label="Roli"
                  variant="outlined"
                  required
                ></v-select>
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
