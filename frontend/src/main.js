import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { useAuthStore } from './stores/auth'

import App from './App.vue'
import router from './router'

import './index.css' 

const app = createApp(App)

app.use(createPinia())
app.use(router)

const { initialize } = useAuthStore()
initialize()

app.mount('#app')
