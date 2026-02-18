import './bootstrap';
// Discussion en temps réel : si Reverb est configuré (VITE_REVERB_APP_KEY), Echo est chargé
if (import.meta.env.VITE_REVERB_APP_KEY) {
    import('./echo');
}