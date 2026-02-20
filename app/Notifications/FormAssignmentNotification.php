<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FormAssignmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $formId,
        public string $formName,
        public int $assignmentId,
        public int $assignedById,
        public string $assignedByName,
        public ?string $dueDate = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'form_assignment',
            'form_id' => $this->formId,
            'form_name' => $this->formName,
            'assignment_id' => $this->assignmentId,
            'assigned_by_id' => $this->assignedById,
            'assigned_by_name' => $this->assignedByName,
            'due_date' => $this->dueDate,
        ];
    }
}
