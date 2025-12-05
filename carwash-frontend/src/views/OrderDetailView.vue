<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { useSnackbarStore } from '@/stores/snackbar'

const route = useRoute()
const snackbarStore = useSnackbarStore()

const order = ref(null)
const isLoading = ref(true)
const errorMessage = ref('')

const orderId = route.params.id

async function fetchOrder() {
  isLoading.value = true
  try {
    const response = await axios.get(`/orders/${orderId}`)
    order.value = response.data.data
  } catch (error) {
    errorMessage.value = 'Buyurtma tafsilotlarini yuklashda xatolik: ' + (error.response?.data?.message || 'Server xatosi');
    snackbarStore.show(errorMessage.value, 'error');
  } finally {
    isLoading.value = false
  }
}

onMounted(fetchOrder)

// Status uchun ranglar
const statusColors = {
  new: 'blue',
  in_progress: 'orange',
  done: 'teal',
  paid: 'green',
  canceled: 'red'
}
</script>

<template>
  <v-container>
    <div class="d-flex justify-space-between align-center mb-4">
      <h1>Buyurtma Tafsilotlari #{{ orderId }}</h1>
      <v-btn color="primary" :to="{ name: 'orders' }" prepend-icon="arrow_back">
        Ro'yxatga Qaytish
      </v-btn>
    </div>

    <!-- Yuklanish holati -->
    <v-card v-if="isLoading" class="pa-5 text-center" elevation="2">
      <v-progress-circular indeterminate color="primary"></v-progress-circular>
      <p class="mt-2">Yuklanmoqda...</p>
    </v-card>

    <!-- Xatolik holati -->
    <v-alert v-else-if="errorMessage" type="error" class="mb-4">
      {{ errorMessage }}
    </v-alert>

    <!-- Buyurtma kontenti -->
    <v-card v-else-if="order">
      <v-card-text>
        <v-row>
          <!-- Umumiy ma'lumotlar -->
          <v-col cols="12" md="4">
            <v-card variant="outlined">
              <v-card-title class="text-h6 pb-2">Asosiy Ma'lumotlar</v-card-title>
              <v-divider></v-divider>
              <v-list density="compact">
                <v-list-item title="Bilet №" :subtitle="order.ticketNo"></v-list-item>
                <v-list-item title="Status">
                    <v-chip :color="statusColors[order.status]" size="small">
                      {{ order.status.replace('_', ' ') }}
                    </v-chip>
                </v-list-item>
                <v-list-item title="Qabul qildi" :subtitle="order.manager?.name || 'N/A'"></v-list-item>
                <v-list-item title="Vaqti" :subtitle="new Date(order.checkinAt).toLocaleString()"></v-list-item>
              </v-list>
            </v-card>
          </v-col>

          <!-- Mashina ma'lumotlari -->
          <v-col cols="12" md="4">
            <v-card variant="outlined">
              <v-card-title class="text-h6 pb-2">Mashina</v-card-title>
              <v-divider></v-divider>
              <v-list density="compact">
                <v-list-item title="Raqami" :subtitle="order.vehicle?.plateNumber || 'Mavjud emas'"></v-list-item>
                <v-list-item title="Marka/Model" :subtitle="order.vehicle?.brand + ' ' + (order.vehicle?.model || '')"></v-list-item>
                <v-list-item title="Egasining tel" :subtitle="order.vehicle?.ownerPhone || 'N/A'"></v-list-item>
              </v-list>
            </v-card>
          </v-col>

          <!-- Narxlar -->
          <v-col cols="12" md="4">
            <v-card color="primary" theme="dark">
              <v-card-title class="text-h4">
                {{ order.total.toLocaleString() }} so'm
              </v-card-title>
              <v-card-subtitle>Umumiy Summa</v-card-subtitle>
              <v-divider></v-divider>
              <v-list density="compact" bg-color="transparent">
                <v-list-item title="Subtotal" :subtitle="order.subtotal.toLocaleString() + ' so\'m'"></v-list-item>
                <v-list-item title="Chegirma" :subtitle="order.discount.toLocaleString() + ' so\'m'"></v-list-item>
              </v-list>
            </v-card>
          </v-col>

          <!-- Xizmatlar ro'yxati -->
          <v-col cols="12">
            <v-card class="mt-4">
                <v-card-title class="text-h6">Buyurtma Xizmatlari ({{ order.items.length }})</v-card-title>
                <v-table density="compact">
                    <thead>
                        <tr>
                            <th class="text-left">Xizmat</th>
                            <th class="text-left">Ishchi</th>
                            <th class="text-left">Narx</th>
                            <th class="text-left">Soni</th>
                            <th class="text-right">Summa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in order.items" :key="item.id">
                            <td>{{ item.service.name }}</td>
                            <td>{{ item.worker?.name || 'Biriktirilmagan' }}</td>
                            <td>{{ item.unit_price.toLocaleString() }} so'm</td>
                            <td>{{ item.qty }}</td>
                            <td class="text-right font-weight-bold">{{ item.line_total.toLocaleString() }} so'm</td>
                        </tr>
                    </tbody>
                </v-table>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<style scoped>
/* No specific styles needed */
</style>