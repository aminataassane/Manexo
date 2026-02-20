<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FormOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $formId,
        public string $formName,
        public int $assignmentId,
        public string $dueDate,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'form_overdue',
            'form_id' => $this->formId,
            'form_name' => $this->formName,
            'assignment_id' => $this->assignmentId,
            'due_date' => $this->dueDate,
        ];
    }
}
