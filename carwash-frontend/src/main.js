// FAYL: src/main.js
import './plugins/axios'
import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'

// --- VUETIFY IMPORTLARI ---
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import 'material-design-icons-iconfont/dist/material-design-icons.css' // Ikonkalar
import { aliases, md } from 'vuetify/iconsets/md'
// -------------------------

const vuetify = createVuetify({
  components,
  directives,
  icons: {
    defaultSet: 'md',
    aliases,
    sets: {
      md,
    },
  },
})

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(vuetify) // <<< VUETIFY'NI ILOVAGA ULAYMIZ

app.mount('#app')
