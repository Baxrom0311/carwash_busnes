

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import VehicleFormModal from '@/components/VehicleFormModal.vue'
import { useSnackbarStore } from '@/stores/snackbar' // <<< IMPORT

const vehicles = ref([])
const isLoading = ref(true)
const errorMessage = ref('')
const snackbarStore = useSnackbarStore() // <<< STORE INSTANSIYASI

async function fetchVehicles() {
  isLoading.value = true
  try {
    const response = await axios.get('/vehicles')
    vehicles.value = response.data.data
    errorMessage.value = ''
  } catch (error) {
    errorMessage.value = 'Mashinalar ro\'yxati yuklanmadi.'
  } finally {
    isLoading.value = false
  }
}

onMounted(fetchVehicles)

function openCreateModal() {
  editableVehicle.value = null
  isModalVisible.value = true
}

function openEditModal(vehicle) {
  // Sathi nusxa olish
  editableVehicle.value = { ...vehicle }
  isModalVisible.value = true
}

function closeModal() {
  isModalVisible.value = false
}

async function handleSave(vehicleData) {
  try {
    if (editableVehicle.value && editableVehicle.value.id) {
      // Tahrirlash (Update)
      await axios.put(`/vehicles/${editableVehicle.value.id}`, vehicleData)
      snackbarStore.show('Mashina ma\'lumotlari yangilandi.', 'success') // <<< SNACKBAR
    } else {
      // Yaratish (Create)
      await axios.post('/vehicles', vehicleData)
      snackbarStore.show('Yangi mashina muvaffaqiyatli qo\'shildi.', 'success') // <<< SNACKBAR
    }
    await fetchVehicles()
    closeModal()
  } catch (error) {
    // Bu yerda alert o'rniga Snackbar ishlatish tavsiya etiladi (3-qadam)
    snackbarStore.show("Saqlashda xatolik yuz berdi!", 'error'); // <<< SNACKBAR
    console.error("Saqlashda xatolik:", error)
  }
}

async function deleteVehicle(vehicleId) {
  if (!confirm("Haqiqatan ham bu mashinani o'chirmoqchimisiz?")) return
  try {
    await axios.delete(`/vehicles/${vehicleId}`)
    snackbarStore.show("Mashina ro'yxatdan o'chirildi.", 'success'); // <<< SNACKBAR
    await fetchVehicles()
  } catch (error) {
    // Bu yerda alert o'rniga Snackbar ishlatish tavsiya etiladi (3-qadam)
    snackbarStore.show("O'chirishda xatolik yuz berdi!", 'error'); // <<< SNACKBAR
    console.error("O'chirishda xatolik:", error)
  }
}


// ---------------------------------------------
// Jadval sarlavhalari
const headers = [
  { title: 'ID', key: 'id' },
  { title: 'Davlat Raqami', key: 'plateNumber' },
  { title: 'Marka / Model', key: 'brand' }, // Buni keyinroq birlashtiramiz
  { title: 'Rangi', key: 'color' },
  { title: 'Egasining Ismi', key: 'owner.name' }, // Ichki obyektga murojaat
  { title: 'Amallar', key: 'actions', sortable: false },
]
</script>

<template>
  <div>
    <div class="d-flex justify-space-between align-center mb-4">
      <h1>Mashinalar</h1>
      <v-btn color="primary" prepend-icon="add" @click="openCreateModal">
        Yangi Mashina
      </v-btn>
    </div>

    <v-alert v-if="errorMessage" type="error" closable class="mb-4">
      {{ errorMessage }}
    </v-alert>

    <!-- Vuetify Data Table -->
    <v-data-table
      :headers="headers"
      :items="vehicles"
      :loading="isLoading"
      class="elevation-1"
      item-value="id"
      :items-per-page="10"
    >
      <!-- Brend va Modelni birlashtirish uchun maxsus slot -->
      <template v-slot:item.brand="{ item }">
        {{ item.brand }} {{ item.model }}
      </template>

      <!-- Amallar ustuni -->
      <template v-slot:item.actions="{ item }">
        <v-icon size="small" class="me-2" color="amber" @click="openEditModal(item)">edit</v-icon>
        <v-icon size="small" color="red" @click="deleteVehicle(item.id)">delete</v-icon>
      </template>
    </v-data-table>

    <!-- VehicleFormModal integratsiyasi -->
    <VehicleFormModal
      :show="isModalVisible"
      :vehicle="editableVehicle"
      @close="closeModal"
      @save="handleSave"
    />
  </div>
</template>

<style scoped>
/* Maxsus stil kerak emas */
</style>

