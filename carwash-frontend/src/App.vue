<script setup>
import { ref } from 'vue'
import { RouterLink, RouterView } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useSnackbarStore } from '@/stores/snackbar' // Yangi qo'shilgan

const authStore = useAuthStore()
const snackbarStore = useSnackbarStore() // Yangi
const drawer = ref(true) // Yon menyuning ochiq/yopiqligini boshqaradi

// Logout funksiyasini AuthStore'dan olamiz
async function handleLogout() {
  await authStore.logout()
  snackbarStore.show('Tizimdan chiqdingiz.', 'info')
}

// Yon menyudagi linklar ro'yxati
const menuItems = [
  { title: 'Dashboard', icon: 'dashboard', to: '/' },
  { title: 'Xizmatlar', icon: 'build', to: '/services' },
  { title: 'Mashinalar', icon: 'directions_car', to: '/vehicles' },
  { title: 'Buyurtmalar', icon: 'receipt', to: '/orders' },
  { title: 'Xodimlar', icon: 'people', to: '/users' },
  { title: 'To\'lovlar', icon: 'money', to: '/payments'}
]
</script>

<template>
  <!-- v-app - bu butun Vuetify ilovasining asosiy o'rami -->
  <v-app>
    <!-- Agar foydalanuvchi tizimga kirgan bo'lsa, asosiy layout'ni ko'rsatamiz -->
    <template v-if="authStore.isAuthenticated">
      <!-- v-navigation-drawer - bu yon menyu (Sidebar) -->
      <v-navigation-drawer v-model="drawer">
        <v-list-item
          prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"
          :title="authStore.user?.name || 'Foydalanuvchi'"
          nav
        ></v-list-item>

        <v-divider></v-divider>

        <!-- v-list - menyu punktlari ro'yxati -->
        <v-list density="compact" nav>
          <v-list-item
            v-for="item in menuItems"
            :key="item.title"
            :prepend-icon="item.icon"
            :title="item.title"
            :to="item.to"
            link
          ></v-list-item>
        </v-list>
        <v-divider></v-divider>
      </v-navigation-drawer>

      <!-- v-app-bar - bu yuqori menyu (Header) -->
      <v-app-bar>
        <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
        <v-toolbar-title>Car Wash Admin</v-toolbar-title>
        <v-list density="compact" nav>
            <v-list-item
                prepend-icon="logout"
                title="Tizimdan Chiqish"
                @click="handleLogout"
                link
            ></v-list-item>
        </v-list>
      </v-app-bar>

      <!-- v-main - bu asosiy kontent joylashadigan qism -->
      <v-main style="background-color: #f4f6f9;">
        <v-container fluid class="pa-4">
          <RouterView />
        </v-container>
      </v-main>
      <v-snackbar
        v-model="snackbarStore.isVisible"
        :color="snackbarStore.color"
        :timeout="3000"
        location="top right"
        multi-line
      >
        {{ snackbarStore.message }}
        <template v-slot:actions>
          <v-btn variant="text" @click="snackbarStore.hide">
            Yopish
          </v-btn>
        </template>
      </v-snackbar>
    </template>
    <template v-else>
      <RouterView />
    </template>
  </v-app>
</template>
<style>
</style>