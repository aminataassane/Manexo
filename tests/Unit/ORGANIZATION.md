# Organisation des tests unitaires (`tests/Unit`)

Les tests sont regroupés par **domaine technique**, avec le namespace PSR-4 `Tests\Unit\…` aligné sur les dossiers.

| Dossier | Contenu |
|--------|---------|
| `Email/` | Parsing MIME, citations, HTML, routage email, boucles, erreurs SMTP/IMAP |
| `Enums/` | Valeurs des enums, labels API/Webhooks, rôle plateforme |
| `Permissions/` | `Permission::defaultsForRole`, `Permission::grouped()` |
| `Domain/` | IDs publics (`HasPublicId`), références courtes, accès plateforme `User` |
| `Helpers/` | Formats de clés de cache (`CacheHelper`) |
| `Http/` | Middleware (`ApiCheckScope`, `DecodeApiToken`) |
| `DTO/` | `TimelineItem`, `ClientAssignmentContext` |
| `Models/` | Comportements légers sur modèles (`TicketMessage`, `PlatformSetting`) |
| `Services/` | Logique métier testable (SLA horaires, onboarding, doublons, paramètres plateforme) |

**Convention**

- `PHPUnit\Framework\TestCase` : pas de base de données, pas d’application Laravel (sauf helpers chargés par Composer).
- `Tests\TestCase` : Laravel minimal (traductions, `response()`, `RefreshDatabase` quand nécessaire).

Les parcours utilisateur (routes, Livewire, politiques avec requêtes) restent dans `tests/Feature/`.

### Couverture HTML (optionnel)

Avec **PCOV** ou **Xdebug** activé pour PHP :

```bash
php artisan test --coverage-html=coverage-report
```

Sans extension de couverture, la commande échoue — lancer `php artisan test` suffit pour la CI locale.

### Feature — organisation utile

| Dossier | Contenu |
|--------|---------|
| `Feature/Api/` | API REST v1 (`/api/v1/*`, Sanctum, scopes) |
| `Feature/Livewire/` | Composants Livewire (ex. onboarding dashboard) |
| `Feature/Security/` | Authz, isolation tenant, fichiers, rate limit |
| `Feature/Email/`, `Feature/PublicForm/`, etc. | Domaines métier |
