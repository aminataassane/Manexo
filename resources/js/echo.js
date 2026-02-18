/**
 * Laravel Echo (WebSocket) - Discussion en temps réel
 * Activer: npm install laravel-echo pusher-js
 * Puis dans .env: BROADCAST_CONNECTION=reverb, VITE_REVERB_APP_KEY=..., etc.
 */
const key = import.meta.env.VITE_REVERB_APP_KEY;
const host = import.meta.env.VITE_REVERB_HOST || 'localhost';
const port = import.meta.env.VITE_REVERB_PORT || '8080';
const scheme = import.meta.env.VITE_REVERB_SCHEME || 'http';

if (key) {
    import('laravel-echo')
        .then(({ default: Echo }) => import('pusher-js').then(({ default: Pusher }) => ({ Echo, Pusher })))
        .then(({ Echo, Pusher }) => {
            window.Pusher = Pusher;
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key,
                wsHost: host,
                wsPort: port,
                wssPort: port,
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
        })
        .catch(() => {});
}
