<?php

namespace App\Console\Commands;

use App\Enums\FormAssignmentStatus;
use App\Models\FormAssignment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateFormAssignmentStatuses extends Command
{
    protected $signature = 'forms:update-statuses';

    protected $description = 'Recalculate form assignment statuses (expired, overdue, pending) based on dates.';

    public function handle(): int
    {
        // 1. Mark expired: expires_at has passed, not already submitted/expired
        $expired = FormAssignment::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->whereNotIn('status', [
                FormAssignmentStatus::Submitted,
                FormAssignmentStatus::Expired,
            ])
            ->update(['status' => FormAssignmentStatus::Expired]);

        // 2. Mark overdue: due_date passed, not expired and not submitted/overdue
        $overdue = FormAssignment::query()
            ->whereNotNull('due_date')
            ->where('due_date', '<', DB::raw('CURRENT_DATE'))
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            })
            ->whereNotIn('status', [
                FormAssignmentStatus::Submitted,
                FormAssignmentStatus::Overdue,
                FormAssignmentStatus::Expired,
            ])
            ->update(['status' => FormAssignmentStatus::Overdue]);

        // 3. Reset to pending: dates not yet passed but status is wrong
        $pending = FormAssignment::query()
            ->where(function ($q) {
                $q->whereNull('due_date')
                  ->orWhere('due_date', '>=', DB::raw('CURRENT_DATE'));
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            })
            ->whereNotIn('status', [
                FormAssignmentStatus::Submitted,
                FormAssignmentStatus::Pending,
            ])
            ->update(['status' => FormAssignmentStatus::Pending]);

        $this->info("Updated statuses: {$expired} expired, {$overdue} overdue, {$pending} reset to pending.");

        return self::SUCCESS;
    }
}
