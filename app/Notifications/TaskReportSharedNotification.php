<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskReportSharedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $senderName,
        public string $reportUrl,
        public int $orgId,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_report_shared',
            'sender_name' => $this->senderName,
            'report_url' => $this->reportUrl,
            'org_id' => $this->orgId,
        ];
    }
}
