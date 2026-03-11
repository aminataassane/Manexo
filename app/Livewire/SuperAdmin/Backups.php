<?php

namespace App\Livewire\SuperAdmin;

use App\Services\BackupService;
use App\Services\SuperAdminAuditService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.super-admin', ['title' => 'super_admin.backups.title'])]
class Backups extends Component
{
    #[Computed]
    public function backups(): array
    {
        return (new BackupService)->listBackups();
    }

    public function createBackup(): void
    {
        if (! auth()->user()->canPlatformAdminister()) {
            return;
        }

        $result = (new BackupService)->createBackup();

        if ($result['success']) {
            SuperAdminAuditService::log('backup.create', null, null, [
                'filename' => $result['filename'],
            ]);

            session()->flash('success', __('super_admin.backups.created', ['filename' => $result['filename']]));
        } else {
            session()->flash('error', __('super_admin.backups.failed', ['error' => $result['error']]));
        }

        unset($this->backups);
    }

    public function deleteBackup(string $filename): void
    {
        if (! auth()->user()->canPlatformAdminister()) {
            return;
        }

        $service = new BackupService;
        $filepath = $service->getBackupDirectory() . '/' . basename($filename);

        if (file_exists($filepath) && @unlink($filepath)) {
            SuperAdminAuditService::log('backup.delete', null, null, [
                'filename' => $filename,
            ]);
            session()->flash('success', __('super_admin.backups.deleted', ['filename' => $filename]));
        } else {
            session()->flash('error', __('super_admin.backups.delete_failed'));
        }

        unset($this->backups);
    }

    public function downloadBackup(string $filename): mixed
    {
        if (! auth()->user()->canPlatformAdminister()) {
            return null;
        }

        $service = new BackupService;
        $filepath = $service->getBackupDirectory() . '/' . basename($filename);

        if (! file_exists($filepath)) {
            session()->flash('error', 'File not found.');
            return null;
        }

        SuperAdminAuditService::log('backup.download', null, null, [
            'filename' => $filename,
        ]);

        return response()->download($filepath);
    }

    public function render()
    {
        return view('livewire.super-admin.backups');
    }
}
