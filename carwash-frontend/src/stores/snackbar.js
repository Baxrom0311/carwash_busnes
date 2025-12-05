// FAYL: src/stores/snackbar.js
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useSnackbarStore = defineStore('snackbar', () => {
  const isVisible = ref(false)
  const message = ref('')
  const color = ref('info') // info, success, warning, error

  function show(newMessage, newColor = 'success') {
    message.value = newMessage
    color.value = newColor
    isVisible.value = true
  }

  function hide() {
    isVisible.value = false
  }

  return { isVisible, message, color, show, hide }
})
