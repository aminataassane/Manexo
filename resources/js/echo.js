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
const echoEnabled = document.body?.dataset?.echoEnabled === '1';

if (key && echoEnabled) {
    const wsHost = import.meta.env.VITE_REVERB_HOST || 'localhost';
    const wsPort = import.meta.env.VITE_REVERB_PORT || '8080';
    let scheme = import.meta.env.VITE_REVERB_SCHEME || 'http';
    /*
     * En local, Reverb écoute souvent en clair sur 8080. Si .env reprend APP_URL en https,
     * VITE_REVERB_SCHEME peut valoir https → le client tente wss://localhost:8080 et échoue.
     * Forcer ws sauf si VITE_REVERB_FORCE_TLS=true (proxy TLS devant Reverb).
     */
    const isLocalHost = wsHost === 'localhost' || wsHost === '127.0.0.1';
    const forceTlsEnv = import.meta.env.VITE_REVERB_FORCE_TLS === 'true';
    if (!forceTlsEnv && scheme === 'https' && isLocalHost && String(wsPort) === '8080') {
        scheme = 'http';
    }

    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key,
        wsHost,
        wsPort,
        wssPort: wsPort,
        forceTLS: scheme === 'https',
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
