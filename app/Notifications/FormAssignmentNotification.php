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
        public ?string $assignmentPublicId = null,
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
            'assignment_public_id' => $this->assignmentPublicId,
            'assigned_by_id' => $this->assignedById,
            'assigned_by_name' => $this->assignedByName,
            'due_date' => $this->dueDate,
        ];
    }
}
