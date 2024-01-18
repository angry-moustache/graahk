import './fit-text';
import Pusher from 'pusher-js';
import Play from './components/Play.vue';
import { createApp } from 'vue';
import axios from 'axios';

window.axios = axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

window.pusher = new Pusher(
  'b8382356f8042afa07bc',
  { cluster: 'eu' }
)

createApp({}).component('Play', Play).mount('#app')
