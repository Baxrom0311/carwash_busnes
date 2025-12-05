<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useSnackbarStore } from '@/stores/snackbar'

const payments = ref([])
const isLoading = ref(true)
const errorMessage = ref('')
const snackbarStore = useSnackbarStore()

// Pagination holati
const pagination = ref({
    page: 1,
    pageCount: 1,
    itemsPerPage: 10,
    totalItems: 0, // Umumiy elementlar sonini saqlash uchun
});

async function fetchPayments(page = 1) {
  isLoading.value = true
  try {
    // Backend sahifalashni qo'llayotganligi sababli, URLga parametrlarni qo'shamiz
    const response = await axios.get(`/payments?page=${page}&per_page=${pagination.value.itemsPerPage}`)
    payments.value = response.data.data // Ma'lumotlar

    // Pagination ma'lumotlarini yangilash
    // Backend javobiga qarab moslashish kerak (masalan Laravel yoki boshqa ramkalarda farq qilishi mumkin)
    pagination.value.page = response.data.current_page || response.data.meta?.current_page || page;
    pagination.value.pageCount = response.data.last_page || response.data.meta?.last_page || 1;
    pagination.value.itemsPerPage = response.data.per_page || response.data.meta?.per_page || pagination.value.itemsPerPage;
    pagination.value.totalItems = response.data.total || response.data.meta?.total || payments.value.length;


    errorMessage.value = ''
  } catch (error) {
    errorMessage.value = 'To\'lovlar ro\'yxati yuklanmadi.'
    snackbarStore.show(errorMessage.value, 'error');
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
    fetchPayments();
})

// Jadval sarlavhalari (Backenddagi camelCase/snake_case ga moslashish)
const headers = [
  { title: 'ID', key: 'id' },
  { title: 'Buyurtma Bilet', key: 'order.ticket_no' }, // Agar backendda order.ticket_no bo'lsa, shu yerda ham shunday qoldirish kerak. Agar camelCase bo'lsa 'order.ticketNo'
  { title: 'Summa', key: 'amount' },
  { title: 'Usul', key: 'method' },
  { title: 'To\'langan Vaqti', key: 'paid_at' },
]
</script>

<template>
  <div>
    <div class="d-flex justify-space-between align-center mb-4">
      <h1>To'lovlar Tarixi</h1>
    </div>

     <v-alert v-if="errorMessage && !isLoading" type="error" closable class="mb-4">
      {{ errorMessage }}
    </v-alert>

    <v-data-table
      :headers="headers"
      :items="payments"
      :loading="isLoading"
      class="elevation-1"
      item-value="id"
       :items-length="pagination.totalItems"
       v-model:page="pagination.page"
       @update:options="fetchPayments($event.page)"
       :items-per-page="pagination.itemsPerPage"
    >
       <template v-slot:loading>
         <v-skeleton-loader type="table-tbody"></v-skeleton-loader>
      </template>
       <template v-slot:item.amount="{ item }">
         {{ item.amount.toLocaleString() }} so'm
      </template>
       <template v-slot:item.paid_at="{ item }">
         {{ new Date(item.paid_at).toLocaleString() }}
      </template>
       <template v-slot:item.method="{ item }">
         <v-chip small color="info">{{ item.method }}</v-chip>
</template>

       <!-- Agar siz v-data-table ning ichki sahifalashini o'chirib, faqat pastdagi v-pagination ni ishlatmoqchi bo'lsangiz -->
       <template v-slot:bottom>
            <v-divider></v-divider>
            <div class="text-xs-center pt-2">
                <v-pagination
                    v-model="pagination.page"
                    :length="pagination.pageCount"
                    @update:modelValue="fetchPayments"
                ></v-pagination>
            </div>
       </template>
     </v-data-table>
   </div>
</template>

<style scoped>
/* Maxsus stil kerak emas */
</style>
