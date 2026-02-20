/**
 * Laravel Echo (WebSocket) - Discussion en temps réel
 * Nécessite: npm install laravel-echo pusher-js
 * .env: BROADCAST_CONNECTION=reverb, VITE_REVERB_APP_KEY=..., etc.
 *
 * IMPORTANT: static imports are used intentionally so that Echo is available
 * synchronously on window.Echo BEFORE Livewire initialises its echo-private
 * listeners. Dynamic import() would resolve too late and cause
 * "Laravel Echo cannot be found" errors in Livewire's installHook.
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const key = import.meta.env.VITE_REVERB_APP_KEY;

if (key) {
    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key,
        wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
        wsPort: import.meta.env.VITE_REVERB_PORT || '8080',
        wssPort: import.meta.env.VITE_REVERB_PORT || '8080',
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'http') === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                Accept: 'application/json',
            },
        },
    });
}
