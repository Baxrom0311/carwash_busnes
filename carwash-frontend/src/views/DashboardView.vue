<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const stats = ref(null)
const isLoading = ref(true)
const errorMessage = ref('')

onMounted(async () => {
  try {
    const response = await axios.get('/dashboard')
    stats.value = response.data
  } catch (error) {
    errorMessage.value = 'Statistika yuklanmadi.'
  } finally {
    isLoading.value = false
  }
})

// Logout funksiyasi o'zgarmaydi, lekin uni App.vue'ga olib chiqqan ma'qul. Hozircha shu yerda tursin.
// import { useAuthStore } from '@/stores/auth'
// import { useRouter } from 'vue-router'
// const authStore = useAuthStore()
// const router = useRouter()
// async function logout() {
//   try {
//     await axios.post('/logout')
//   } catch (error) {
//     console.error("Logout xatosi:", error)
//   } finally {
//     authStore.clearAuthData()
//     await router.push('/login')
//   }
// }
</script>

<template>
  <div>
    <div class="d-flex justify-space-between align-center mb-4">
      <h1>Boshqaruv Paneli</h1>
    </div>
    <!-- Vuetify Progress Circular (yuklanish animatsiyasi) -->
    <div v-if="isLoading" class="text-center">
      <v-progress-circular indeterminate color="primary"></v-progress-circular>
    </div>

    <!-- Vuetify Alert (xatolik xabari) -->
    <v-alert v-else-if="errorMessage" type="error" closable>
      {{ errorMessage }}
    </v-alert>

    <!-- Vuetify Grid tizimi (v-row, v-col) -->
    <v-row v-else-if="stats">
      <v-col cols="12" sm="6" md="3">
        <!-- Vuetify Card (kartochka) -->
        <v-card class="text-center" elevation="2">
          <v-card-text>
            <div class="text-caption">Bugungi Tushum</div>
            <div class="text-h5 font-weight-bold">{{ stats.todaysRevenue.toLocaleString() }} so'm</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" md="3">
        <v-card class="text-center" elevation="2">
          <v-card-text>
            <div class="text-caption">Bugungi Buyurtmalar</div>
            <div class="text-h5 font-weight-bold">{{ stats.todaysOrdersCount }} ta</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" md="3">
        <v-card class="text-center" elevation="2">
          <v-card-text>
            <div class="text-caption">Bajarilmoqda</div>
            <div class="text-h5 font-weight-bold">{{ stats.inProgressOrdersCount }} ta</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" md="3">
        <v-card class="text-center" elevation="2">
          <v-card-text>
            <div class="text-caption">Oyning Eng Yaxshi Ishchisi</div>
            <div class="text-h5 font-weight-bold">{{ stats.topWorker ? stats.topWorker.name : 'Noma\'lum' }}</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<style scoped>
/* Vuetify o'zining stillarini bergani uchun, bu yer deyarli bo'sh bo'ladi */
.text-caption {
  color: #6c757d;
}
</style>
