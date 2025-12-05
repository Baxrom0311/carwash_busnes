import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'; // axios import qilinishi kerak

export const useAuthStore = defineStore('auth', () => {
  // Ma'lumotlarni saqlash uchun
  const user = ref(JSON.parse(localStorage.getItem('user')))
  const token = ref(localStorage.getItem('token'))

  // Foydalanuvchi tizimga kirganmi yoki yo'qligini tekshirish uchun
  const isAuthenticated = computed(() => !!token.value)

  // Login muvaffaqiyatli bo'lganda chaqiriladigan funksiya
  function setAuthData(userData, authToken) {
    user.value = userData
    token.value = authToken
    localStorage.setItem('user', JSON.stringify(userData))
    localStorage.setItem('token', authToken)
  }

  // Tizimdan chiqqanda chaqiriladigan funksiya
  function clearAuthData() {
    user.value = null
    token.value = null
    localStorage.removeItem('user')
    localStorage.removeItem('token')
  }

  async function logout() {
    try {
      // Backendga tokenni o'chirish so'rovini yuboramiz (Axios kerak)
      // Eslatma: Agar sizda global axios instance (masalan, interceptorlar bilan) mavjud bo'lsa,
      // u yerda token avtomatik yuboriladi. Agar yo'q bo'lsa, tokenni qo'shish kerak:
      /*
      await axios.post('/v1/logout', {}, {
        headers: {
          Authorization: `Bearer ${token.value}`
        }
      })
      */
      await axios.post('/v1/logout')
    } catch (error) {
      console.error("Logout xatosi:", error)
    } finally {
      clearAuthData()
    }
  }

  return { user, token, isAuthenticated, setAuthData, clearAuthData, logout }
})

