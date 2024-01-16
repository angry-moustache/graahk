import './fit-text';
import Pusher from 'pusher-js';

window.pusher = new Pusher('b8382356f8042afa07bc', {
  cluster: 'eu'
})

window.channel = window.pusher.subscribe('my-channel')
