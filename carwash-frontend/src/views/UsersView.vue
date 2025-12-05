<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import UserFormModal from '@/components/UserFormModal.vue' // <<< 1. MODALNI IMPORT QILAMIZ
import { useSnackbarStore } from '@/stores/snackbar' // <<< IMPORT

const users = ref([])
const isLoading = ref(true)
const errorMessage = ref('')

// --- MODAL UCHUN BARCHA LOGIKA QO'SHILDI ---
const isModalVisible = ref(false)
const editableUser = ref(null)
const snackbarStore = useSnackbarStore() // <<< STORE INSTANSIYASI

async function fetchUsers() {
  isLoading.value = true
  try {
    const response = await axios.get('/users')
    users.value = response.data.data
  } catch (error) {
    errorMessage.value = 'Xodimlar ro\'yxati yuklanmadi.'
  } finally {
    isLoading.value = false
  }
}

onMounted(fetchUsers)

function openCreateModal() {
  editableUser.value = null
  isModalVisible.value = true
}

function openEditModal(user) {
  editableUser.value = { ...user }
  isModalVisible.value = true
}

function closeModal() {
  isModalVisible.value = false
}

async function handleSave(userData) {
  try {
    if (editableUser.value && editableUser.value.id) {
      await axios.put(`/users/${editableUser.value.id}`, userData)
      snackbarStore.show('Xodim ma\'lumotlari yangilandi.', 'success'); // <<< SNACKBAR
    } else {
      await axios.post('/users', userData)
      snackbarStore.show('Yangi xodim muvaffaqiyatli qo\'shildi.', 'success'); // <<< SNACKBAR
    }
    await fetchUsers()
    closeModal()
  } catch (error) {
    if (error.response && error.response.status === 422) {
      const errors = error.response.data.errors;
      let errorText = '';
      for (const key in errors) {
        errorText += errors[key].join(', ') + ' '; // Birlashtirish
      }
      snackbarStore.show(errorText.trim() || "Validatsiya xatosi!", 'error');
    } else {
      snackbarStore.show("Saqlashda xatolik yuz berdi!", 'error');
    }
    console.error("Saqlashda xatolik:", error)
  }
}

async function deleteUser(userId) {
  if (!confirm("Haqiqatan ham bu xodimni o'chirmoqchimisiz?")) return
  try {
    await axios.delete(`/users/${userId}`)
    snackbarStore.show("Xodim muvaffaqiyatli o'chirildi.", 'success'); // <<< SNACKBAR
    await fetchUsers()
  } catch (error) {
    snackbarStore.show("O'chirishda xatolik yuz berdi!", 'error');
    console.error("O'chirishda xatolik:", error)
  }
}
// ---------------------------------------------

// Jadval sarlavhalari
const headers = [
  { title: 'ID', key: 'id' },
  { title: 'Ismi', key: 'name' },
  { title: 'Telefon', key: 'phone' },
  { title: 'Roli', key: 'role' },
  { title: 'Amallar', key: 'actions', sortable: false },
]

// Rol uchun ranglarni belgilaymiz
const roleColors = {
  owner: 'purple',
  manager: 'blue',
  cashier: 'green',
  worker: 'grey',
}
</script>

<template>
  <div>
    <div class="d-flex justify-space-between align-center mb-4">
      <h1>Xodimlar</h1>
      <!-- O'ZGARISH: @click hodisasi qo'shildi -->
      <v-btn color="primary" prepend-icon="person_add" @click="openCreateModal">
        Yangi Xodim
      </v-btn>
    </div>

    <v-alert v-if="errorMessage" type="error" closable class="mb-4">
      {{ errorMessage }}
    </v-alert>

    <v-data-table
      :headers="headers"
      :items="users"
      :loading="isLoading"
      class="elevation-1"
      item-value="id"
      :items-per-page="10"
    >
      <template v-slot:item.role="{ item }">
        <v-chip :color="roleColors[item.role] || 'default'" size="small">
          {{ item.role }}
        </v-chip>
      </template>

      <!-- O'ZGARISH: @click hodisalari qo'shildi -->
      <template v-slot:item.actions="{ item }">
        <v-icon size="small" class="me-2" color="amber" @click="openEditModal(item)">edit</v-icon>
        <v-icon size="small" color="red" @click="deleteUser(item.id)">delete</v-icon>
      </template>
    </v-data-table>

    <!-- O'ZGARISH: Modal komponenti qo'shildi -->
    <UserFormModal
      :show="isModalVisible"
      :user="editableUser"
      @close="closeModal"
      @save="handleSave"
    />
  </div>
</template>

<style scoped>
.role {
  text-transform: capitalize;
}
</style>

