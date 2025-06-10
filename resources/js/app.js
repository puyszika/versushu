import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from 'axios';
import Alpine from 'alpinejs';
import Matchmaking from './matchmaking.js';
import Lobby from './Lobby';
import './bootstrap';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

window.axios = axios;
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

console.log("✅ Vite működik és az app.js betöltődött!");
console.log("Teszt: axios =", typeof axios);

// Alpine setup
window.Alpine = Alpine;
window.Matchmaking = Matchmaking;
Alpine.data('Lobby', Lobby);
Alpine.start();
