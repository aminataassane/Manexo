<?php

namespace App\Livewire\Admin;

use App\Enums\ApiTokenScope;
use App\Enums\Permission;
use App\Enums\WebhookEvent;
use App\Helpers\CacheHelper;
use App\Models\AutomationRule;
use App\Models\Form;
use App\Models\KbArticle;
use App\Models\KbCategory;
use App\Models\Organization;
use App\Models\OrganizationFunction;
use App\Models\OrganizationMailbox;
use App\Models\OrganizationMembership;
use App\Models\OrganizationRolePermission;
use App\Models\RoleDefinition;
use App\Models\SlaPolicy;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketGroup;
use App\Models\TicketPriority;
use App\Models\WebhookEndpoint;
use App\Services\Email\ImapService;
use App\Services\Email\MailErrorTranslator;
use App\Services\OrganizationAuditService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.manexo-app')]
#[Title('Paramètres')]
class Settings extends Component
{
    use WithFileUploads;

    /** 1 = en-tête + navigation, 2 = listes / onglets (requêtes cache) */
    public int $loadStage = 1;
    public string $activeTab = 'branding';

    public function loadSettingsBody(): void
    {
        if ($this->loadStage < 2) {
            $this->loadStage = 2;
        }
    }

    public function setActiveTab(string $tab): void
    {
        if ($tab === $this->activeTab) {
            return;
        }

        $allowedTabs = [
            'branding',
            'tickets',
            'categories',
            'groups',
            'priorities',
            'functions',
            'forms',
            'email',
            'sla',
            'automations',
            'knowledge_base',
            'api',
            'roles',
            'maintenance',
            'danger',
            'webhooks',
        ];

        if (in_array($tab, $allowedTabs, true)) {
            $this->activeTab = $tab;
        }
    }

    public bool $canManage = false;

    public bool $isOwner = false;

    public string $name = '';

    public string $slug = '';

    public ?string $primary_color = null;

    public bool $slugManuallyEdited = false;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $logo = null;

    public ?string $currentLogoUrl = null;

    public ?int $default_category_id = null;

    public ?int $default_priority_id = null;

    public ?int $auto_close_days = null;

    public bool $members_can_edit = true;

    public bool $members_can_delete = false;

    // --- Category CRUD
    public string $newCategoryName = '';

    public ?int $newCategoryDefaultGroupId = null;

    public ?int $newCategoryDefaultFormId = null;

    public bool $newCategoryRequiresApproval = false;

    public ?string $newCategoryApprovalType = null;

    public ?int $newCategoryApprovalUserId = null;

    public ?string $newCategoryApprovalRole = null;

    public ?int $editingCategoryId = null;

    public string $editingCategoryName = '';

    public ?int $editingCategoryDefaultGroupId = null;

    public ?int $editingCategoryDefaultFormId = null;

    public bool $editingCategoryRequiresApproval = false;

    public ?string $editingCategoryApprovalType = null;

    public ?int $editingCategoryApprovalUserId = null;

    public ?string $editingCategoryApprovalRole = null;

    // --- Priority CRUD
    public string $newPriorityName = '';

    public ?int $newPriorityLevel = null;

    public ?int $editingPriorityId = null;

    public string $editingPriorityName = '';

    public ?int $editingPriorityLevel = null;

    // --- Function (fonction métier) CRUD
    public string $newFunctionName = '';

    public ?int $editingFunctionId = null;

    public string $editingFunctionName = '';

    // --- Group CRUD
    public string $newGroupName = '';

    public ?string $newGroupColor = null;

    public ?int $editingGroupId = null;

    public string $editingGroupName = '';

    public ?string $editingGroupColor = null;

    public string $dangerConfirmName = '';

    /** Mode de résolution des tickets (flexible | strict) */
    public string $ticket_resolution_mode = 'flexible';

    /** Paramètres formulaires (onglet Formulaires) */
    public ?int $forms_default_due_days = 7;

    public bool $forms_notify_on_response = true;

    public ?int $forms_default_expiry_days = 30;

    /** Message de succès après enregistrement (affiché sans redirection). */
    public string $successMessage = '';

    // --- Roles & Permissions
    /** @var array<string, array<string, bool>> [role => [permission => bool]] */
    public array $rolePermissions = [];

    public string $selectedRole = 'admin';

    // --- Role CRUD
    public string $newRoleName = '';

    public string $newRoleBaseSlug = '';

    public ?int $editingRoleId = null;

    public string $editingRoleName = '';

    // --- Email Mailbox
    public string $mailboxEmail = '';

    public string $mailboxDisplayName = '';

    public string $mailboxImapHost = '';

    public int $mailboxImapPort = 993;

    public string $mailboxImapUsername = '';

    public string $mailboxImapPassword = '';

    public string $mailboxImapEncryption = 'ssl';

    public string $mailboxImapFolder = 'INBOX';

    public ?int $mailboxDefaultCategoryId = null;

    public ?int $mailboxDefaultPriorityId = null;

    public ?int $mailboxDefaultGroupId = null;

    public bool $mailboxIsActive = false;

    public ?string $mailboxTestResult = null;

    public ?string $mailboxLastError = null;

    public ?string $mailboxLastFetchedAt = null;

    // --- Email Mailbox SMTP
    public string $mailboxSmtpHost = '';

    public int $mailboxSmtpPort = 587;

    public string $mailboxSmtpUsername = '';

    public string $mailboxSmtpPassword = '';

    public string $mailboxSmtpEncryption = 'tls';

    public ?string $mailboxSmtpTestResult = null;

    // --- SLA
    public bool $slaEnabled = false;

    public int $slaAtRiskThreshold = 80;

    /** @var array<int, array{first_response_minutes: ?int, resolution_minutes: ?int, is_active: bool}> */
    public array $slaPolicies = [];

    // --- Automations
    public bool $automationsEnabled = true;

    public array $editingRule = [];

    public ?int $editingRuleId = null;

    public bool $showRuleModal = false;

    // --- API Tokens
    public string $newTokenName = '';

    public array $newTokenScopes = [];

    public ?string $newTokenExpiresAt = null;

    public ?string $createdTokenPlainText = null;

    // --- Webhooks
    public string $newWebhookUrl = '';

    public string $newWebhookDescription = '';

    public array $newWebhookEvents = [];

    public ?int $editingWebhookId = null;

    public string $editingWebhookUrl = '';

    public string $editingWebhookDescription = '';

    public array $editingWebhookEvents = [];

    public bool $editingWebhookIsActive = true;

    public ?string $createdWebhookSecret = null;

    // --- Knowledge Base
    public string $newKbCategoryName = '';

    public string $newKbCategoryDescription = '';

    public string $newKbCategoryIcon = '';

    public ?int $editingKbCategoryId = null;

    public string $editingKbCategoryName = '';

    public string $editingKbCategoryDescription = '';

    public string $editingKbCategoryIcon = '';

    public bool $showKbArticleModal = false;

    public ?int $editingKbArticleId = null;

    public string $kbArticleTitle = '';

    public ?int $kbArticleCategoryId = null;

    public string $kbArticleContent = '';

    public string $kbArticleStatus = 'published';

    public string $kbArticleVisibility = 'public';

    public string $kbArticleKeywords = '';

    private function orgId(): int
    {
        return (int) session('current_organization_id');
    }

    private function orgOrFail(): Organization
    {
        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $org = Organization::query()->find($orgId);
        abort_if(! $org, 404);

        return $org;
    }

    public function mount(): void
    {
        // Ensure settings body is immediately available even with wire:navigate.
        // Some clients may skip/lag wire:init, which left the page in stage 1 skeleton.
        $this->loadStage = 2;

        $org = $this->orgOrFail();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->canManage = $user->hasAnyPermission([
            Permission::SettingsManageBranding,
            Permission::SettingsManageCategories,
            Permission::SettingsManagePriorities,
            Permission::SettingsManageFunctions,
            Permission::SettingsManageForms,
            Permission::SettingsManageRoles,
            Permission::SettingsManageEmail,
            Permission::SettingsManageSla,
            Permission::SettingsManageAutomations,
            Permission::SettingsManageApprovals,
            Permission::SettingsManageApi,
            Permission::SettingsManageWebhooks,
            Permission::SettingsManageKnowledgeBase,
        ]);
        $this->isOwner = $user->hasPermission(Permission::SettingsDeleteOrg);

        $this->name = (string) $org->name;
        $this->slug = (string) $org->slug;
        $this->primary_color = $this->normalizeHexColorForDisplay($org->primary_color);
        $this->currentLogoUrl = $org->logo_path ? asset('storage/'.ltrim($org->logo_path, '/')) : null;

        $settings = is_array($org->settings) ? $org->settings : [];

        $this->default_category_id = isset($settings['defaults']['ticket_category_id'])
            ? (int) $settings['defaults']['ticket_category_id']
            : null;
        $this->default_priority_id = isset($settings['defaults']['ticket_priority_id'])
            ? (int) $settings['defaults']['ticket_priority_id']
            : null;
        $this->auto_close_days = isset($settings['workflow']['auto_close_days'])
            ? (int) $settings['workflow']['auto_close_days']
            : null;
        $this->members_can_edit = (bool) ($settings['permissions']['members_can_edit'] ?? true);
        $this->members_can_delete = (bool) ($settings['permissions']['members_can_delete'] ?? false);
        $this->ticket_resolution_mode = $settings['workflow']['ticket_resolution_mode'] ?? 'flexible';

        $forms = $settings['forms'] ?? [];
        $this->forms_default_due_days = isset($forms['default_due_days']) ? (int) $forms['default_due_days'] : 7;
        $this->forms_notify_on_response = (bool) ($forms['notify_on_response'] ?? true);
        $this->forms_default_expiry_days = isset($forms['default_expiry_days']) ? (int) $forms['default_expiry_days'] : 30;

        $this->loadRolePermissions();
        $this->mountMailbox();
        $this->mountSla();
        $this->mountAutomations();
    }

    public function mountMailbox(): void
    {
        $orgId = $this->orgId();
        if (! $orgId) {
            return;
        }

        try {
            $mailbox = OrganizationMailbox::query()->where('organization_id', $orgId)->first();
        } catch (\Throwable) {
            return; // Table not yet migrated
        }
        if ($mailbox) {
            $this->mailboxEmail = (string) $mailbox->email;
            $this->mailboxDisplayName = (string) ($mailbox->display_name ?? '');
            $this->mailboxImapHost = (string) $mailbox->imap_host;
            $this->mailboxImapPort = (int) $mailbox->imap_port;
            $this->mailboxImapUsername = (string) $mailbox->imap_username;
            $this->mailboxImapPassword = ''; // Never send back to frontend
            $this->mailboxImapEncryption = (string) $mailbox->imap_encryption;
            $this->mailboxImapFolder = (string) ($mailbox->imap_folder ?: 'INBOX');
            $this->mailboxDefaultCategoryId = $mailbox->default_category_id;
            $this->mailboxDefaultPriorityId = $mailbox->default_priority_id;
            $this->mailboxDefaultGroupId = $mailbox->default_group_id;
            $this->mailboxIsActive = (bool) $mailbox->is_active;
            $this->mailboxLastError = $mailbox->last_error_message;
            $this->mailboxLastFetchedAt = $mailbox->last_fetched_at?->translatedFormat('d/m/Y H:i');

            // SMTP
            $this->mailboxSmtpHost = (string) ($mailbox->smtp_host ?? '');
            $this->mailboxSmtpPort = (int) ($mailbox->smtp_port ?: 587);
            $this->mailboxSmtpUsername = (string) ($mailbox->smtp_username ?? '');
            $this->mailboxSmtpPassword = ''; // Never send back to frontend
            $this->mailboxSmtpEncryption = (string) ($mailbox->smtp_encryption ?? 'tls');
        }
    }

    public function mountSla(): void
    {
        $orgId = $this->orgId();
        if (! $orgId) {
            return;
        }

        try {
            $org = Organization::find($orgId);
            $settings = is_array($org?->settings) ? $org->settings : [];
            $this->slaEnabled = (bool) ($settings['sla']['enabled'] ?? false);
            $this->slaAtRiskThreshold = (int) ($settings['sla']['at_risk_threshold_percent'] ?? 80);

            $priorities = TicketPriority::withoutOrganizationScope()
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderByDesc('level')
                ->get(['id', 'name', 'level']);

            $policies = SlaPolicy::withoutOrganizationScope()
                ->where('organization_id', $orgId)
                ->get()
                ->keyBy('ticket_priority_id');

            $this->slaPolicies = [];
            foreach ($priorities as $priority) {
                $policy = $policies->get($priority->id);
                $this->slaPolicies[$priority->id] = [
                    'first_response_minutes' => $policy?->first_response_minutes,
                    'resolution_minutes' => $policy?->resolution_minutes,
                    'is_active' => $policy ? (bool) $policy->is_active : true,
                ];
            }
        } catch (\Throwable) {
            // Table not yet migrated
        }
    }

    public function saveSlaSettings(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageSla)) {
            $this->addError('slaEnabled', __('Accès refusé.'));

            return;
        }

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $this->validate([
            'slaEnabled' => ['boolean'],
            'slaAtRiskThreshold' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        // Update organization settings
        $org = $this->orgOrFail();
        $settings = is_array($org->settings) ? $org->settings : [];
        $settings['sla'] = [
            'enabled' => (bool) $this->slaEnabled,
            'at_risk_threshold_percent' => (int) $this->slaAtRiskThreshold,
        ];
        $org->update(['settings' => $settings]);

        // Upsert SLA policies per priority
        foreach ($this->slaPolicies as $priorityId => $policyData) {
            $frMinutes = isset($policyData['first_response_minutes']) && $policyData['first_response_minutes'] !== '' && $policyData['first_response_minutes'] !== null
                ? (int) $policyData['first_response_minutes']
                : null;
            $resMinutes = isset($policyData['resolution_minutes']) && $policyData['resolution_minutes'] !== '' && $policyData['resolution_minutes'] !== null
                ? (int) $policyData['resolution_minutes']
                : null;
            $isActive = (bool) ($policyData['is_active'] ?? true);

            // Skip if both are null/empty — no policy to create
            if ($frMinutes === null && $resMinutes === null) {
                // Delete existing policy if any
                SlaPolicy::withoutOrganizationScope()
                    ->where('organization_id', $orgId)
                    ->where('ticket_priority_id', (int) $priorityId)
                    ->delete();

                continue;
            }

            SlaPolicy::withoutOrganizationScope()->updateOrCreate(
                [
                    'organization_id' => $orgId,
                    'ticket_priority_id' => (int) $priorityId,
                ],
                [
                    'first_response_minutes' => $frMinutes,
                    'resolution_minutes' => $resMinutes,
                    'is_active' => $isActive,
                ],
            );
        }

        CacheHelper::invalidateSlaPolicies($orgId);

        OrganizationAuditService::log(
            'settings.sla_updated',
            'organization',
            (int) $org->id,
            ['sla_enabled' => $this->slaEnabled],
        );

        $this->dispatch('toast', type: 'success', message: __('Paramètres SLA enregistrés.'));
    }

    // ─── Automations ──────────────────────────────────────────────────

    public function mountAutomations(): void
    {
        $orgId = $this->orgId();
        if (! $orgId) {
            return;
        }

        try {
            $org = Organization::find($orgId);
            $settings = is_array($org?->settings) ? $org->settings : [];
            $this->automationsEnabled = (bool) ($settings['automations']['enabled'] ?? true);
        } catch (\Throwable) {
            // Table not yet migrated
        }
    }

    public function saveAutomationsSettings(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageAutomations)) {
            $this->addError('automationsEnabled', __('Accès refusé.'));

            return;
        }

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $org = $this->orgOrFail();
        $settings = is_array($org->settings) ? $org->settings : [];
        $settings['automations'] = [
            'enabled' => (bool) $this->automationsEnabled,
        ];
        $org->update(['settings' => $settings]);

        OrganizationAuditService::log(
            'settings.automations_updated',
            'organization',
            (int) $org->id,
            ['automations_enabled' => $this->automationsEnabled],
        );

        $this->dispatch('toast', type: 'success', message: __('Paramètres d\'automatisation enregistrés.'));
    }

    public function openCreateRule(): void
    {
        $this->editingRuleId = null;
        $this->editingRule = [
            'name' => '',
            'description' => '',
            'trigger_type' => 'ticket_created',
            'conditions' => [],
            'actions' => [],
        ];
        $this->showRuleModal = true;
    }

    public function openEditRule(int $id): void
    {
        $orgId = $this->orgId();
        $rule = AutomationRule::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $this->editingRuleId = (int) $rule->id;
        // Transform stored items array back to items_text for the blade input
        $actions = $rule->actions ?? [];
        foreach ($actions as &$action) {
            if (($action['type'] ?? '') === 'add_checklist' && ! empty($action['items'])) {
                $action['items_text'] = collect($action['items'])->pluck('title')->implode(', ');
                unset($action['items']);
            }
        }
        unset($action);

        $this->editingRule = [
            'name' => $rule->name,
            'description' => $rule->description ?? '',
            'trigger_type' => $rule->trigger_type,
            'conditions' => $rule->conditions ?? [],
            'actions' => $actions,
        ];
        $this->showRuleModal = true;
    }

    public function saveRule(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageAutomations)) {
            $this->addError('editingRule.name', __('Accès refusé.'));

            return;
        }

        $this->validate([
            'editingRule.name' => ['required', 'string', 'max:120'],
            'editingRule.trigger_type' => ['required', 'string', 'in:ticket_created,status_changed,priority_changed,sla_at_risk,sla_breached'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $conditions = $this->editingRule['conditions'] ?? [];
        // Clean empty condition values
        $conditions = array_filter($conditions, fn ($v) => $v !== null && $v !== '' && $v !== false);

        $actions = $this->editingRule['actions'] ?? [];
        // Transform items_text into proper items array for add_checklist actions
        foreach ($actions as &$action) {
            if (($action['type'] ?? '') === 'add_checklist' && ! empty($action['items_text'])) {
                $action['items'] = collect(explode(',', $action['items_text']))
                    ->map(fn ($t) => trim($t))
                    ->filter()
                    ->map(fn ($t) => ['title' => $t])
                    ->values()
                    ->all();
                unset($action['items_text']);
            }
        }
        unset($action);

        $data = [
            'organization_id' => $orgId,
            'name' => trim($this->editingRule['name']),
            'description' => trim($this->editingRule['description'] ?? '') ?: null,
            'trigger_type' => $this->editingRule['trigger_type'],
            'conditions' => $conditions,
            'actions' => array_values($actions),
        ];

        if ($this->editingRuleId) {
            $rule = AutomationRule::withoutOrganizationScope()
                ->where('organization_id', $orgId)
                ->whereKey($this->editingRuleId)
                ->firstOrFail();
            $rule->update($data);
        } else {
            $data['sort_order'] = (int) AutomationRule::withoutOrganizationScope()
                ->where('organization_id', $orgId)
                ->max('sort_order') + 1;
            AutomationRule::create($data);
        }

        CacheHelper::invalidateAutomationRules($orgId);
        $wasEditing = $this->editingRuleId;
        $this->showRuleModal = false;
        $this->editingRuleId = null;
        $this->editingRule = [];

        $this->dispatch('toast', type: 'success', message: $wasEditing ? __('Règle mise à jour.') : __('Règle créée.'));
    }

    public function deleteRule(int $id): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageAutomations)) {
            return;
        }

        $orgId = $this->orgId();
        AutomationRule::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->delete();

        CacheHelper::invalidateAutomationRules($orgId);
        $this->dispatch('toast', type: 'success', message: __('Règle supprimée.'));
    }

    public function toggleRule(int $id): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageAutomations)) {
            return;
        }

        $orgId = $this->orgId();
        $rule = AutomationRule::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $rule->update(['is_active' => ! $rule->is_active]);
        CacheHelper::invalidateAutomationRules($orgId);

        $this->dispatch('toast', type: 'success', message: $rule->is_active ? __('Règle activée.') : __('Règle désactivée.'));
    }

    public function moveRuleUp(int $id): void
    {
        $this->reorderRule($id, -1);
    }

    public function moveRuleDown(int $id): void
    {
        $this->reorderRule($id, 1);
    }

    private function reorderRule(int $id, int $direction): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageAutomations)) {
            return;
        }

        $orgId = $this->orgId();
        $rules = AutomationRule::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->orderBy('sort_order')
            ->get();

        $index = $rules->search(fn ($r) => (int) $r->id === $id);
        if ($index === false) {
            return;
        }

        $swapIndex = $index + $direction;
        if ($swapIndex < 0 || $swapIndex >= $rules->count()) {
            return;
        }

        /** @var AutomationRule $currentRule */
        $currentRule = $rules[$index];
        /** @var AutomationRule $swapRule */
        $swapRule = $rules[$swapIndex];

        $tmpOrder = $currentRule->sort_order;
        $currentRule->update(['sort_order' => $swapRule->sort_order]);
        $swapRule->update(['sort_order' => $tmpOrder]);

        CacheHelper::invalidateAutomationRules($orgId);
    }

    public function loadRolePermissions(): void
    {
        $orgId = $this->orgId();
        if (! $orgId) {
            return;
        }

        $existing = OrganizationRolePermission::query()
            ->where('organization_id', $orgId)
            ->get()
            ->groupBy('role')
            ->map(fn ($rows) => $rows->pluck('permission')->flip()->map(fn () => true)->all())
            ->all();

        $allPermissions = array_map(fn (Permission $p) => $p->value, Permission::cases());

        $roleSlugs = RoleDefinition::query()
            ->where('organization_id', $orgId)
            ->pluck('slug');

        $this->rolePermissions = [];
        foreach ($roleSlugs as $slug) {
            $granted = $existing[$slug] ?? [];
            $this->rolePermissions[$slug] = [];
            foreach ($allPermissions as $perm) {
                $this->rolePermissions[$slug][$perm] = isset($granted[$perm]);
            }
        }

        // Ensure selectedRole is valid
        if (! isset($this->rolePermissions[$this->selectedRole]) && $roleSlugs->isNotEmpty()) {
            $this->selectedRole = $roleSlugs->contains('admin') ? 'admin' : $roleSlugs->first();
        }
    }

    public function selectRoleTab(string $role): void
    {
        $orgId = $this->orgId();
        $allowed = RoleDefinition::query()
            ->where('organization_id', $orgId)
            ->pluck('slug')
            ->all();

        if (in_array($role, $allowed, true)) {
            $this->selectedRole = $role;
        }
    }

    /** Bascule une permission pour un rôle (évite wire:model avec clés contenant un point). */
    public function toggleRolePermission(string $role, string $permission): void
    {
        if ($role === 'owner') {
            return;
        }
        $allValid = array_map(fn (Permission $p) => $p->value, Permission::cases());
        if (! in_array($permission, $allValid, true)) {
            return;
        }
        if (! isset($this->rolePermissions[$role])) {
            $this->rolePermissions[$role] = [];
        }
        $current = $this->rolePermissions[$role][$permission] ?? false;
        $this->rolePermissions[$role][$permission] = ! $current;
    }

    public function saveRolePermissions(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageRoles)) {
            $this->addError('rolePermissions', __('Acces refuse.'));

            return;
        }

        $orgId = $this->orgId();
        if (! $orgId) {
            return;
        }

        $role = $this->selectedRole;

        // Owner permissions cannot be changed
        if ($role === 'owner') {
            return;
        }

        $allValid = array_map(fn (Permission $p) => $p->value, Permission::cases());
        $granted = [];
        foreach ($this->rolePermissions[$role] ?? [] as $perm => $enabled) {
            if ($enabled && in_array($perm, $allValid, true)) {
                $granted[] = $perm;
            }
        }

        // Delete all existing permissions for this org+role, then insert new ones
        OrganizationRolePermission::query()
            ->where('organization_id', $orgId)
            ->where('role', $role)
            ->delete();

        $now = now();
        $rows = array_map(fn (string $p) => [
            'organization_id' => $orgId,
            'role' => $role,
            'permission' => $p,
            'created_at' => $now,
            'updated_at' => $now,
        ], $granted);

        if (! empty($rows)) {
            OrganizationRolePermission::query()->insert($rows);
        }

        CacheHelper::invalidateRolePermissions($orgId);

        OrganizationAuditService::log(
            'settings.role_permissions_updated',
            'role',
            null,
            ['role' => $role, 'permissions_count' => count($granted)],
        );

        $this->dispatch('toast', type: 'success', message: __('settings.permissions_saved'));
    }

    /** Enregistre les permissions de tous les rôles (sauf owner) en une fois. */
    public function saveAllRolePermissions(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageRoles)) {
            $this->addError('rolePermissions', __('Acces refuse.'));

            return;
        }

        $orgId = $this->orgId();
        if (! $orgId) {
            return;
        }

        $allValid = array_map(fn (Permission $p) => $p->value, Permission::cases());

        foreach ($this->rolePermissions as $role => $perms) {
            if ($role === 'owner') {
                continue;
            }
            $granted = [];
            foreach ($perms ?? [] as $perm => $enabled) {
                if ($enabled && in_array($perm, $allValid, true)) {
                    $granted[] = $perm;
                }
            }
            OrganizationRolePermission::query()
                ->where('organization_id', $orgId)
                ->where('role', $role)
                ->delete();
            $now = now();
            $rows = array_map(fn (string $p) => [
                'organization_id' => $orgId,
                'role' => $role,
                'permission' => $p,
                'created_at' => $now,
                'updated_at' => $now,
            ], $granted);
            if (! empty($rows)) {
                OrganizationRolePermission::query()->insert($rows);
            }
        }

        CacheHelper::invalidateRolePermissions($orgId);

        OrganizationAuditService::log(
            'settings.all_role_permissions_updated',
            'role',
            null,
            ['roles_count' => count($this->rolePermissions)],
        );

        $this->dispatch('toast', type: 'success', message: __('settings.permissions_saved'));
    }

    public function updatedName(string $value): void
    {
        if ($this->slugManuallyEdited) {
            return;
        }

        $this->slug = Str::slug($value);
    }

    public function updatedSlug(string $value): void
    {
        // Dès que l'utilisateur touche au slug, on n'auto-modifie plus.
        $this->slugManuallyEdited = true;
        $this->slug = Str::slug($value);
    }

    public function updatedLogo(): void
    {
        $this->validate([
            'logo' => ['nullable', 'image', 'max:2048'], // 2MB
        ]);

        // Enregistrer le logo immédiatement : Livewire ne renvoie pas le fichier lors du submit "Save"
        if (! $this->logo || ! $this->canManage) {
            return;
        }

        $org = $this->orgOrFail();
        $ext = $this->logo->getClientOriginalExtension() ?: 'png';
        $filename = 'org-'.$org->id.'-'.Str::lower(Str::random(10)).'.'.$ext;
        $logoPath = $this->logo->storeAs('org-logos', $filename, 'public');

        if ($org->logo_path) {
            Storage::disk('public')->delete($org->logo_path);
        }

        $org->update(['logo_path' => $logoPath]);
        $org->refresh();

        $this->currentLogoUrl = asset('storage/'.ltrim($logoPath, '/'));
        $this->logo = null;

        CacheHelper::invalidateAll($this->orgId());
        $this->dispatch('toast', type: 'success', message: __('Logo enregistré.'));
    }

    /**
     * Normalise une couleur hex pour l'affichage au chargement (avec ou sans #, 3 ou 6 caractères) en #rrggbb.
     */
    private function normalizeHexColorForDisplay(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $this->normalizeHexColor(is_string($value) ? trim($value) : (string) $value) ?: null;
    }

    /**
     * Normalise une couleur hex (avec ou sans #, 3 ou 6 caractères) en #rrggbb.
     */
    private function normalizeHexColor(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        if (str_starts_with($value, '#')) {
            $value = substr($value, 1);
        }
        $value = strtolower($value);
        if (preg_match('/^[0-9a-f]{3}$/', $value)) {
            $value = $value[0].$value[0].$value[1].$value[1].$value[2].$value[2];
        }
        if (! preg_match('/^[0-9a-f]{6}$/', $value)) {
            return null;
        }

        return '#'.$value;
    }

    public function save()
    {
        if (! $this->canManage) {
            $this->successMessage = '';
            $this->addError('canManage', __('Accès refusé: réservé aux admins.'));

            return;
        }

        $this->successMessage = '';
        if ($this->primary_color === '') {
            $this->primary_color = null;
        } else {
            $normalized = $this->normalizeHexColor($this->primary_color);
            if ($normalized === null && $this->primary_color !== null && trim($this->primary_color) !== '') {
                $this->addError('primary_color', __('La couleur doit être un code hex valide (ex: #000000 ou 000000).'));

                return;
            }
            $this->primary_color = $normalized;
        }

        $org = $this->orgOrFail();
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:organizations,slug,'.$org->id],
            'primary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'default_category_id' => ['nullable', 'integer'],
            'default_priority_id' => ['nullable', 'integer'],
            'auto_close_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'members_can_edit' => ['boolean'],
            'members_can_delete' => ['boolean'],
            'ticket_resolution_mode' => ['required', 'in:flexible,strict'],
        ]);

        if ($validated['default_category_id'] ?? null) {
            $catOk = TicketCategory::query()
                ->where('organization_id', $org->id)
                ->where('is_active', true)
                ->whereKey((int) $validated['default_category_id'])
                ->exists();

            if (! $catOk) {
                $this->addError('default_category_id', 'Catégorie invalide.');

                return;
            }
        }

        if ($validated['default_priority_id'] ?? null) {
            $prioOk = TicketPriority::query()
                ->where('organization_id', $org->id)
                ->where('is_active', true)
                ->whereKey((int) $validated['default_priority_id'])
                ->exists();

            if (! $prioOk) {
                $this->addError('default_priority_id', 'Priorité invalide.');

                return;
            }
        }

        $logoPath = $org->logo_path;
        if ($this->logo) {
            $ext = $this->logo->getClientOriginalExtension() ?: 'png';
            $filename = 'org-'.$org->id.'-'.Str::lower(Str::random(10)).'.'.$ext;
            $logoPath = $this->logo->storeAs('org-logos', $filename, 'public');
        }

        $settings = is_array($org->settings) ? $org->settings : [];
        $settings['defaults'] = [
            'ticket_category_id' => $validated['default_category_id'] ?? null,
            'ticket_priority_id' => $validated['default_priority_id'] ?? null,
        ];
        $settings['workflow'] = [
            'auto_close_days' => $validated['auto_close_days'] ?? null,
            'ticket_resolution_mode' => $this->ticket_resolution_mode,
        ];
        $settings['permissions'] = [
            'members_can_edit' => (bool) ($validated['members_can_edit'] ?? true),
            'members_can_delete' => (bool) ($validated['members_can_delete'] ?? false),
        ];

        $org->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'primary_color' => $validated['primary_color'] ?: null,
            'logo_path' => $logoPath,
            'settings' => $settings,
        ]);

        $org->refresh();

        $this->name = (string) $org->name;
        $this->slug = (string) $org->slug;
        $this->primary_color = $this->normalizeHexColorForDisplay($org->primary_color);

        $this->currentLogoUrl = $org->logo_path ? asset('storage/'.ltrim($org->logo_path, '/')) : null;
        $this->logo = null;

        $settings = is_array($org->settings) ? $org->settings : [];
        $this->default_category_id = isset($settings['defaults']['ticket_category_id']) ? (int) $settings['defaults']['ticket_category_id'] : null;
        $this->default_priority_id = isset($settings['defaults']['ticket_priority_id']) ? (int) $settings['defaults']['ticket_priority_id'] : null;
        $this->auto_close_days = isset($settings['workflow']['auto_close_days']) ? (int) $settings['workflow']['auto_close_days'] : null;
        $this->ticket_resolution_mode = $settings['workflow']['ticket_resolution_mode'] ?? 'flexible';
        $this->members_can_edit = (bool) ($settings['permissions']['members_can_edit'] ?? true);
        $this->members_can_delete = (bool) ($settings['permissions']['members_can_delete'] ?? false);

        view()->share('currentOrganization', $org);

        CacheHelper::invalidateAll($this->orgId());

        OrganizationAuditService::log(
            'settings.updated',
            'organization',
            (int) $org->id,
            ['fields' => array_keys($validated)],
        );

        $this->successMessage = __('Paramètres mis à jour.');
    }

    public function saveFormsSettings(): void
    {
        if (! $this->canManage) {
            $this->addError('canManage', __('Accès refusé.'));

            return;
        }

        $validated = $this->validate([
            'forms_default_due_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'forms_notify_on_response' => ['boolean'],
            'forms_default_expiry_days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ]);

        $org = $this->orgOrFail();
        $settings = is_array($org->settings) ? $org->settings : [];
        $settings['forms'] = [
            'default_due_days' => $validated['forms_default_due_days'] ?? null,
            'notify_on_response' => (bool) ($validated['forms_notify_on_response'] ?? true),
            'default_expiry_days' => $validated['forms_default_expiry_days'] ?? null,
        ];
        $org->update(['settings' => $settings]);

        $this->forms_default_due_days = $validated['forms_default_due_days'] ?? null;
        $this->forms_notify_on_response = (bool) ($validated['forms_notify_on_response'] ?? true);
        $this->forms_default_expiry_days = $validated['forms_default_expiry_days'] ?? null;

        CacheHelper::invalidateAll($this->orgId());

        OrganizationAuditService::log(
            'settings.forms_updated',
            'organization',
            (int) $org->id,
            ['fields' => array_keys($validated)],
        );

        $this->dispatch('toast', type: 'success', message: __('settings.forms_settings_saved'));
    }

    public function removeLogo(): void
    {
        if (! $this->canManage) {
            session()->flash('settings_status', 'Accès refusé: réservé aux admins.');

            return;
        }

        $org = $this->orgOrFail();

        if ($org->logo_path) {
            Storage::disk('public')->delete($org->logo_path);
        }

        $org->update(['logo_path' => null]);
        $this->currentLogoUrl = null;
        $this->logo = null;

        session()->flash('settings_status', 'Logo supprimé.');
    }

    public function deleteOrganization(): void
    {
        if (! $this->isOwner) {
            session()->flash('settings_status', 'Accès refusé: réservé au propriétaire.');

            return;
        }

        $org = $this->orgOrFail();

        if (trim($this->dangerConfirmName) !== (string) $org->name) {
            $this->addError('dangerConfirmName', 'Le nom ne correspond pas.');

            return;
        }

        OrganizationAuditService::log(
            'organization.deleted',
            'organization',
            (int) $org->id,
            ['name' => $org->name],
        );

        $org->delete();
        session()->forget('current_organization_id');

        $this->redirectRoute('organizations.select');
    }

    // ─── Category CRUD ────────────────────────────────────────────────

    public function createCategory(): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $this->validate([
            'newCategoryName' => ['required', 'string', 'max:80'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $slug = Str::slug($this->newCategoryName);
        if ($slug === '') {
            $slug = 'cat-'.Str::lower(Str::random(6));
        }

        $baseSlug = $slug;
        $suffix = 2;
        while (TicketCategory::query()->where('organization_id', $orgId)->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        $category = TicketCategory::query()->create([
            'organization_id' => $orgId,
            'name' => trim($this->newCategoryName),
            'slug' => $slug,
            'is_active' => false,
            'default_ticket_group_id' => $this->newCategoryDefaultGroupId ?: null,
            'default_form_id' => $this->newCategoryDefaultFormId ?: null,
            'requires_approval' => $this->newCategoryRequiresApproval,
            'approval_type' => $this->newCategoryRequiresApproval ? $this->newCategoryApprovalType : null,
            'approval_user_id' => $this->newCategoryRequiresApproval && $this->newCategoryApprovalType === 'user' ? $this->newCategoryApprovalUserId : null,
            'approval_role' => $this->newCategoryRequiresApproval && $this->newCategoryApprovalType === 'role' ? $this->newCategoryApprovalRole : null,
        ]);

        $this->newCategoryName = '';
        $this->newCategoryDefaultGroupId = null;
        $this->newCategoryDefaultFormId = null;
        $this->newCategoryRequiresApproval = false;
        $this->newCategoryApprovalType = null;
        $this->newCategoryApprovalUserId = null;
        $this->newCategoryApprovalRole = null;
        CacheHelper::invalidateCategories($orgId);

        OrganizationAuditService::log(
            'settings.category_created',
            'ticket_category',
            (int) $category->id,
            ['name' => $category->name],
        );

        $this->dispatch('toast', type: 'success', message: 'Catégorie créée.');
    }

    public function startEditCategory(int $id): void
    {
        $orgId = $this->orgId();
        $cat = TicketCategory::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $this->editingCategoryId = (int) $cat->id;
        $this->editingCategoryName = (string) $cat->name;
        $this->editingCategoryDefaultGroupId = $cat->default_ticket_group_id;
        $this->editingCategoryDefaultFormId = $cat->default_form_id;
        $this->editingCategoryRequiresApproval = (bool) $cat->requires_approval;
        $this->editingCategoryApprovalType = $cat->approval_type;
        $this->editingCategoryApprovalUserId = $cat->approval_user_id;
        $this->editingCategoryApprovalRole = $cat->approval_role;
    }

    public function cancelEditCategory(): void
    {
        $this->editingCategoryId = null;
        $this->editingCategoryName = '';
        $this->editingCategoryDefaultGroupId = null;
        $this->editingCategoryDefaultFormId = null;
        $this->editingCategoryRequiresApproval = false;
        $this->editingCategoryApprovalType = null;
        $this->editingCategoryApprovalUserId = null;
        $this->editingCategoryApprovalRole = null;
    }

    public function updateCategory(): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $this->validate([
            'editingCategoryName' => ['required', 'string', 'max:80'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->editingCategoryId, 403);

        $cat = TicketCategory::query()
            ->where('organization_id', $orgId)
            ->whereKey((int) $this->editingCategoryId)
            ->firstOrFail();

        $slug = Str::slug($this->editingCategoryName);
        if ($slug === '') {
            $slug = 'cat-'.Str::lower(Str::random(6));
        }

        $baseSlug = $slug;
        $suffix = 2;
        while (
            TicketCategory::query()
                ->where('organization_id', $orgId)
                ->where('slug', $slug)
                ->where('id', '!=', $cat->id)
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        $cat->update([
            'name' => trim($this->editingCategoryName),
            'slug' => $slug,
            'default_ticket_group_id' => $this->editingCategoryDefaultGroupId ?: null,
            'default_form_id' => $this->editingCategoryDefaultFormId ?: null,
            'requires_approval' => $this->editingCategoryRequiresApproval,
            'approval_type' => $this->editingCategoryRequiresApproval ? $this->editingCategoryApprovalType : null,
            'approval_user_id' => $this->editingCategoryRequiresApproval && $this->editingCategoryApprovalType === 'user' ? $this->editingCategoryApprovalUserId : null,
            'approval_role' => $this->editingCategoryRequiresApproval && $this->editingCategoryApprovalType === 'role' ? $this->editingCategoryApprovalRole : null,
        ]);

        $this->editingCategoryId = null;
        $this->editingCategoryName = '';
        $this->editingCategoryDefaultGroupId = null;
        $this->editingCategoryDefaultFormId = null;
        $this->editingCategoryRequiresApproval = false;
        $this->editingCategoryApprovalType = null;
        $this->editingCategoryApprovalUserId = null;
        $this->editingCategoryApprovalRole = null;
        CacheHelper::invalidateCategories($orgId);
        $this->dispatch('toast', type: 'success', message: 'Catégorie mise à jour.');
    }

    public function toggleCategory(int $id): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $orgId = $this->orgId();
        $cat = TicketCategory::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $cat->update(['is_active' => ! $cat->is_active]);
        CacheHelper::invalidateCategories($orgId);
        $this->dispatch('toast', type: 'success', message: $cat->is_active ? 'Catégorie activée.' : 'Catégorie désactivée.');
    }

    public function deleteCategory(int $id): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $orgId = $this->orgId();
        $cat = TicketCategory::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        if ($cat->tickets()->exists()) {
            $this->dispatch('toast', type: 'error', message: 'Impossible de supprimer : des tickets utilisent cette catégorie.');

            return;
        }

        $deletedId = (int) $cat->id;
        $deletedName = (string) $cat->name;
        $cat->delete();

        if ($this->default_category_id === $deletedId) {
            $this->default_category_id = null;
            $org = $this->orgOrFail();
            $settings = is_array($org->settings) ? $org->settings : [];
            $settings['defaults']['ticket_category_id'] = null;
            $org->update(['settings' => $settings]);
        }

        CacheHelper::invalidateCategories($orgId);

        OrganizationAuditService::log(
            'settings.category_deleted',
            'ticket_category',
            $deletedId,
            ['name' => $deletedName],
        );

        $this->dispatch('toast', type: 'success', message: 'Catégorie supprimée.');
    }

    // ─── Priority CRUD ──────────────────────────────────────────────

    public function createPriority(): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $this->validate([
            'newPriorityName' => ['required', 'string', 'max:80'],
            'newPriorityLevel' => ['required', 'integer', 'min:0'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        if (TicketPriority::query()->where('organization_id', $orgId)->where('level', (int) $this->newPriorityLevel)->exists()) {
            $this->addError('newPriorityLevel', 'Ce niveau est déjà utilisé.');

            return;
        }

        $priority = TicketPriority::query()->create([
            'organization_id' => $orgId,
            'name' => trim($this->newPriorityName),
            'level' => (int) $this->newPriorityLevel,
            'is_active' => true,
        ]);

        $this->newPriorityName = '';
        $this->newPriorityLevel = null;
        CacheHelper::invalidatePriorities($orgId);

        OrganizationAuditService::log(
            'settings.priority_created',
            'ticket_priority',
            (int) $priority->id,
            ['name' => $priority->name, 'level' => $priority->level],
        );

        $this->dispatch('toast', type: 'success', message: 'Priorité créée.');
    }

    public function startEditPriority(int $id): void
    {
        $orgId = $this->orgId();
        $prio = TicketPriority::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $this->editingPriorityId = (int) $prio->id;
        $this->editingPriorityName = (string) $prio->name;
        $this->editingPriorityLevel = (int) $prio->level;
    }

    public function cancelEditPriority(): void
    {
        $this->editingPriorityId = null;
        $this->editingPriorityName = '';
        $this->editingPriorityLevel = null;
    }

    public function updatePriority(): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $this->validate([
            'editingPriorityName' => ['required', 'string', 'max:80'],
            'editingPriorityLevel' => ['required', 'integer', 'min:0'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->editingPriorityId, 403);

        $prio = TicketPriority::query()
            ->where('organization_id', $orgId)
            ->whereKey((int) $this->editingPriorityId)
            ->firstOrFail();

        if (
            TicketPriority::query()
                ->where('organization_id', $orgId)
                ->where('level', (int) $this->editingPriorityLevel)
                ->where('id', '!=', $prio->id)
                ->exists()
        ) {
            $this->addError('editingPriorityLevel', 'Ce niveau est déjà utilisé par une autre priorité.');

            return;
        }

        $prio->update([
            'name' => trim($this->editingPriorityName),
            'level' => (int) $this->editingPriorityLevel,
        ]);

        $this->editingPriorityId = null;
        $this->editingPriorityName = '';
        $this->editingPriorityLevel = null;
        CacheHelper::invalidatePriorities($orgId);
        $this->dispatch('toast', type: 'success', message: 'Priorité mise à jour.');
    }

    public function togglePriority(int $id): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $orgId = $this->orgId();
        $prio = TicketPriority::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $prio->update(['is_active' => ! $prio->is_active]);
        CacheHelper::invalidatePriorities($orgId);
        $this->dispatch('toast', type: 'success', message: $prio->is_active ? 'Priorité activée.' : 'Priorité désactivée.');
    }

    public function deletePriority(int $id): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $orgId = $this->orgId();
        $prio = TicketPriority::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        if ($prio->tickets()->exists()) {
            $this->dispatch('toast', type: 'error', message: 'Impossible de supprimer : des tickets utilisent cette priorité.');

            return;
        }

        $deletedId = (int) $prio->id;
        $deletedName = (string) $prio->name;
        $prio->delete();

        if ($this->default_priority_id === $deletedId) {
            $this->default_priority_id = null;
            $org = $this->orgOrFail();
            $settings = is_array($org->settings) ? $org->settings : [];
            $settings['defaults']['ticket_priority_id'] = null;
            $org->update(['settings' => $settings]);
        }

        CacheHelper::invalidatePriorities($orgId);

        OrganizationAuditService::log(
            'settings.priority_deleted',
            'ticket_priority',
            $deletedId,
            ['name' => $deletedName],
        );

        $this->dispatch('toast', type: 'success', message: 'Priorité supprimée.');
    }

    // ─── Role CRUD ────────────────────────────────────────────────

    public function createRole(): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $this->validate([
            'newRoleName' => ['required', 'string', 'max:80'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $slug = Str::slug($this->newRoleName);
        if ($slug === '') {
            $slug = 'role-'.Str::lower(Str::random(6));
        }

        $baseSlug = $slug;
        $suffix = 2;
        while (RoleDefinition::query()->where('organization_id', $orgId)->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        RoleDefinition::query()->create([
            'organization_id' => $orgId,
            'name' => trim($this->newRoleName),
            'slug' => $slug,
            'is_default' => false,
        ]);

        // Clone permissions from base role if specified
        if ($this->newRoleBaseSlug !== '') {
            $basePerms = OrganizationRolePermission::query()
                ->where('organization_id', $orgId)
                ->where('role', $this->newRoleBaseSlug)
                ->pluck('permission');

            $now = now();
            $rows = $basePerms->map(fn (string $p) => [
                'organization_id' => $orgId,
                'role' => $slug,
                'permission' => $p,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            if (! empty($rows)) {
                OrganizationRolePermission::query()->insert($rows);
            }
        }

        $createdRoleName = trim($this->newRoleName);
        $this->newRoleName = '';
        $this->newRoleBaseSlug = '';
        $this->loadRolePermissions();
        CacheHelper::invalidateRolePermissions($orgId);
        CacheHelper::invalidateSettingsRoles($orgId);

        OrganizationAuditService::log(
            'settings.role_created',
            'role_definition',
            null,
            ['name' => $createdRoleName, 'slug' => $slug],
        );

        $this->dispatch('toast', type: 'success', message: __('settings.role_created'));
    }

    public function startEditRole(int $id): void
    {
        $orgId = $this->orgId();
        $role = RoleDefinition::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $this->editingRoleId = (int) $role->id;
        $this->editingRoleName = (string) $role->name;
    }

    public function cancelEditRole(): void
    {
        $this->editingRoleId = null;
        $this->editingRoleName = '';
    }

    public function updateRoleName(): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $this->validate([
            'editingRoleName' => ['required', 'string', 'max:80'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->editingRoleId, 403);

        $role = RoleDefinition::query()
            ->where('organization_id', $orgId)
            ->whereKey((int) $this->editingRoleId)
            ->firstOrFail();

        if ($role->is_default) {
            // Default roles: only rename display name, keep slug
            $role->update(['name' => trim($this->editingRoleName)]);
        } else {
            // Custom roles: rename name + slug + update memberships.role and permissions.role
            $newSlug = Str::slug($this->editingRoleName);
            if ($newSlug === '') {
                $newSlug = 'role-'.Str::lower(Str::random(6));
            }

            $baseSlug = $newSlug;
            $suffix = 2;
            while (
                RoleDefinition::query()
                    ->where('organization_id', $orgId)
                    ->where('slug', $newSlug)
                    ->where('id', '!=', $role->id)
                    ->exists()
            ) {
                $newSlug = $baseSlug.'-'.$suffix;
                $suffix++;
            }

            $oldSlug = $role->slug;

            DB::transaction(function () use ($role, $orgId, $oldSlug, $newSlug) {
                $role->update([
                    'name' => trim($this->editingRoleName),
                    'slug' => $newSlug,
                ]);

                OrganizationMembership::query()
                    ->where('organization_id', $orgId)
                    ->where('role', $oldSlug)
                    ->update(['role' => $newSlug]);

                OrganizationRolePermission::query()
                    ->where('organization_id', $orgId)
                    ->where('role', $oldSlug)
                    ->update(['role' => $newSlug]);
            });

            // Update selectedRole if it was the renamed one
            if ($this->selectedRole === $oldSlug) {
                $this->selectedRole = $newSlug;
            }
        }

        $this->editingRoleId = null;
        $this->editingRoleName = '';
        $this->loadRolePermissions();
        CacheHelper::invalidateRolePermissions($orgId);
        CacheHelper::invalidateSettingsRoles($orgId);
        $this->dispatch('toast', type: 'success', message: __('settings.role_updated'));
    }

    public function deleteRole(int $id): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $orgId = $this->orgId();
        $role = RoleDefinition::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        if ($role->is_default) {
            $this->dispatch('toast', type: 'error', message: __('settings.role_cannot_delete_default'));

            return;
        }

        if ($role->memberCount() > 0) {
            $this->dispatch('toast', type: 'error', message: __('settings.role_cannot_delete_members'));

            return;
        }

        $deletedSlug = $role->slug;
        $deletedName = (string) $role->name;

        OrganizationRolePermission::query()
            ->where('organization_id', $orgId)
            ->where('role', $deletedSlug)
            ->delete();

        $role->delete();

        if ($this->selectedRole === $deletedSlug) {
            $this->selectedRole = 'admin';
        }

        $this->loadRolePermissions();
        CacheHelper::invalidateRolePermissions($orgId);
        CacheHelper::invalidateSettingsRoles($orgId);

        OrganizationAuditService::log(
            'settings.role_deleted',
            'role_definition',
            $id,
            ['name' => $deletedName, 'slug' => $deletedSlug],
        );

        $this->dispatch('toast', type: 'success', message: __('settings.role_deleted'));
    }

    // ─── Email Mailbox ────────────────────────────────────────────────

    public function saveMailbox(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageEmail)) {
            $this->addError('mailboxEmail', __('Accès refusé.'));

            return;
        }

        $orgId = $this->orgId();
        if (! $orgId) {
            $this->addError('mailboxEmail', __('Aucune organisation trouvée. Veuillez vous reconnecter.'));

            return;
        }

        $rules = [
            'mailboxEmail' => ['required', 'email', 'max:255'],
            'mailboxDisplayName' => ['nullable', 'string', 'max:120'],
            'mailboxImapHost' => ['required', 'string', 'max:255'],
            'mailboxImapPort' => ['required', 'integer', 'min:1', 'max:65535'],
            'mailboxImapUsername' => ['required', 'string', 'max:255'],
            'mailboxImapEncryption' => ['required', 'in:ssl,tls,none'],
            'mailboxImapFolder' => ['required', 'string', 'max:120'],
            'mailboxDefaultCategoryId' => ['nullable', 'integer'],
            'mailboxDefaultPriorityId' => ['nullable', 'integer'],
            'mailboxDefaultGroupId' => ['nullable', 'integer'],
            'mailboxSmtpHost' => ['nullable', 'string', 'max:255'],
            'mailboxSmtpPort' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'mailboxSmtpUsername' => ['nullable', 'string', 'max:255'],
            'mailboxSmtpPassword' => ['nullable', 'string', 'max:500'],
            'mailboxSmtpEncryption' => ['nullable', 'in:ssl,tls,none'],
        ];

        // Password required only when creating new mailbox
        $existing = OrganizationMailbox::query()->where('organization_id', $orgId)->first();
        if (! $existing) {
            $rules['mailboxImapPassword'] = ['required', 'string', 'max:500'];
        } else {
            $rules['mailboxImapPassword'] = ['nullable', 'string', 'max:500'];
        }

        $this->validate($rules);

        $data = [
            'organization_id' => $orgId,
            'email' => $this->mailboxEmail,
            'display_name' => $this->mailboxDisplayName ?: null,
            'imap_host' => $this->mailboxImapHost,
            'imap_port' => $this->mailboxImapPort,
            'imap_username' => $this->mailboxImapUsername,
            'imap_encryption' => $this->mailboxImapEncryption,
            'imap_folder' => $this->mailboxImapFolder ?: 'INBOX',
            'default_category_id' => $this->mailboxDefaultCategoryId ?: null,
            'default_priority_id' => $this->mailboxDefaultPriorityId ?: null,
            'default_group_id' => $this->mailboxDefaultGroupId ?: null,
        ];

        if ($this->mailboxImapPassword !== '') {
            $data['imap_password'] = $this->mailboxImapPassword;
        }

        // SMTP fields
        $data['smtp_host'] = $this->mailboxSmtpHost ?: null;
        $data['smtp_port'] = $this->mailboxSmtpPort ?: 587;
        $data['smtp_username'] = $this->mailboxSmtpUsername ?: null;
        $data['smtp_encryption'] = $this->mailboxSmtpEncryption ?: 'tls';

        if ($this->mailboxSmtpPassword !== '') {
            $data['smtp_password'] = $this->mailboxSmtpPassword;
        }

        if ($existing) {
            $existing->update($data);
        } else {
            OrganizationMailbox::query()->create($data);
        }

        $this->mailboxImapPassword = '';
        $this->mailboxSmtpPassword = '';

        OrganizationAuditService::log(
            'settings.email_mailbox_updated',
            'organization_mailbox',
            null,
            ['email' => $this->mailboxEmail],
        );

        $this->dispatch('toast', type: 'success', message: __('Configuration email enregistrée.'));
    }

    public function testMailboxConnection(): void
    {
        $this->mailboxTestResult = null;

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageEmail)) {
            $this->mailboxTestResult = 'error:Accès refusé.';

            return;
        }

        $password = $this->mailboxImapPassword;
        if ($password === '') {
            // Use stored password
            $orgId = $this->orgId();
            $existing = OrganizationMailbox::query()->where('organization_id', $orgId)->first();
            $password = $existing?->imap_password;
        }

        if (! $password) {
            $this->mailboxTestResult = 'error:Mot de passe requis.';

            return;
        }

        $result = ImapService::testConnection(
            host: $this->mailboxImapHost,
            port: $this->mailboxImapPort,
            username: $this->mailboxImapUsername,
            password: $password,
            encryption: $this->mailboxImapEncryption,
            folder: $this->mailboxImapFolder ?: 'INBOX',
        );

        if ($result === true) {
            $this->mailboxTestResult = 'success';
            $this->dispatch('toast', type: 'success', message: __('Connexion IMAP réussie !'));
        } else {
            $friendly = MailErrorTranslator::translate($result, 'IMAP', $this->mailboxImapPort);
            $this->mailboxTestResult = 'error:'.$friendly;
            $this->dispatch('toast', type: 'error', message: $friendly);
        }
    }

    public function testSmtpConnection(): void
    {
        $this->mailboxSmtpTestResult = null;

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageEmail)) {
            $this->mailboxSmtpTestResult = 'error:Accès refusé.';

            return;
        }

        if (! $this->mailboxSmtpHost) {
            $this->mailboxSmtpTestResult = 'error:Hôte SMTP requis.';

            return;
        }

        // Fallback: use IMAP credentials if SMTP username/password not set
        $username = $this->mailboxSmtpUsername ?: $this->mailboxImapUsername;
        $password = $this->mailboxSmtpPassword;
        if ($password === '') {
            $orgId = $this->orgId();
            $existing = OrganizationMailbox::query()->where('organization_id', $orgId)->first();
            // Try SMTP password first, then IMAP password
            $password = $existing?->smtp_password ?: $existing?->imap_password;
        }

        if (! $username || ! $password) {
            $this->mailboxSmtpTestResult = 'error:Identifiant et mot de passe requis (SMTP ou IMAP).';

            return;
        }

        try {
            $tls = match ($this->mailboxSmtpEncryption) {
                'ssl', 'tls' => true,
                default => false,
            };

            $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
                host: $this->mailboxSmtpHost,
                port: $this->mailboxSmtpPort ?: 587,
                tls: $tls,
            );

            $transport->setUsername($username);
            $transport->setPassword($password);

            // Test the connection by starting and stopping
            $transport->start();
            $transport->stop();

            $this->mailboxSmtpTestResult = 'success';
            $this->dispatch('toast', type: 'success', message: __('Connexion SMTP réussie !'));
        } catch (\Throwable $e) {
            $friendly = MailErrorTranslator::translate($e->getMessage(), 'SMTP', $this->mailboxSmtpPort ?: 587);
            $this->mailboxSmtpTestResult = 'error:'.$friendly;
            $this->dispatch('toast', type: 'error', message: $friendly);
        }
    }

    public function fetchMailboxNow(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageEmail)) {
            $this->dispatch('toast', type: 'error', message: __('Accès refusé.'));

            return;
        }

        $orgId = $this->orgId();
        $mailbox = OrganizationMailbox::query()
            ->where('organization_id', $orgId)
            ->where('is_active', true)
            ->first();

        if (! $mailbox) {
            $this->dispatch('toast', type: 'error', message: __('Aucune boîte mail active.'));

            return;
        }

        try {
            $messages = ImapService::fetchNewMessages($mailbox, 50);
            $count = count($messages);

            if ($count === 0) {
                $this->dispatch('toast', type: 'info', message: __('Aucun nouveau message.'));

                return;
            }

            $service = new \App\Services\Email\InboundEmailService;
            $parser = \App\Services\Email\EmailParser::class;
            $processed = 0;
            $maxUid = $mailbox->last_fetched_uid;

            foreach ($messages as $imapMessage) {
                try {
                    $parsed = $parser::parse($imapMessage);
                    $service->process($parsed, $mailbox);
                    $uid = $parsed->uid;
                    if ($uid && $uid > $maxUid) {
                        $maxUid = $uid;
                    }
                    $processed++;
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Manual email fetch: failed to process', [
                        'mailbox_id' => $mailbox->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $mailbox->update([
                'last_fetched_at' => now(),
                'last_fetched_uid' => $maxUid,
                'last_error_at' => null,
                'last_error_message' => null,
            ]);

            $this->mailboxLastFetchedAt = now()->translatedFormat('d/m/Y H:i');
            $this->mailboxLastError = null;

            $this->dispatch('toast', type: 'success', message: __(':count email(s) récupéré(s) et traité(s).', ['count' => $processed]));
        } catch (\Throwable $e) {
            $friendly = MailErrorTranslator::translate($e->getMessage(), 'IMAP', $mailbox->imap_port);
            $mailbox->update([
                'last_error_at' => now(),
                'last_error_message' => $friendly,
            ]);
            $this->mailboxLastError = $friendly;

            $this->dispatch('toast', type: 'error', message: $friendly);
        }
    }

    public function toggleMailbox(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageEmail)) {
            return;
        }

        $orgId = $this->orgId();
        $mailbox = OrganizationMailbox::query()->where('organization_id', $orgId)->first();
        if (! $mailbox) {
            $this->dispatch('toast', type: 'error', message: __('Veuillez d\'abord configurer la boîte mail.'));

            return;
        }

        $mailbox->update(['is_active' => ! $mailbox->is_active]);
        $this->mailboxIsActive = (bool) $mailbox->is_active;

        $this->dispatch('toast', type: 'success', message: $this->mailboxIsActive
            ? __('Réception email activée.')
            : __('Réception email désactivée.'));
    }

    // ─── Knowledge Base ─────────────────────────────────────────────

    public function addKbCategory(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_if(! $user?->hasPermission(Permission::SettingsManageKnowledgeBase), 403);

        $this->validate([
            'newKbCategoryName' => ['required', 'string', 'max:120'],
            'newKbCategoryDescription' => ['nullable', 'string', 'max:500'],
            'newKbCategoryIcon' => ['nullable', 'string', 'max:80'],
        ]);

        $orgId = $this->orgId();
        $maxSort = KbCategory::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->max('sort_order') ?? 0;

        KbCategory::create([
            'organization_id' => $orgId,
            'name' => $this->newKbCategoryName,
            'slug' => Str::slug($this->newKbCategoryName),
            'description' => $this->newKbCategoryDescription ?: null,
            'icon' => $this->newKbCategoryIcon ?: null,
            'sort_order' => $maxSort + 1,
        ]);

        $this->reset(['newKbCategoryName', 'newKbCategoryDescription', 'newKbCategoryIcon']);
        CacheHelper::invalidateKnowledgeBase($orgId);
        $this->dispatch('toast', type: 'success', message: __('Catégorie KB créée.'));
    }

    public function editKbCategory(int $id): void
    {
        $orgId = $this->orgId();
        $cat = KbCategory::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->findOrFail($id);

        $this->editingKbCategoryId = $cat->id;
        $this->editingKbCategoryName = (string) $cat->name;
        $this->editingKbCategoryDescription = (string) ($cat->description ?? '');
        $this->editingKbCategoryIcon = (string) ($cat->icon ?? '');
    }

    public function updateKbCategory(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_if(! $user?->hasPermission(Permission::SettingsManageKnowledgeBase), 403);

        $this->validate([
            'editingKbCategoryName' => ['required', 'string', 'max:120'],
            'editingKbCategoryDescription' => ['nullable', 'string', 'max:500'],
            'editingKbCategoryIcon' => ['nullable', 'string', 'max:80'],
        ]);

        $orgId = $this->orgId();
        $cat = KbCategory::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->findOrFail($this->editingKbCategoryId);

        $cat->update([
            'name' => $this->editingKbCategoryName,
            'slug' => Str::slug($this->editingKbCategoryName),
            'description' => $this->editingKbCategoryDescription ?: null,
            'icon' => $this->editingKbCategoryIcon ?: null,
        ]);

        $this->editingKbCategoryId = null;
        CacheHelper::invalidateKnowledgeBase($orgId);
        $this->dispatch('toast', type: 'success', message: __('Catégorie KB mise à jour.'));
    }

    public function deleteKbCategory(int $id): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_if(! $user?->hasPermission(Permission::SettingsManageKnowledgeBase), 403);

        $orgId = $this->orgId();
        KbCategory::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->where('id', $id)
            ->delete();

        CacheHelper::invalidateKnowledgeBase($orgId);
        $this->dispatch('toast', type: 'success', message: __('Catégorie KB supprimée.'));
    }

    public function toggleKbCategory(int $id): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_if(! $user?->hasPermission(Permission::SettingsManageKnowledgeBase), 403);

        $orgId = $this->orgId();
        $cat = KbCategory::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->findOrFail($id);

        $cat->update(['is_active' => ! $cat->is_active]);

        CacheHelper::invalidateKnowledgeBase($orgId);
        $this->dispatch('toast', type: 'success', message: $cat->is_active
            ? __('Catégorie activée.')
            : __('Catégorie désactivée.'));
    }

    public function openCreateArticle(): void
    {
        $this->reset(['editingKbArticleId', 'kbArticleTitle', 'kbArticleCategoryId', 'kbArticleContent', 'kbArticleStatus', 'kbArticleVisibility', 'kbArticleKeywords']);
        $this->kbArticleStatus = 'published';
        $this->kbArticleVisibility = 'public';
        $this->showKbArticleModal = true;
    }

    public function openEditArticle(int $id): void
    {
        $orgId = $this->orgId();
        $article = KbArticle::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->findOrFail($id);

        $this->editingKbArticleId = $article->id;
        $this->kbArticleTitle = (string) $article->title;
        $this->kbArticleCategoryId = $article->kb_category_id;
        $this->kbArticleContent = (string) $article->content;
        $this->kbArticleStatus = (string) $article->status;
        $this->kbArticleVisibility = (string) $article->visibility;
        $this->kbArticleKeywords = implode(', ', $article->keywords ?? []);
        $this->showKbArticleModal = true;
    }

    public function saveArticle(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_if(! $user?->hasPermission(Permission::SettingsManageKnowledgeBase), 403);

        $this->validate([
            'kbArticleTitle' => ['required', 'string', 'max:255'],
            'kbArticleCategoryId' => ['required', 'integer', 'exists:kb_categories,id'],
            'kbArticleContent' => ['required', 'string'],
            'kbArticleStatus' => ['required', 'in:draft,published'],
            'kbArticleVisibility' => ['required', 'in:public,internal'],
            'kbArticleKeywords' => ['nullable', 'string', 'max:500'],
        ]);

        $orgId = $this->orgId();

        $allowedTags = '<b><strong><i><em><u><br><p><ul><ol><li><h2><h3><h4><blockquote><a><hr>';
        $cleanContent = strip_tags($this->kbArticleContent, $allowedTags);

        $data = [
            'organization_id' => $orgId,
            'kb_category_id' => $this->kbArticleCategoryId,
            'title' => $this->kbArticleTitle,
            'slug' => Str::slug($this->kbArticleTitle),
            'content' => $cleanContent,
            'status' => $this->kbArticleStatus,
            'visibility' => $this->kbArticleVisibility,
            'keywords' => array_values(array_filter(array_map('trim', explode(',', $this->kbArticleKeywords)))),
            'updated_by' => $user->id,
        ];

        if ($this->kbArticleStatus === 'published') {
            $data['published_at'] = now();
        }

        if ($this->editingKbArticleId) {
            $article = KbArticle::withoutOrganizationScope()
                ->where('organization_id', $orgId)
                ->findOrFail($this->editingKbArticleId);
            $article->update($data);
        } else {
            $data['created_by'] = $user->id;
            KbArticle::create($data);
        }

        $this->showKbArticleModal = false;
        CacheHelper::invalidateKnowledgeBase($orgId);
        $this->dispatch('toast', type: 'success', message: $this->editingKbArticleId
            ? __('Article mis à jour.')
            : __('Article créé.'));
    }

    public function deleteArticle(int $id): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_if(! $user?->hasPermission(Permission::SettingsManageKnowledgeBase), 403);

        $orgId = $this->orgId();
        KbArticle::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->where('id', $id)
            ->delete();

        CacheHelper::invalidateKnowledgeBase($orgId);
        $this->dispatch('toast', type: 'success', message: __('Article supprimé.'));
    }

    public function toggleArticle(int $id): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_if(! $user?->hasPermission(Permission::SettingsManageKnowledgeBase), 403);

        $orgId = $this->orgId();
        $article = KbArticle::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->findOrFail($id);

        $article->update(['is_active' => ! $article->is_active]);

        CacheHelper::invalidateKnowledgeBase($orgId);
        $this->dispatch('toast', type: 'success', message: $article->is_active
            ? __('Article activé.')
            : __('Article désactivé.'));
    }

    public function render(): View
    {
        return view('livewire.admin.settings', $this->buildSettingsViewData());
    }

    /**
     * Données passées à la vue Paramètres (logique extraite pour l’analyse statique / IDE).
     *
     * @return array<string, mixed>
     */
    private function buildSettingsViewData(): array
    {
        $orgId = $this->orgId();
        $tab = $this->activeTab;
        $needs = static fn (array $tabs) => in_array($tab, $tabs, true);

        if ($this->loadStage < 2) {
            $org = null;
            if ($orgId) {
                $org = request()->attributes->get('currentOrganization');
                if (! $org instanceof Organization) {
                    $org = Organization::query()->find($orgId);
                }
            }

            return [
                'org' => $org,
                'categories' => collect(),
                'priorities' => collect(),
                'organizationFunctions' => collect(),
                'ticketGroups' => collect(),
                'forms' => collect(),
                'members' => collect(),
                'roles' => collect(),
                'roleMemberCounts' => [],
                'automationRules' => collect(),
                'maintenanceStats' => [],
                'apiTokens' => collect(),
                'webhookEndpoints' => collect(),
                'kbCategories' => collect(),
                'kbArticles' => collect(),
            ];
        }

        $org = null;
        if ($orgId) {
            $org = request()->attributes->get('currentOrganization');
            if (! $org instanceof Organization) {
                $org = Organization::query()->find($orgId);
            }
        }

        $categories = ($orgId && $needs(['tickets', 'categories', 'email', 'automations', 'sla']))
            ? Cache::remember(CacheHelper::categoriesKey($orgId, false), CacheHelper::TTL, function () use ($orgId) {
                return TicketCategory::query()
                    ->where('organization_id', $orgId)
                    ->orderBy('name')
                    ->get(['id', 'name', 'slug', 'is_active', 'default_ticket_group_id', 'default_form_id', 'requires_approval', 'approval_type', 'approval_user_id', 'approval_role']);
            })
            : collect();

        $priorities = ($orgId && $needs(['tickets', 'priorities', 'email', 'automations', 'sla']))
            ? Cache::remember(CacheHelper::prioritiesKey($orgId, false), CacheHelper::TTL, function () use ($orgId) {
                return TicketPriority::query()
                    ->where('organization_id', $orgId)
                    ->orderByDesc('level')
                    ->get(['id', 'name', 'level', 'is_active']);
            })
            : collect();

        $members = ($orgId && $needs(['categories', 'automations', 'roles', 'maintenance']))
            ? Cache::remember(CacheHelper::settingsMembersListKey($orgId), 300, fn () => OrganizationMembership::query()
                ->where('organization_id', $orgId)
                ->with(['user:id,name,email'])
                ->orderBy('id')
                ->get(['id', 'organization_id', 'user_id', 'role'])
            )
            : collect();

        $organizationFunctions = ($orgId && $needs(['functions']))
            ? Cache::remember(CacheHelper::orgFunctionsKey($orgId), CacheHelper::TTL, function () use ($orgId) {
                return OrganizationFunction::query()
                    ->where('organization_id', $orgId)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(['id', 'name', 'sort_order']);
            })
            : collect();

        $ticketGroups = ($orgId && $needs(['groups', 'categories', 'email', 'automations']))
            ? Cache::remember(CacheHelper::ticketGroupsKey($orgId, false), CacheHelper::TTL, function () use ($orgId) {
                return TicketGroup::query()
                    ->where('organization_id', $orgId)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(['id', 'name', 'slug', 'color', 'is_active', 'sort_order']);
            })
            : collect();

        $roles = ($orgId && $needs(['categories', 'roles', 'maintenance']))
            ? Cache::remember(CacheHelper::settingsRolesListKey($orgId), 300, fn () => RoleDefinition::query()
                ->where('organization_id', $orgId)
                ->orderByRaw("case slug when 'owner' then 0 when 'admin' then 1 when 'agent' then 2 when 'member' then 3 else 4 end")
                ->get()
            )
            : collect();

        $roleMemberCounts = [];
        if ($orgId && $needs(['roles', 'maintenance'])) {
            $roleMemberCounts = Cache::remember(CacheHelper::settingsRoleCountsKey($orgId), 120, function () use ($orgId, $roles) {
                $counts = OrganizationMembership::query()
                    ->where('organization_id', $orgId)
                    ->selectRaw('role, count(*) as cnt')
                    ->groupBy('role')
                    ->pluck('cnt', 'role');
                $result = [];
                foreach ($roles as $r) {
                    $result[$r->slug] = (int) ($counts[$r->slug] ?? 0);
                }

                return $result;
            });
        }

        $forms = ($orgId && $needs(['categories']))
            ? Cache::remember(CacheHelper::settingsPublishedFormsKey($orgId), CacheHelper::TTL, function () use ($orgId) {
                return Form::query()
                    ->where('organization_id', $orgId)
                    ->where('status', \App\Enums\FormStatus::Published)
                    ->orderBy('name')
                    ->get(['id', 'name']);
            })
            : collect();

        $automationRules = collect();
        if ($orgId && $needs(['automations'])) {
            try {
                $automationRules = Cache::remember(CacheHelper::settingsAutomationRulesListKey($orgId), CacheHelper::TTL, function () use ($orgId) {
                    return AutomationRule::withoutOrganizationScope()
                        ->where('organization_id', $orgId)
                        ->orderBy('sort_order')
                        ->get();
                });
            } catch (\Throwable) {
                // Table not yet migrated
            }
        }

        $maintenanceStats = ($orgId && $needs(['maintenance'])) ? Cache::remember(CacheHelper::settingsMaintenanceStatsKey($orgId), 300, function () use ($orgId) {
            return [
                'members' => OrganizationMembership::query()->where('organization_id', $orgId)->count(),
                'tickets' => Ticket::query()->where('organization_id', $orgId)->count(),
                'forms' => Form::query()->where('organization_id', $orgId)->count(),
                'categories' => TicketCategory::query()->where('organization_id', $orgId)->count(),
                'priorities' => TicketPriority::query()->where('organization_id', $orgId)->count(),
                'roles' => RoleDefinition::query()->where('organization_id', $orgId)->count(),
            ];
        }) : [];

        $apiTokens = collect();
        if ($orgId && $needs(['api'])) {
            try {
                $apiTokens = Cache::remember(CacheHelper::settingsApiTokensKey($orgId), CacheHelper::TTL, function () use ($orgId) {
                    return \Laravel\Sanctum\PersonalAccessToken::query()
                        ->where('organization_id', $orgId)
                        ->orderByDesc('created_at')
                        ->get();
                });
            } catch (\Throwable) {
                // Table not yet migrated
            }
        }

        $kbCategories = collect();
        $kbArticles = collect();
        if ($orgId && $needs(['knowledge_base'])) {
            try {
                $kbCategories = Cache::remember(CacheHelper::settingsKbCategoriesAdminKey($orgId), CacheHelper::TTL, function () use ($orgId) {
                    return KbCategory::withoutOrganizationScope()
                        ->where('organization_id', $orgId)
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->get();
                });
                $kbArticles = Cache::remember(CacheHelper::settingsKbArticlesAdminKey($orgId), CacheHelper::TTL, function () use ($orgId) {
                    return KbArticle::withoutOrganizationScope()
                        ->where('organization_id', $orgId)
                        ->with('category')
                        ->orderByDesc('updated_at')
                        ->get();
                });
            } catch (\Throwable) {
                // Table not yet migrated
            }
        }

        $webhookEndpoints = collect();
        if ($orgId && $needs(['webhooks'])) {
            try {
                $webhookEndpoints = Cache::remember(CacheHelper::settingsWebhookEndpointsKey($orgId), CacheHelper::TTL, function () use ($orgId) {
                    return WebhookEndpoint::query()
                        ->where('organization_id', $orgId)
                        ->orderByDesc('created_at')
                        ->get();
                });
            } catch (\Throwable) {
                // Table not yet migrated
            }
        }

        return [
            'org' => $org,
            'categories' => $categories,
            'priorities' => $priorities,
            'organizationFunctions' => $organizationFunctions,
            'ticketGroups' => $ticketGroups,
            'forms' => $forms,
            'members' => $members,
            'roles' => $roles,
            'roleMemberCounts' => $roleMemberCounts,
            'automationRules' => $automationRules,
            'maintenanceStats' => $maintenanceStats,
            'apiTokens' => $apiTokens,
            'webhookEndpoints' => $webhookEndpoints,
            'kbCategories' => $kbCategories,
            'kbArticles' => $kbArticles,
        ];
    }

    // ─── Maintenance ──────────────────────────────────────────────────

    public function clearOrganizationCache(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageBranding)) {
            $this->dispatch('toast', type: 'error', message: __('Accès refusé.'));

            return;
        }

        $orgId = $this->orgId();
        if (! $orgId) {
            return;
        }

        CacheHelper::invalidateAll($orgId);

        // Also invalidate per-user caches for all members
        $memberIds = OrganizationMembership::query()
            ->where('organization_id', $orgId)
            ->pluck('user_id');

        foreach ($memberIds as $userId) {
            CacheHelper::invalidateNotificationsCount((int) $userId);
            CacheHelper::invalidateSidebarDiscussionsUnread((int) $userId);
            CacheHelper::invalidatePendingForms($orgId, (int) $userId);
        }

        $this->dispatch('toast', type: 'success', message: __('settings.cache_cleared'));
    }

    public function clearViewCache(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->canPlatformAdminister()) {
            $this->dispatch('toast', type: 'error', message: __('Accès refusé.'));

            return;
        }

        \Illuminate\Support\Facades\Artisan::call('view:clear');
        $this->dispatch('toast', type: 'success', message: __('settings.view_cache_cleared'));
    }

    // ─── Group CRUD ────────────────────────────────────────────────────

    public function createGroup(): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $this->validate([
            'newGroupName' => ['required', 'string', 'max:120'],
            'newGroupColor' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $slug = Str::slug($this->newGroupName);
        if ($slug === '') {
            $slug = 'grp-'.Str::lower(Str::random(6));
        }

        $baseSlug = $slug;
        $suffix = 2;
        while (TicketGroup::query()->where('organization_id', $orgId)->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        $sortOrder = (int) TicketGroup::query()->where('organization_id', $orgId)->max('sort_order') + 1;

        $group = TicketGroup::query()->create([
            'organization_id' => $orgId,
            'name' => trim($this->newGroupName),
            'slug' => $slug,
            'color' => $this->newGroupColor ?: null,
            'is_active' => true,
            'sort_order' => $sortOrder,
        ]);

        $this->newGroupName = '';
        $this->newGroupColor = null;
        CacheHelper::invalidateTicketGroups($orgId);

        OrganizationAuditService::log(
            'settings.group_created',
            'ticket_group',
            (int) $group->id,
            ['name' => $group->name],
        );

        $this->dispatch('toast', type: 'success', message: __('Groupe créé.'));
    }

    public function startEditGroup(int $id): void
    {
        $orgId = $this->orgId();
        $group = TicketGroup::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $this->editingGroupId = (int) $group->id;
        $this->editingGroupName = (string) $group->name;
        $this->editingGroupColor = $group->color;
    }

    public function cancelEditGroup(): void
    {
        $this->editingGroupId = null;
        $this->editingGroupName = '';
        $this->editingGroupColor = null;
    }

    public function updateGroup(): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $this->validate([
            'editingGroupName' => ['required', 'string', 'max:120'],
            'editingGroupColor' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->editingGroupId, 403);

        $group = TicketGroup::query()
            ->where('organization_id', $orgId)
            ->whereKey((int) $this->editingGroupId)
            ->firstOrFail();

        $slug = Str::slug($this->editingGroupName);
        if ($slug === '') {
            $slug = 'grp-'.Str::lower(Str::random(6));
        }

        $baseSlug = $slug;
        $suffix = 2;
        while (
            TicketGroup::query()
                ->where('organization_id', $orgId)
                ->where('slug', $slug)
                ->where('id', '!=', $group->id)
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        $group->update([
            'name' => trim($this->editingGroupName),
            'slug' => $slug,
            'color' => $this->editingGroupColor ?: null,
        ]);

        $this->editingGroupId = null;
        $this->editingGroupName = '';
        $this->editingGroupColor = null;
        CacheHelper::invalidateTicketGroups($orgId);
        $this->dispatch('toast', type: 'success', message: __('Groupe mis à jour.'));
    }

    public function toggleGroup(int $id): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $orgId = $this->orgId();
        $group = TicketGroup::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $group->update(['is_active' => ! $group->is_active]);
        CacheHelper::invalidateTicketGroups($orgId);
        $this->dispatch('toast', type: 'success', message: $group->is_active ? __('Groupe activé.') : __('Groupe désactivé.'));
    }

    public function deleteGroup(int $id): void
    {
        if (! $this->canManage) {
            abort(403);
        }

        $orgId = $this->orgId();
        $group = TicketGroup::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        // Unlink tickets from this group (don't delete them)
        Ticket::query()
            ->where('organization_id', $orgId)
            ->where('ticket_group_id', $group->id)
            ->update(['ticket_group_id' => null]);

        $deletedId = (int) $group->id;
        $deletedName = (string) $group->name;
        $group->delete();
        CacheHelper::invalidateTicketGroups($orgId);

        OrganizationAuditService::log(
            'settings.group_deleted',
            'ticket_group',
            $deletedId,
            ['name' => $deletedName],
        );

        $this->dispatch('toast', type: 'success', message: __('Groupe supprimé.'));
    }

    // ─── Function (fonction métier) CRUD ───────────────────────────────────

    public function createFunction(): void
    {
        if (! $this->canManage) {
            abort(403);
        }
        $this->validate(['newFunctionName' => ['required', 'string', 'max:80']]);
        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        OrganizationFunction::query()->create([
            'organization_id' => $orgId,
            'name' => trim($this->newFunctionName),
            'sort_order' => OrganizationFunction::query()->where('organization_id', $orgId)->max('sort_order') + 1,
        ]);
        $this->newFunctionName = '';
        CacheHelper::invalidateOrgFunctions($orgId);
        $this->dispatch('toast', type: 'success', message: __('Fonction créée.'));
    }

    public function startEditFunction(int $id): void
    {
        $orgId = $this->orgId();
        $fn = OrganizationFunction::query()->where('organization_id', $orgId)->whereKey($id)->firstOrFail();
        $this->editingFunctionId = (int) $fn->id;
        $this->editingFunctionName = (string) $fn->name;
    }

    public function cancelEditFunction(): void
    {
        $this->editingFunctionId = null;
        $this->editingFunctionName = '';
    }

    public function updateFunction(): void
    {
        if (! $this->canManage) {
            abort(403);
        }
        $this->validate(['editingFunctionName' => ['required', 'string', 'max:80']]);
        $orgId = $this->orgId();
        abort_if(! $orgId || ! $this->editingFunctionId, 403);

        $fn = OrganizationFunction::query()
            ->where('organization_id', $orgId)
            ->whereKey($this->editingFunctionId)
            ->firstOrFail();
        $fn->update(['name' => trim($this->editingFunctionName)]);
        $this->editingFunctionId = null;
        $this->editingFunctionName = '';
        CacheHelper::invalidateOrgFunctions($orgId);
        $this->dispatch('toast', type: 'success', message: __('Fonction mise à jour.'));
    }

    public function deleteFunction(int $id): void
    {
        if (! $this->canManage) {
            abort(403);
        }
        $orgId = $this->orgId();
        $fn = OrganizationFunction::query()->where('organization_id', $orgId)->whereKey($id)->firstOrFail();
        if ($fn->tickets()->exists()) {
            $this->dispatch('toast', type: 'error', message: __('Impossible de supprimer : des tickets sont assignés à cette fonction.'));

            return;
        }
        $fn->memberships()->update(['organization_function_id' => null]);
        $fn->delete();
        CacheHelper::invalidateOrgFunctions($orgId);
        $this->dispatch('toast', type: 'success', message: __('Fonction supprimée.'));
    }

    // ─── API Tokens ───────────────────────────────────────────────────

    public function createApiToken(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageApi)) {
            $this->dispatch('toast', type: 'error', message: __('Accès refusé.'));

            return;
        }

        $this->validate([
            'newTokenName' => ['required', 'string', 'max:120'],
            'newTokenScopes' => ['required', 'array', 'min:1'],
            'newTokenScopes.*' => ['string', \Illuminate\Validation\Rule::in(array_column(ApiTokenScope::cases(), 'value'))],
            'newTokenExpiresAt' => ['nullable', 'date', 'after:today'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $token = $user->createToken($this->newTokenName, $this->newTokenScopes);

        $accessToken = $token->accessToken;
        $accessToken->forceFill([
            'organization_id' => $orgId,
            'scopes' => $this->newTokenScopes,
            'expires_at' => $this->newTokenExpiresAt ? \Carbon\Carbon::parse($this->newTokenExpiresAt)->endOfDay() : null,
        ])->save();

        $this->createdTokenPlainText = 'mnx_'.base64_encode($token->plainTextToken);
        $this->newTokenName = '';
        $this->newTokenScopes = [];
        $this->newTokenExpiresAt = null;

        OrganizationAuditService::log('api_token.created', 'PersonalAccessToken', $accessToken->id, [
            'name' => $accessToken->name,
            'scopes' => $this->createdTokenPlainText ? $accessToken->scopes : [],
        ]);

        CacheHelper::invalidateSettingsApiTokens($orgId);

        $this->dispatch('toast', type: 'success', message: __('Token API créé avec succès.'));
    }

    public function dismissCreatedToken(): void
    {
        $this->createdTokenPlainText = null;
    }

    public function revokeApiToken(int $tokenId): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageApi)) {
            $this->dispatch('toast', type: 'error', message: __('Accès refusé.'));

            return;
        }

        $orgId = $this->orgId();
        $token = \Laravel\Sanctum\PersonalAccessToken::query()
            ->where('organization_id', $orgId)
            ->whereKey($tokenId)
            ->firstOrFail();

        OrganizationAuditService::log('api_token.revoked', 'PersonalAccessToken', $token->id, [
            'name' => $token->name,
        ]);

        $token->delete();

        CacheHelper::invalidateSettingsApiTokens($orgId);

        $this->dispatch('toast', type: 'success', message: __('Token révoqué.'));
    }

    // ─── Webhooks ─────────────────────────────────────────────────────

    public function createWebhookEndpoint(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageWebhooks)) {
            $this->dispatch('toast', type: 'error', message: __('Accès refusé.'));

            return;
        }

        $this->validate([
            'newWebhookUrl' => ['required', 'url', 'max:2048'],
            'newWebhookEvents' => ['required', 'array', 'min:1'],
            'newWebhookEvents.*' => ['string', \Illuminate\Validation\Rule::in(array_column(WebhookEvent::cases(), 'value'))],
            'newWebhookDescription' => ['nullable', 'string', 'max:255'],
        ]);

        $orgId = $this->orgId();
        abort_if(! $orgId, 403);

        $secret = Str::random(64);

        $endpoint = WebhookEndpoint::query()->create([
            'organization_id' => $orgId,
            'url' => $this->newWebhookUrl,
            'description' => $this->newWebhookDescription ?: null,
            'secret' => $secret,
            'events' => $this->newWebhookEvents,
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $this->createdWebhookSecret = $secret;
        $this->newWebhookUrl = '';
        $this->newWebhookDescription = '';
        $this->newWebhookEvents = [];

        OrganizationAuditService::log('webhook.created', 'WebhookEndpoint', $endpoint->id, [
            'url' => $endpoint->url,
            'events' => $endpoint->events,
        ]);

        CacheHelper::invalidateSettingsWebhooks($orgId);

        $this->dispatch('toast', type: 'success', message: __('Webhook créé avec succès.'));
    }

    public function dismissCreatedWebhookSecret(): void
    {
        $this->createdWebhookSecret = null;
    }

    public function startEditWebhook(int $id): void
    {
        $orgId = $this->orgId();
        $endpoint = WebhookEndpoint::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $this->editingWebhookId = $endpoint->id;
        $this->editingWebhookUrl = $endpoint->url;
        $this->editingWebhookDescription = $endpoint->description ?? '';
        $this->editingWebhookEvents = $endpoint->events ?? [];
        $this->editingWebhookIsActive = $endpoint->is_active;
    }

    public function cancelEditWebhook(): void
    {
        $this->editingWebhookId = null;
        $this->editingWebhookUrl = '';
        $this->editingWebhookDescription = '';
        $this->editingWebhookEvents = [];
        $this->editingWebhookIsActive = true;
    }

    public function updateWebhookEndpoint(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageWebhooks)) {
            $this->dispatch('toast', type: 'error', message: __('Accès refusé.'));

            return;
        }

        $this->validate([
            'editingWebhookUrl' => ['required', 'url', 'max:2048'],
            'editingWebhookEvents' => ['required', 'array', 'min:1'],
            'editingWebhookEvents.*' => ['string', \Illuminate\Validation\Rule::in(array_column(WebhookEvent::cases(), 'value'))],
            'editingWebhookDescription' => ['nullable', 'string', 'max:255'],
        ]);

        $orgId = $this->orgId();
        $endpoint = WebhookEndpoint::query()
            ->where('organization_id', $orgId)
            ->whereKey($this->editingWebhookId)
            ->firstOrFail();

        $endpoint->update([
            'url' => $this->editingWebhookUrl,
            'description' => $this->editingWebhookDescription ?: null,
            'events' => $this->editingWebhookEvents,
            'is_active' => $this->editingWebhookIsActive,
        ]);

        OrganizationAuditService::log('webhook.updated', 'WebhookEndpoint', $endpoint->id, [
            'url' => $endpoint->url,
            'events' => $endpoint->events,
        ]);

        CacheHelper::invalidateSettingsWebhooks($orgId);

        $this->cancelEditWebhook();
        $this->dispatch('toast', type: 'success', message: __('Webhook mis à jour.'));
    }

    public function toggleWebhookEndpoint(int $id): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageWebhooks)) {
            $this->dispatch('toast', type: 'error', message: __('Accès refusé.'));

            return;
        }

        $orgId = $this->orgId();
        $endpoint = WebhookEndpoint::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        $endpoint->update(['is_active' => ! $endpoint->is_active]);

        CacheHelper::invalidateSettingsWebhooks($orgId);

        $this->dispatch('toast', type: 'success', message: $endpoint->is_active ? __('Webhook activé.') : __('Webhook désactivé.'));
    }

    public function deleteWebhookEndpoint(int $id): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->hasPermission(Permission::SettingsManageWebhooks)) {
            $this->dispatch('toast', type: 'error', message: __('Accès refusé.'));

            return;
        }

        $orgId = $this->orgId();
        $endpoint = WebhookEndpoint::query()
            ->where('organization_id', $orgId)
            ->whereKey($id)
            ->firstOrFail();

        OrganizationAuditService::log('webhook.deleted', 'WebhookEndpoint', $endpoint->id, [
            'url' => $endpoint->url,
        ]);

        $endpoint->delete();

        CacheHelper::invalidateSettingsWebhooks($orgId);

        $this->dispatch('toast', type: 'success', message: __('Webhook supprimé.'));
    }
}
