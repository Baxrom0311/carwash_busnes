<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import OrderFormModal from '@/components/OrderFormModal.vue'
// import { useSnackbarStore } from '@/stores/snackbar' // Hozircha buni ishlatmaymiz

const orders = ref([])
const isLoading = ref(true)
const errorMessage = ref('')

const isModalVisible = ref(false)
const editableOrder = ref(null)
// const snackbarStore = useSnackbarStore()

// --- XATOLIKLAR TUZATILDI ---

// 1. Jadval sarlavhalari e'lon qilindi
const headers = [
  { title: 'ID / Bilet №', key: 'ticket' },
  { title: 'Mashina', key: 'vehicle' },
  { title: 'Status', key: 'status' },
  { title: 'Umumiy Summa', key: 'total' },
  { title: 'Menejer', key: 'manager' },
  { title: 'Vaqti', key: 'createdAt' },
  { title: 'Amallar', key: 'actions', sortable: false },
]

// 2. Status ranglari e'lon qilindi
const statusColors = {
  new: 'blue',
  in_progress: 'orange',
  done: 'teal',
  paid: 'green',
  canceled: 'red',
}

// 3. Sana formatlash funksiyasi e'lon qilindi
function formatDate(dateString) {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleString('uz-UZ'); // O'zbekiston formati uchun
}

// -----------------------------

async function fetchOrders() {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await axios.get('/orders')
    orders.value = response.data.data
  } catch (error) {
    errorMessage.value = "Buyurtmalarni yuklashda xatolik yuz berdi."
  } finally {
    isLoading.value = false
  }
}

onMounted(fetchOrders)

// 4. Modalni ochish funksiyasi e'lon qilindi
function openCreateModal() {
  editableOrder.value = null
  isModalVisible.value = true
}

function openEditModal(order) {
  editableOrder.value = { ...order }
  isModalVisible.value = true
}

function closeModal() {
  isModalVisible.value = false
}

async function handleSave(orderData) {
  try {
    if (editableOrder.value && editableOrder.value.id) {
      await axios.put(`/orders/${editableOrder.value.id}`, orderData)
      // snackbarStore.show('Buyurtma yangilandi.', 'success');
    } else {
      await axios.post('/orders', orderData)
      // snackbarStore.show('Yangi buyurtma yaratildi.', 'success');
    }
    await fetchOrders()
    closeModal()
  } catch (error) {
    // snackbarStore.show("Saqlashda xatolik yuz berdi!", 'error');
    alert("Saqlashda xatolik yuz berdi!");
  }
}

async function deleteOrder(orderId) {
  if (!confirm("Haqiqatan ham bu buyurtmani o'chirmoqchimisiz?")) return
  try {
    await axios.delete(`/orders/${orderId}`)
    // snackbarStore.show("Buyurtma o'chirildi.", 'success');
    await fetchOrders()
  } catch (error) {
    // snackbarStore.show("O'chirishda xatolik yuz berdi!", 'error');
    alert("O'chirishda xatolik yuz berdi!");
  }
}
</script>

<template>
  <div>
    <div class="d-flex justify-space-between align-center mb-4">
      <h1>Buyurtmalar</h1>
      <v-btn color="primary" prepend-icon="add_shopping_cart" @click="openCreateModal">
        Yangi Buyurtma
      </v-btn>
    </div>

    <v-alert v-if="errorMessage" type="error" closable class="mb-4">
      {{ errorMessage }}
    </v-alert>

    <v-data-table
      :headers="headers"
      :items="orders"
      :loading="isLoading"
      class="elevation-1"
      item-value="id"
      :items-per-page="10"
    >
      <!-- 5. SLOT NOMLARI TO'G'RILANDI -->
      <template v-slot:item.ticket="{ item }">
        #{{ item.id }} / {{ item.ticketNo }}
      </template>

      <template v-slot:item.vehicle="{ item }">
        {{ item.vehicle ? item.vehicle.plateNumber : 'Biriktirilmagan' }}
      </template>

      <template v-slot:item.status="{ item }">
        <v-chip :color="statusColors[item.status] || 'default'" size="small" class="text-capitalize">
          {{ item.status.replace('_', ' ') }}
        </v-chip>
      </template>

      <template v-slot:item.total="{ item }">
        {{ item.total.toLocaleString() }} so'm
      </template>

      <template v-slot:item.manager="{ item }">
        {{ item.manager ? item.manager.name : 'N/A' }}
      </template>

      <template v-slot:item.createdAt="{ item }">
        {{ formatDate(item.checkinAt || item.createdAt) }}
      </template>
      
      <template v-slot:item.actions="{ item }">
        <!-- Hozircha 'to' linkini olib turamiz, chunki OrderDetailView hali yo'q -->
        <v-icon size="small" class="me-2" color="blue">visibility</v-icon>
        <v-icon size="small" class="me-2" color="amber" @click="openEditModal(item)">edit</v-icon>
        <v-icon size="small" color="red" @click="deleteOrder(item.id)">delete</v-icon>
      </template>
    </v-data-table>

    <OrderFormModal
      :show="isModalVisible"
      :order="editableOrder"
      @close="closeModal"
      @save="handleSave"
    />
  </div>
</template>

<style scoped>
.text-capitalize {
  text-transform: capitalize;
}
</style>