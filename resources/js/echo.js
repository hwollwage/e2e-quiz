import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

const reverbConfig = window.GYMIN_REVERB ?? {};
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME ?? reverbConfig.scheme ?? 'https';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY ?? reverbConfig.key,
    wsHost: import.meta.env.VITE_REVERB_HOST ?? reverbConfig.host,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? reverbConfig.port ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? reverbConfig.port ?? 443,
    forceTLS: reverbScheme === 'https',
    enabledTransports: ['ws', 'wss'],
});
