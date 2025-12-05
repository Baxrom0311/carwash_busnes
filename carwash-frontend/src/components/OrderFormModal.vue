<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  order: Object
})
const emit = defineEmits(['close', 'save'])

// --- Ma'lumotlarni saqlash uchun ---
const form = ref({
  vehicle_id: null,
  manager_id: null,
  note: '',
  items: [] // Xizmatlar ro'yxati
})

// --- Boshlang'ich holatni yaratish uchun yordamchi funksiya ---
const getInitialForm = () => ({
  vehicle_id: null,
  manager_id: null,
  note: '',
  items: []
});


// --- Ochiladigan ro'yxatlar uchun ma'lumotlarni backend'dan yuklab olamiz ---
const vehicles = ref([])
const users = ref([])
const services = ref([])

// Komponent ko'ringanda, kerakli ma'lumotlarni yuklash
onMounted(async () => {
  // Bu so'rovlarni parallel yuborish uchun
  try {
    const [vehiclesRes, usersRes, servicesRes] = await Promise.all([
        axios.get('/vehicles'),
        axios.get('/users'),
        axios.get('/services?filter[is_active]=true')
    ]);
    vehicles.value = vehiclesRes.data.data;
    users.value = usersRes.data.data;
    services.value = servicesRes.data.data;
  } catch(e) {
    console.error("OrderFormModal: Required data fetch failed", e);
  }
})

// Tahrirlash rejimi uchun
watch(() => props.order, (newVal) => {
  if (newVal) {
    // Tahrirlash (Update) logikasi: ma'lumotlarni formaga yuklash
    form.value = {
      vehicle_id: newVal.vehicle_id || newVal.vehicle?.id,
      manager_id: newVal.manager_id || newVal.manager?.id,
      note: newVal.note,
      // Buyurtma elementlarini (items) chuqur nusxalash (deep clone)
      items: newVal.items.map(item => ({
        service_id: item.service_id,
        worker_id: item.worker_id,
        qty: item.qty
      }))
    };
  } else {
    // Yaratish (Create) logikasi: formani tozalash
    form.value = getInitialForm();
    addItem(); // Yaratish rejimida avtomatik ravishda birinchi elementni qo'shamiz
  }
}, { immediate: true });

// --- Xizmatlarni (items) qo'shish va o'chirish ---
function addItem() {
  form.value.items.push({
    service_id: null,
    worker_id: null,
    qty: 1
  })
}
function removeItem(index) {
  form.value.items.splice(index, 1)
}

function save() {
  // Backend'da tranzaksiya bor, lekin biz frontendda qandaydir eng kam validatsiya qo'shishimiz mumkin
  if (!form.value.items.some(item => item.service_id && item.qty > 0)) {
      alert("Buyurtmaga hech bo'lmasa bitta xizmat qo'shing!");
      return;
  }
  emit('save', form.value)
}
function close() {
  // Modal yopilganda formani qayta tiklashga tayyorlanamiz
  form.value = getInitialForm();
  emit('close')
}
</script>

<template>
  <v-dialog :model-value="show" persistent max-width="800px">
    <v-card>
      <v-card-title>
        <span class="text-h5">{{ order ? 'Buyurtmani Tahrirlash' : 'Yangi Buyurtma' }}</span>
      </v-card-title>

      <v-card-text>
        <v-container>
          <v-form @submit.prevent="save">
            <v-row>
              <!-- Asosiy ma'lumotlar -->
              <v-col cols="12" md="6">
                <v-autocomplete
                  v-model="form.vehicle_id"
                  :items="vehicles"
                  item-title="plateNumber"
                  item-value="id"
                  label="Mashina (davlat raqami)"
                  variant="outlined"
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.manager_id"
                  :items="users"
                  item-title="name"
                  item-value="id"
                  label="Menejer"
                  variant="outlined"
                ></v-select>
              </v-col>

              <v-col cols="12">
                <v-textarea v-model="form.note" label="Izoh" variant="outlined" rows="2"></v-textarea>
              </v-col>

              <!-- Xizmatlar (Items) ro'yxati -->
              <v-col cols="12">
                <div class="d-flex justify-space-between align-center mb-2">
                  <span class="text-h6">Xizmatlar</span>
                  <v-btn @click="addItem" color="primary" size="small" prepend-icon="add">
                    Xizmat qo'shish
                  </v-btn>
                </div>

                <!-- Har bir item uchun alohida qator -->
                <v-row v-for="(item, index) in form.items" :key="index" class="align-center item-row">
                  <v-col cols="12" md="5">
                    <v-select
                      v-model="item.service_id"
                      :items="services"
                      item-title="name"
                      item-value="id"
                      label="Xizmat turi"
                      variant="outlined"
                      density="compact"
                    ></v-select>
                  </v-col>
                  <v-col cols="12" md="5">
                    <v-select
                      v-model="item.worker_id"
                      :items="users.filter(u => u.role === 'worker')"
                      item-title="name"
                      item-value="id"
                      label="Ishchi"
                      variant="outlined"
                      density="compact"
                    ></v-select>
                  </v-col>
                  <v-col cols="12" md="1">
                    <v-text-field
                      v-model="item.qty"
                      type="number"
                      label="Soni"
                      variant="outlined"
                      density="compact"
                      min="1"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="1" class="text-center">
                    <v-btn @click="removeItem(index)" icon="delete" color="red" variant="text"></v-btn>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-form>
        </v-container>
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="grey" variant="text" @click="close">Bekor Qilish</v-btn>
        <v-btn color="primary" variant="flat" @click="save">Saqlash</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>
.item-row {
  border-bottom: 1px solid #eee;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
}
.item-row:last-child {
  border-bottom: none;
}
</style>

