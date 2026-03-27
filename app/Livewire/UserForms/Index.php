<?php

namespace App\Livewire\UserForms;

use App\Enums\FormAssignmentStatus;
use App\Enums\FormStatus;
use App\Helpers\CacheHelper;
use App\Models\Form;
use App\Models\FormAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.manexo-app')]
#[Title('Mes formulaires')]
class Index extends Component
{
    /** Limite d’affichage par onglet (évite les requêtes trop lourdes). */
    public const ASSIGNMENTS_LIST_LIMIT = 200;

    /** Fast path: render stats/lists directly on page open. */
    public int $loadStage = 2;

    public function mount(): void {}

    public string $tab = 'pending'; // pending | overdue | submitted | expired | all

    #[Computed]
    public function orgId(): ?int
    {
        return (int) session('current_organization_id') ?: null;
    }

    #[Computed]
    public function assignments()
    {
        $userId = Auth::id();
        $orgId = $this->orgId;
        if (! $userId || ! $orgId) {
            return collect();
        }

        return Cache::remember(
            CacheHelper::userFormsAssignmentsKey($orgId, $userId, $this->tab),
            CacheHelper::TTL,
            function () use ($userId, $orgId) {
                $query = FormAssignment::query()
                    ->forUser($userId, $orgId)
                    ->where('organization_id', $orgId)
                    ->with(['form:id,name,description,status', 'assignedBy:id,name']);

                if ($this->tab === 'pending') {
                    $query->where('status', FormAssignmentStatus::Pending);
                } elseif ($this->tab === 'overdue') {
                    $query->where('status', FormAssignmentStatus::Overdue);
                } elseif ($this->tab === 'submitted') {
                    $query->where('status', FormAssignmentStatus::Submitted);
                } elseif ($this->tab === 'expired') {
                    $query->where('status', FormAssignmentStatus::Expired);
                }

                return $query->latest()->limit(self::ASSIGNMENTS_LIST_LIMIT)->get();
            }
        );
    }

    /** Published forms for the whole team (target_user_id null) – everyone in the org can fill them. */
    #[Computed]
    public function teamForms()
    {
        $orgId = $this->orgId;
        if (! $orgId) {
            return collect();
        }

        return Cache::remember(CacheHelper::formsListKey($orgId), CacheHelper::TTL, function () use ($orgId) {
            return Form::query()
                ->forOrg($orgId)
                ->whereNull('target_user_id')
                ->where('status', FormStatus::Published)
                ->orderBy('name')
                ->get(['id', 'public_id', 'name', 'description', 'slug']);
        });
    }

    /** Statistics for assigned forms: pending, overdue, submitted, total. */
    #[Computed]
    public function formStats(): array
    {
        $userId = Auth::id();
        $orgId = $this->orgId;
        if (! $userId || ! $orgId) {
            return ['pending' => 0, 'overdue' => 0, 'expired' => 0, 'submitted' => 0, 'total' => 0];
        }

        return Cache::remember(CacheHelper::userFormsStatsKey($orgId, $userId), CacheHelper::TTL, function () use ($userId, $orgId) {
            $counts = FormAssignment::query()
                ->forUser($userId, $orgId)
                ->where('organization_id', $orgId)
                ->selectRaw('status, count(*) as c')
                ->groupBy('status')
                ->pluck('c', 'status');

            $pending = (int) ($counts[FormAssignmentStatus::Pending->value] ?? 0);
            $overdue = (int) ($counts[FormAssignmentStatus::Overdue->value] ?? 0);
            $expired = (int) ($counts[FormAssignmentStatus::Expired->value] ?? 0);
            $submitted = (int) ($counts[FormAssignmentStatus::Submitted->value] ?? 0);

            return [
                'pending' => $pending,
                'overdue' => $overdue,
                'expired' => $expired,
                'submitted' => $submitted,
                'total' => $pending + $overdue + $expired + $submitted,
            ];
        });
    }

    public function render()
    {
        return view('livewire.user-forms.index');
    }
}
