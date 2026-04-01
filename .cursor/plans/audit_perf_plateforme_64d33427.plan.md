---
name: Audit perf plateforme
overview: Cartographie des goulots d’étranglement (SQL, algorithmes PHP, Livewire) observés dans le code ticketing et les zones connexes, avec des pistes d’optimisation priorisées et mesurables — sans optimisation « au hasard ».
todos:
    - id: index-kanban-branch
      content: "Tickets Index: séparer chemins requête liste vs Kanban (éviter paginate inutile en mode Kanban)"
      status: completed
    - id: discussion-single-sidebar
      content: "Discussion: supprimer doublon TicketSidebar (lazy mobile ou un composant + UI)"
      status: completed
    - id: kanban-query-strategy
      content: "Kanban: remplacer limit 250 + PHP par requêtes par statut ou fenêtre SQL (valider métier)"
      status: completed
    - id: message-search-index
      content: "Recherche ticket_messages: index trigram ou FTS + ajuster requête"
      status: completed
    - id: timeline-incremental
      content: "TicketTimeline: éviter get() complet de l’historique à chaque render après loadMore"
      status: completed
    - id: validate-sql-plans
      content: Mesurer avec Telescope/Debugbar + EXPLAIN sur filtres whereHas et sidebar
      status: completed
isProject: false
---

Voici une liste structurée : d’abord les **causes typiques de lenteur côté backend** (Laravel / PHP / SQL / Livewire), puis **côté frontend**. En fin de section, quelques **points liés à votre projet** (d’après l’audit déjà fait dans le dépôt).

---

## Backend — ce qui peut ralentir (liste de contrôle)

### Base de données & SQL

- **Requêtes nombreuses** (N+1) : une requête par ligne au lieu d’un `with()` / jointure.

- **Absence d’index** sur colonnes filtrées, jointées ou triées `WHERE`, `JOIN`, `ORDER BY`, clés étrangères, pivots).

- \*`LIKE` / `ILIKE '%…%'`\*\* sur grosses tables sans index adapté (ex. trigram / FTS).

- **Sous-requêtes `EXISTS` / `whereHas`** coûteuses si pivots ou FK non indexés.

- **Agrégats** `COUNT`, `GROUP BY`) sur grandes tables sans index couvrant partiellement le filtre.

- **Sélection de trop de colonnes** `SELECT `\* ou modèles lourds) alors que la vue n’en utilise qu’une partie.

- **Pas de pagination** (ou `limit` trop haut) : charger des milliers de lignes en mémoire.

- **Transactions trop longues** ou verrous.

- **Migrations / index** non appliqués en prod (plans de requêtes dégradés).

### Cache & sessions

- **Pas de cache** pour données peu volatiles (listes de référence, compteurs agrégés déjà calculés).

- **Clés de cache trop larges ou invalidation trop agressive** (recalcul constant).

- **Session / Redis** lent ou sur disque réseau.

### PHP & application

- **Boucles PHP** sur de gros tableaux (filtrage / tri faisable en SQL).

- **Traitement répété** du même résultat à chaque requête au lieu de mutualiser.

- **Sérialisation lourde** (gros tableaux Livewire, fichiers en base64).

- **Files I/O** : logs verbeux, stockage lent (ex. projet sur sync cloud).

- **Files d’upload** traités de façon synchrone lourde sans file d’attente.

### Laravel / Livewire (backend “perçu” comme lent)

- \*`render()` qui refait\*\* les mêmes requêtes à chaque interaction.

- **Plusieurs composants Livewire** sur une page = plusieurs cycles serveur + HTML.

- \*`$refresh` ou listeners larges\*\* : rerend tout le composant (requêtes + vue).

- **Propriétés publiques énormes** : hydration / payload JSON plus lourd.

- \*`wire:model.live` partout\*\* : allers-retours serveur très fréquents.

### Réseau & infra

- **TTFB élevé** : PHP-FPM, OPcache, connexion DB distante, TLS.

- **Pas de `config:cache` / `route:cache` / `view:cache`** en prod (latence de bootstrap).

---

## Frontend — ce qui peut ralentir (liste de contrôle)

### Chargement initial (document & assets)

- **Beaucoup de fichiers JS/CSS** (pas assez regroupés ou pas de cache navigateur).

- **Vite en dev** : HMR et bundles non minifiés = plus lent qu’en prod.

- **Polices externes** (Google Fonts) : requêtes réseau + FOUT/FOIT.

- **Scripts tiers** (analytics, Iconify CDN, etc.) : chaîne de dépendances réseau.

- **Images non optimisées** (poids, dimensions, pas de lazy loading).

### Après le premier rendu (perception)

- **Hydratation Livewire** : gros DOM + beaucoup de composants.

- **Alpine** : expressions lourdes, trop de `x-effect`, téléportations qui recalculent.

- **Re-rendus** : tout le DOM qui clignote ou se remorph à chaque réponse Livewire.

### Interactions

- **Pas de feedback** au clic `wire:loading` absent) → double clics et attente perçue comme “rien ne se passe”.

- **Actions synchrones** côté serveur longues sans état “loading” sur le bouton.

### WebSockets / temps réel

- **Echo / Reverb** : reconnexions, scripts chargés tard, erreurs console (souvent confondues avec lenteur app).

### Navigateur & environnement

- **Extensions** `content.js`, `polyfill.js`, etc.) : erreurs console, pas le bundle app.

- **Onglets / mémoire** saturés.

---

## Liens avec **votre** codebase (déjà identifiés dans l’audit)

| Zone | Risque backend | Risque frontend |

|------|----------------|-----------------|

| **Tickets Index** | Filtres `whereHas`, recherche dans les messages, Kanban = plusieurs requêtes par statut (volontaire) | Liste + filtres Livewire, beaucoup de HTML |

| **Page discussion** | `Discussion` + `TicketSidebar` chargeaient le ticket (sidebar unifiée ensuite pour éviter le double) | Timeline + composer + sidebar = plusieurs composants |

| **TicketTimeline** | Comptages + rechargement des messages (optimisation par IDs chargés) | Scroll, groupes par date |

| **TicketSidebar** | Données org en cache + requêtes pièces jointes / groupe | Formulaires lourds dans le panneau |

| **Admin Settings** | Fichier / écran souvent très chargé | Beaucoup de champs |

Les migrations d’index `2026_03_28_100000_…`, trigram sur `ticket_messages.body` si migré) visent précisément la partie **SQL**.

---

## Comment “lister chaque requête” concrètement pour **toutes** les pages

Sans outil, c’est impossible page par page à la main. En pratique :

1. **Backend** : Laravel **Telescope** ou **Debugbar** (onglet requêtes) sur chaque URL importante ; noter nombre de requêtes, durées, doublons.

2. **SQL** : `EXPLAIN (ANALYZE, BUFFERS)` sur les requêtes les plus lentes (vous avez aussi la commande `manexo:diagnose-ticket-queries` si elle est présente dans le projet).

3. **Frontend** : DevTools **Network** (waterfall, TTFB vs téléchargement JS) + **Performance** (long tasks, JS).

---

Je suis en **mode Ask** : je ne peux pas modifier le code ni lancer ces outils à ta place. Si tu veux qu’on **cartographie fichier par fichier** (grep ciblé sur `Livewire`, `->get()`, `paginate`, etc.) et qu’on produise une **liste priorisée par module**, passe en **mode Agent** et précise les zones (tickets seulement vs toute l’app).
