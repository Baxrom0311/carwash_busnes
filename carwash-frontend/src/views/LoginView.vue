<script setup>
// ... script qismi deyarli o'zgarmaydi ...
import { ref } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const phone = ref('')
const code = ref('')
const step = ref(1)
const errorMessage = ref('')
const isLoading = ref(false)

async function sendOtp() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    await axios.post('/otp/send', { phone: phone.value })
    step.value = 2
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Xatolik yuz berdi.'
  } finally {
    isLoading.value = false
  }
}

async function verifyOtp() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await axios.post('/otp/verify', {
      phone: phone.value,
      code: code.value
    })
    authStore.setAuthData(response.data.user, response.data.access_token)
    await router.push('/')
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Noto\'g\'ri kod yoki muddati o\'tgan.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <!-- v-layout - bu sahifani markazlashtirish uchun qulay komponent -->
  <v-layout class="d-flex align-center justify-center" style="height: 100vh;">
    <v-card width="400" class="pa-4" elevation="8">
      <v-card-title class="text-center text-h5">
        Tizimga Kirish
      </v-card-title>
      <v-card-text>

        <!-- v-alert - xatolik xabarini ko'rsatish uchun -->
        <v-alert v-if="errorMessage" type="error" density="compact" class="mb-4">
          {{ errorMessage }}
        </v-alert>

        <!-- 1-QADAM: Telefon raqami formasi -->
        <v-form v-if="step === 1" @submit.prevent="sendOtp">
          <!-- v-text-field - bu matn kiritish maydoni -->
          <v-text-field
            v-model="phone"
            label="Telefon raqami"
            placeholder="998901234567"
            variant="outlined"
            prepend-inner-icon="phone"
            required
          ></v-text-field>

          <!-- v-btn - bu tugma -->
          <v-btn
            type="submit"
            color="primary"
            block
            :loading="isLoading"
          >
            Kod Yuborish
          </v-btn>
        </v-form>

        <!-- 2-QADAM: OTP kod formasi -->
        <v-form v-if="step === 2" @submit.prevent="verifyOtp">
          <p class="text-center mb-2">{{ phone }} raqamiga yuborilgan kodni kiriting.</p>
          <v-text-field
            v-model="code"
            label="Tasdiqlash kodi"
            variant="outlined"
            prepend-inner-icon="password"
            required
          ></v-text-field>

          <v-btn
            type="submit"
            color="primary"
            block
            :loading="isLoading"
          >
            Tasdiqlash
          </v-btn>
        </v-form>

      </v-card-text>
    </v-card>
  </v-layout>
</template>

<style scoped>
/* Maxsus stil kerak emas */
</style>
