<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FormResponseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $formId,
        public string $formName,
        public int $responseId,
        public int $responderId,
        public string $responderName,
        public string $source = 'assignment',
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'form_response',
            'form_id' => $this->formId,
            'form_name' => $this->formName,
            'response_id' => $this->responseId,
            'responder_id' => $this->responderId,
            'responder_name' => $this->responderName,
            'source' => $this->source,
        ];
    }
}
