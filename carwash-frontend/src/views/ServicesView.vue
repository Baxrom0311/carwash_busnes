<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import ServiceFormModal from '@/components/ServiceFormModal.vue' // <<< 1. MODALNI IMPORT QILAMIZ
import { useSnackbarStore } from '@/stores/snackbar' // <<< IMPORT

const services = ref([])
const isLoading = ref(true)
const errorMessage = ref('')

// --- MODAL UCHUN BARCHA LOGIKA QAYTARILDI ---
const isModalVisible = ref(false)
const editableService = ref(null)
const snackbarStore = useSnackbarStore() // <<< STORE INSTANSIYASI

async function fetchServices() {
  isLoading.value = true
  try {
    const response = await axios.get('/services')
    services.value = response.data.data
  } catch (error) {
    errorMessage.value = 'Xizmatlar yuklanmadi.'
  } finally {
    isLoading.value = false
  }
}

onMounted(fetchServices)

function openCreateModal() {
  editableService.value = null
  isModalVisible.value = true
}

function openEditModal(service) {
  editableService.value = { ...service }
  isModalVisible.value = true
}

function closeModal() {
  isModalVisible.value = false
}

async function handleSave(serviceData) {
  try {
    if (editableService.value && editableService.value.id) {
      await axios.put(`/services/${editableService.value.id}`, serviceData)
      snackbarStore.show('Xizmat ma\'lumotlari yangilandi.', 'success'); // <<< SNACKBAR
    } else {
      await axios.post('/services', serviceData)
      snackbarStore.show('Yangi xizmat muvaffaqiyatli qo\'shildi.', 'success'); // <<< SNACKBAR
    }
    await fetchServices()
    closeModal()
  } catch (error) {
    snackbarStore.show("Saqlashda xatolik yuz berdi!", 'error');
  }
}

async function deleteService(serviceId) {
  if (!confirm("Haqiqatan ham bu xizmatni o'chirmoqchimisiz?")) return
  try {
    await axios.delete(`/services/${serviceId}`)
    snackbarStore.show("Xizmat ro'yxatdan o'chirildi.", 'success'); // <<< SNACKBAR
    await fetchServices()
  } catch (error) {
    snackbarStore.show("O'chirishda xatolik yuz berdi!", 'error');
  }
}
// ---------------------------------------------

// Jadval sarlavhalari
const headers = [
  { title: 'ID', key: 'id' },
  { title: 'Nomi', key: 'name' },
  { title: 'Narxi', key: 'price' },
  { title: 'Status', key: 'isActive' },
  { title: 'Amallar', key: 'actions', sortable: false },
]

// Status uchun rang berish funksiyasi
const getStatusSeverity = (isActive) => {
  return isActive ? 'green' : 'red';
}
</script>

<template>
  <div>
    <div class="d-flex justify-space-between align-center mb-4">
      <h1>Xizmatlar</h1>
      <!-- O'ZGARISH: @click hodisasi qo'shildi -->
      <v-btn color="primary" prepend-icon="add" @click="openCreateModal">
        Yangi Xizmat
      </v-btn>
    </div>

    <v-alert v-if="errorMessage" type="error" closable class="mb-4">
      {{ errorMessage }}
    </v-alert>

    <v-data-table
      :headers="headers"
      :items="services"
      :loading="isLoading"
      class="elevation-1"
      item-value="id"
      :items-per-page="10"
    >
      <template v-slot:item.price="{ item }">
        {{ item.price.toLocaleString() }} so'm
      </template>

      <template v-slot:item.isActive="{ item }">
        <v-chip :color="getStatusSeverity(item.isActive)">
          {{ item.isActive ? 'Aktiv' : 'Aktiv Emas' }}
        </v-chip>
      </template>

      <!-- O'ZGARISH: @click hodisalari qo'shildi -->
      <template v-slot:item.actions="{ item }">
        <v-icon size="small" class="me-2" color="amber" @click="openEditModal(item)">edit</v-icon>
        <v-icon size="small" color="red" @click="deleteService(item.id)">delete</v-icon>
      </template>
    </v-data-table>

    <!-- O'ZGARISH: Modal komponenti qaytarildi -->
    <ServiceFormModal
      :show="isModalVisible"
      :service="editableService"
      @close="closeModal"
      @save="handleSave"
    />
  </div>
</template>

<style scoped>
/* Maxsus stil kerak emas */
</style>

