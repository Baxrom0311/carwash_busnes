// FAYL: src/plugins/axios.js
import axios from 'axios'
// O'ZGARISH: Bizga endi bu yerda Pinia Store kerak emas
// import { useAuthStore } from '@/stores/auth'

axios.defaults.baseURL = 'http://127.0.0.1:8000/api/v1'
axios.defaults.headers.common['Accept'] = 'application/json'

// Har bir so'rov yuborilishidan OLDIN ishga tushadi
axios.interceptors.request.use(function (config) {
  // O'ZGARISH: Token'ni Pinia'dan emas, to'g'ridan-to'g'ri localStorage'dan olamiz
  const token = localStorage.getItem('token')

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
}, function (error) {
  return Promise.reject(error)
})

export default axios
