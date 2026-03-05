<?php

namespace App\Livewire\SuperAdmin;

use App\Models\SuperAdminAuditLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.super-admin', ['title' => 'super_admin.audit.title'])]
class AuditLog extends Component
{
    use WithPagination;

    #[Url]
    public string $actionFilter = '';

    #[Url]
    public string $targetTypeFilter = '';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public int $perPage = 20;

    public function updatingActionFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTargetTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = SuperAdminAuditLog::query()
            ->with('user:id,name');

        if ($this->actionFilter !== '') {
            $query->where('action', $this->actionFilter);
        }

        if ($this->targetTypeFilter !== '') {
            $query->where('target_type', $this->targetTypeFilter);
        }

        if ($this->dateFrom !== '') {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo !== '') {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $logs = $query->orderByDesc('created_at')->paginate($this->perPage);

        $actions = SuperAdminAuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $targetTypes = SuperAdminAuditLog::query()
            ->select('target_type')
            ->whereNotNull('target_type')
            ->distinct()
            ->orderBy('target_type')
            ->pluck('target_type');

        return view('livewire.super-admin.audit-log', [
            'logs' => $logs,
            'actions' => $actions,
            'targetTypes' => $targetTypes,
        ]);
    }
}
