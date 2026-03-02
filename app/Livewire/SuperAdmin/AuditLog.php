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

    public int $perPage = 20;

    public function updatingActionFilter(): void
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

        $logs = $query->orderByDesc('created_at')->paginate($this->perPage);

        $actions = SuperAdminAuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('livewire.super-admin.audit-log', [
            'logs' => $logs,
            'actions' => $actions,
        ]);
    }
}
