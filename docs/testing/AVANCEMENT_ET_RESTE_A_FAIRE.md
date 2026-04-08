# Tests automatisés — avancement et reste à faire

Document de suivi pour la suite de tests Manexo (PHPUnit via `php artisan test`).  
Dernière mise à jour indicative : à maintenir lors d’ajouts ou refactors majeurs de tests.

---

## 1. État actuel (synthèse)

- **Commande** : `php artisan test` (depuis la racine du projet).
- **Périmètre** : tests **unitaires** (`tests/Unit/`) et tests **feature** (`tests/Feature/`), base **SQLite en mémoire** en environnement `testing` (voir `phpunit.xml`).
- **Organisation** : namespaces alignés sur les dossiers ; détail dans `tests/Unit/ORGANIZATION.md`.

### 1.1 Ce qui est déjà couvert (par domaine)

| Domaine | Emplacement typique | Contenu principal |
|--------|---------------------|---------------------|
| Email | `tests/Unit/Email/` | Parser MIME, citations, HTML, normalisation Message-ID, boucles auto-réponses, traduction d’erreurs IMAP/SMTP |
| Enums & permissions | `tests/Unit/Enums/`, `tests/Unit/Permissions/` | Valeurs d’enums, labels API/webhooks, `Permission::defaultsForRole`, `Permission::grouped()` |
| Domaine métier léger | `tests/Unit/Domain/`, `tests/Unit/DTO/`, `tests/Unit/Models/` | `HasPublicId`, références courtes, accès plateforme `User`, `TimelineItem`, `ClientAssignmentContext`, modèles légers |
| Helpers & HTTP | `tests/Unit/Helpers/`, `tests/Unit/Http/` | Clés `CacheHelper`, middleware `ApiCheckScope`, `DecodeApiToken` |
| Services | `tests/Unit/Services/` | Horaires ouvrés, onboarding (constantes / état), doublons (sujet trop court), paramètres plateforme |
| API REST v1 | `tests/Feature/Api/` | Authentification Sanctum, scopes, tickets (liste, détail, création), webhooks |
| Livewire | `tests/Feature/Livewire/` | Onboarding (checklist), cloche notifications (`NotificationsBell`) |
| Sécurité & multi-tenant | `tests/Feature/Security/` | Authz, isolation par organisation, fichiers, rate limiting, en-têtes |
| Formulaire public & email | `tests/Feature/PublicForm/`, `tests/Feature/Email/` | Soumission publique, routage ticket |
| Utilisateurs & notifications | `tests/Feature/User/`, `tests/Feature/Notifications/` | Rôles / flags, canaux de notification |
| **Policies (Gate)** | `tests/Feature/Policies/` | `TicketPolicy`, `FormPolicy` ; `DiscussionThreadPolicy` (vue, participants, notes internes) ; `OrganizationPolicy` (paramètres, équipe, suppression org) — `PermissionSeeder::seedForOrganization()` |
| **Jobs & webhooks** | `tests/Feature/Jobs/`, `tests/Feature/Services/` | `DispatchWebhookJob` (HTTP 200, 500 + retry, endpoint inactif, livraison inexistante) — `Http::fake()` ; `WebhookService::dispatch` — `Queue::fake()` + livraisons |
| **Listeners** | `tests/Feature/Listeners/` | `RecordLastLogin` (login `last_login_at` / `last_login_ip`) |

### 1.2 Aides réutilisables dans les tests

- `tests/Support/CreatesTicketDependencies.php` — création catégorie + priorité pour les tickets (contraintes SQLite).
- `tests/Support/CreatesApiToken.php` — token Sanctum lié à une organisation (comme l’UI paramètres API).

---

## 2. Ce qui reste à faire (priorités)

Ce n’est pas une liste exhaustive du code applicatif ; ce sont les **zones les plus utiles** à couvrir ensuite pour réduire la dette de régression.

### 2.1 Priorité haute (impact métier / régression)

| Sujet | Pourquoi |
|-------|----------|
| **Composants Livewire « cœur »** (tickets, admin settings, formulaires) | Grande partie de l’UI ; onboarding couvert ; étendre la couverture (notifications, tickets, etc.). |
| **Policies complémentaires** | Cas limites `TicketPolicy` / `FormPolicy` (support session, trash, etc.). — *`DiscussionThreadPolicy` et `OrganizationPolicy` sont couverts.* |
| **Jobs & files d’attente** | Autres jobs applicables ; *`WebhookService` + `DispatchWebhookJob` couverts (exécution job + dispatch file d’attente).* |
| **Listeners / événements** | Logique métier hors `RecordLastLogin` (effets de bord ticket, etc.) si extraite en listeners dédiés. |

> **Fait récemment** : policies `DiscussionThread` / `Organization` ; listener `RecordLastLogin` ; `DispatchWebhookJob` retry HTTP 500 ; `WebhookServiceDispatchTest` (`Queue::fake()`).

### 2.2 Priorité moyenne

| Sujet | Pourquoi |
|-------|----------|
| **Contrôleurs / exports** (PDF, exports CSV) | Réponses HTTP, en-têtes, contenu minimal ou fichiers temporaires. |
| **Services lourds** (IMAP, sauvegardes) | Souvent mockés ou tests d’intégration optionnels (CI avec services). |
| **WebSockets / temps réel** | Si la logique métier est testable sans navigateur, extraire et test unitaire ; sinon tests manuels ou E2E séparés. |

### 2.3 Couverture de code (rapport HTML)

- La commande `php artisan test --coverage-html coverage-report` **nécessite** une extension PHP de couverture (**PCOV** ou **Xdebug** avec mode coverage).
- Sans cette extension, le message `No code coverage driver available` est **normal** : les tests s’exécutent, mais **aucun rapport de couverture** n’est produit.
- Sur Windows : installer/activer PCOV (ou Xdebug) pour le **même binaire `php`** que celui utilisé dans le terminal, puis vérifier avec `php -m` que l’extension est chargée.

---

## 3. Objectifs de qualité (recommandations)

- Viser des **tests stables** (pas de dépendance réseau, pas d’horloge non figée) ; utiliser `Carbon::setTestNow()` si besoin.
- Préférer **tests unitaires** pour la logique pure et **tests Feature** pour les flux HTTP / API / Livewire.
- Ne pas viser **100 % de couverture** comme objectif absolu ; couvrir surtout les **chemins critiques** (création ticket, auth, API, multi-tenant).

---

## 4. Fichiers de référence dans le dépôt

| Fichier | Rôle |
|---------|------|
| `phpunit.xml` | Suites Unit / Feature, env `testing`, SQLite |
| `tests/Unit/ORGANIZATION.md` | Structure des tests unitaires + rappel couverture |
| `tests/TestCase.php` | Traits partagés (`CreatesTicketDependencies`, `CreatesApiToken`) |

---

## 5. Prochaine mise à jour du document

À actualiser lorsque :

- de nouveaux dossiers majeurs de tests sont ajoutés ;
- une zone listée en section 2 est considérée comme « couverte » ;
- la stratégie de CI (obligatoire : `php artisan test` uniquement, ou couverture minimale) change.
