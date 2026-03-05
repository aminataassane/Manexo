<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Organization;
use App\Models\Ticket;
use App\Services\BackupService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.super-admin', ['title' => 'super_admin.files.title'])]
class FilesStorage extends Component
{
    #[Computed]
    public function storageByOrg(): array
    {
        $disk = Storage::disk('public');
        $orgs = Organization::all(['id', 'name']);
        $backup = new BackupService;
        $result = [];

        // Map ticket IDs to org IDs for ticket-messages/ folder
        $ticketOrgMap = Ticket::pluck('organization_id', 'id')->toArray();

        foreach ($orgs as $org) {
            $totalFiles = 0;
            $totalSize = 0;

            // 1. ticket-attachments/org-{id}/
            $attachPath = 'ticket-attachments/org-' . $org->id;
            if ($disk->exists($attachPath)) {
                $files = $disk->allFiles($attachPath);
                $totalFiles += count($files);
                foreach ($files as $f) {
                    $totalSize += $disk->size($f);
                }
            }

            // 2. form-responses/org-{id}/
            $formPath = 'form-responses/org-' . $org->id;
            if ($disk->exists($formPath)) {
                $files = $disk->allFiles($formPath);
                $totalFiles += count($files);
                foreach ($files as $f) {
                    $totalSize += $disk->size($f);
                }
            }

            // 3. ticket-messages/{ticketId}/ — match via ticket ownership
            $orgTicketIds = array_keys(array_filter($ticketOrgMap, fn ($oid) => $oid === $org->id));
            foreach ($orgTicketIds as $ticketId) {
                $msgPath = 'ticket-messages/' . $ticketId;
                if ($disk->exists($msgPath)) {
                    $files = $disk->allFiles($msgPath);
                    $totalFiles += count($files);
                    foreach ($files as $f) {
                        $totalSize += $disk->size($f);
                    }
                }
            }

            if ($totalFiles > 0) {
                $result[] = [
                    'org_name' => $org->name,
                    'files_count' => $totalFiles,
                    'size' => $totalSize,
                    'size_human' => $backup->humanSize($totalSize),
                ];
            }
        }

        usort($result, fn ($a, $b) => $b['size'] <=> $a['size']);

        return $result;
    }

    #[Computed]
    public function totalStorage(): array
    {
        $disk = Storage::disk('public');
        $allFiles = $disk->allFiles();
        $totalSize = 0;
        $extensions = [];

        foreach ($allFiles as $file) {
            $size = $disk->size($file);
            $totalSize += $size;
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION)) ?: 'other';
            $extensions[$ext] = ($extensions[$ext] ?? 0) + 1;
        }

        arsort($extensions);

        return [
            'total_files' => count($allFiles),
            'total_size' => $totalSize,
            'total_size_human' => (new BackupService)->humanSize($totalSize),
            'extensions' => array_slice($extensions, 0, 10, true),
        ];
    }

    #[Computed]
    public function recentFiles(): array
    {
        $disk = Storage::disk('public');
        $allFiles = $disk->allFiles();
        $fileList = [];

        foreach ($allFiles as $file) {
            $fileList[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => $disk->size($file),
                'size_human' => (new BackupService)->humanSize($disk->size($file)),
                'modified' => date('Y-m-d H:i:s', $disk->lastModified($file)),
            ];
        }

        usort($fileList, fn ($a, $b) => strcmp($b['modified'], $a['modified']));

        return array_slice($fileList, 0, 20);
    }

    public function render()
    {
        return view('livewire.super-admin.files-storage');
    }
}
