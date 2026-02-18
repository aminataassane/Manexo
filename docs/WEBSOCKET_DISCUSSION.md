# Discussion en temps réel (WebSocket)

La discussion des tickets peut fonctionner en temps réel grâce à **Laravel Reverb** (serveur WebSocket) et **Laravel Echo** côté frontend.

## Comportement

- **Sans WebSocket** : les messages s’affichent dès que vous envoyez (rechargement Livewire).
- **Avec WebSocket** : quand un autre utilisateur envoie un message sur le même ticket, il apparaît immédiatement chez tous les participants sans recharger la page.

## 1. Backend (Laravel Reverb)

```bash
composer require laravel/reverb
php artisan reverb:install
```

Dans `.env` :

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=manexo
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

Générer une clé (optionnel) :

```bash
php artisan key:generate
# ou pour Reverb : utiliser une chaîne aléatoire pour REVERB_APP_KEY et REVERB_APP_SECRET
```

Démarrer le serveur WebSocket :

```bash
php artisan reverb:start
```

En local, laisser Reverb tourner dans un terminal (ou utiliser `php artisan reverb:start` en arrière-plan).

## 2. File de diffusion (queue)

Les événements de diffusion sont mis en file. Utiliser la queue (ex. `database`) :

```env
QUEUE_CONNECTION=database
```

Puis lancer le worker :

```bash
php artisan queue:work
```

## 3. Frontend (Laravel Echo)

```bash
npm install laravel-echo pusher-js
```

Dans `.env`, exposer les variables à Vite (préfixe `VITE_`) :

```env
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

Dans `resources/js/app.js`, activer Echo :

```js
import './bootstrap';
import './echo';
```

Reconstruire les assets :

```bash
npm run build
# ou npm run dev
```

## 4. Vérification

1. Ouvrir un ticket (liste des tickets → clic sur un ticket).
2. Ouvrir la même page dans un autre navigateur (ou session privée) avec un autre utilisateur autorisé.
3. Envoyer un message depuis l’un : il doit apparaître immédiatement chez l’autre.

## Fichiers concernés

| Fichier | Rôle |
|--------|------|
| `app/Events/TicketMessageSent.php` | Événement diffusé sur le canal privé `ticket.{id}` |
| `routes/channels.php` | Autorisation du canal (créateur, assigné, membre de l’organisation) |
| `app/Livewire/Tickets/Discussion.php` | Envoi de message + `event(new TicketMessageSent(...))` |
| `resources/views/livewire/tickets/discussion.blade.php` | Alpine écoute `Echo.private('ticket.' + id).listen('.message.sent', ...)` |
| `resources/js/echo.js` | Initialisation de `window.Echo` (Reverb) |

## Dépannage

- **Les messages n’arrivent pas en temps réel** : vérifier que `php artisan reverb:start` et `php artisan queue:work` tournent, que `BROADCAST_CONNECTION=reverb` et que le front charge bien `echo.js` avec les variables `VITE_REVERB_*` définies.
- **401 sur /broadcasting/auth** : l’utilisateur doit être connecté ; le canal `ticket.{id}` n’est autorisé que pour le créateur du ticket, l’assigné ou un membre de l’organisation du ticket.
