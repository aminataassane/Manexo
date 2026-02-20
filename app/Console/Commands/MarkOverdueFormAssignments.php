<?php

namespace App\Console\Commands;

use App\Enums\FormAssignmentStatus;
use App\Events\UserNotificationReceived;
use App\Models\FormAssignment;
use App\Notifications\FormOverdueNotification;
use Illuminate\Console\Command;

class MarkOverdueFormAssignments extends Command
{
    protected $signature = 'forms:mark-overdue';

    protected $description = 'Mark pending form assignments past their due date as overdue and notify users.';

    public function handle(): int
    {
        $assignments = FormAssignment::query()
            ->where('status', FormAssignmentStatus::Pending)
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->with(['form:id,name', 'user'])
            ->get();

        $count = 0;

        foreach ($assignments as $assignment) {
            $assignment->update(['status' => FormAssignmentStatus::Overdue]);

            $user = $assignment->user;
            if ($user) {
                $user->notify(new FormOverdueNotification(
                    formId: (int) $assignment->form_id,
                    formName: (string) ($assignment->form?->name ?? 'Formulaire'),
                    assignmentId: (int) $assignment->id,
                    dueDate: $assignment->due_date?->format('d/m/Y'),
                ));

                broadcast(new UserNotificationReceived((int) $user->id));
            }

            $count++;
        }

        $this->info("Marked {$count} assignment(s) as overdue.");

        return self::SUCCESS;
    }
}
