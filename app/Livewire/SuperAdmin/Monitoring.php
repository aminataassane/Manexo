<?php

namespace App\Livewire\SuperAdmin;

use App\Services\SuperAdminAuditService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.super-admin', ['title' => 'super_admin.monitoring.title'])]
class Monitoring extends Component
{
    use WithPagination;

    public int $perPage = 15;

    #[Computed]
    public function systemInfo(): array
    {
        $dbVersion = '';
        try {
            $dbVersion = DB::selectOne('SELECT version()')->version ?? '';
        } catch (\Throwable) {
        }

        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'db_version' => $dbVersion,
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'session_driver' => config('session.driver'),
        ];
    }

    #[Computed]
    public function jobCounts(): array
    {
        $pending = 0;
        $failed = 0;

        try {
            $pending = DB::table('jobs')->count();
        } catch (\Throwable) {
        }

        try {
            $failed = DB::table('failed_jobs')->count();
        } catch (\Throwable) {
        }

        return [
            'pending' => $pending,
            'failed' => $failed,
        ];
    }

    public function retryJob(string $uuid): void
    {
        if (! auth()->user()->canPlatformAdminister()) {
            return;
        }

        Artisan::call('queue:retry', ['id' => [$uuid]]);

        SuperAdminAuditService::log('job.retry', 'FailedJob', null, [
            'uuid' => $uuid,
        ]);

        session()->flash('success', __('super_admin.monitoring.retried'));
    }

    public function deleteFailedJob(string $uuid): void
    {
        if (! auth()->user()->canPlatformAdminister()) {
            return;
        }

        DB::table('failed_jobs')->where('uuid', $uuid)->delete();

        SuperAdminAuditService::log('job.delete', 'FailedJob', null, [
            'uuid' => $uuid,
        ]);

        session()->flash('success', __('super_admin.monitoring.deleted'));
    }

    public function render()
    {
        $failedJobs = collect();
        try {
            $failedJobs = DB::table('failed_jobs')
                ->orderByDesc('failed_at')
                ->paginate($this->perPage);
        } catch (\Throwable) {
            $failedJobs = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }

        return view('livewire.super-admin.monitoring', [
            'failedJobs' => $failedJobs,
        ]);
    }
}
