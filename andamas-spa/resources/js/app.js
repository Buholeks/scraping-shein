// resources/js/app.js
import { createApp } from 'vue'
import App from './App.vue'
import { createPinia } from 'pinia'
import router from './router'
import Swal from 'sweetalert2'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

// Íconos que vas a usar
import {
  faLink,
  faTimes,
  faSearch,
  faSpinner,
  faPeopleCarryBox,
  faPenToSquare,
  faTrashCan,
  faS,
  faBarcode,
  faHouse,
  faUsers,
  faMagnifyingGlass
} from '@fortawesome/free-solid-svg-icons'

// Agrega los íconos a la librería
library.add(
  faLink,
  faTimes,
  faSearch,
  faSpinner,
  faPeopleCarryBox,
  faPenToSquare,
  faTrashCan,
  faS,
  faBarcode,
  faHouse,
  faUsers,
  faMagnifyingGlass)

// Montaje único
const app = createApp(App)

app.use(createPinia())
app.use(router)

app.component('fa', FontAwesomeIcon)

// SweetAlert global
window.Swal = Swal

app.mount('#app')
