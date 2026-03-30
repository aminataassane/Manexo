<?php

namespace App\Notifications;

use App\DataTransferObjects\ClientAssignmentClientScenario;
use App\DataTransferObjects\ClientAssignmentContext;
use App\Models\Ticket;
use App\Notifications\Concerns\BuildsTicketThreadedOutboundMail;
use App\Traits\ResolvesNotificationChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

/**
 * E-mail + in-app (externes) : message **événement** d’assignation, pas un instantané ambigu du « responsable ».
 *
 * @phpstan-type AssignmentPayload array{scenario: string, target: ?string}
 */
class TicketClientRoutingNotification extends Notification implements ShouldQueue
{
    use BuildsTicketThreadedOutboundMail;
    use Queueable;
    use ResolvesNotificationChannels;

    /** @var AssignmentPayload */
    public array $assignment;

    public function __construct(
        public int $ticketId,
        public int $organizationId,
        ClientAssignmentContext $assignmentContext,
    ) {
        $this->assignment = [
            'scenario' => $assignmentContext->clientScenario->value,
            'target' => $assignmentContext->targetDisplayName,
        ];
    }

    private function assignmentContext(): ClientAssignmentContext
    {
        return new ClientAssignmentContext(
            ClientAssignmentClientScenario::from($this->assignment['scenario']),
            $this->assignment['target'] ?? null,
        );
    }

    public function via(object $notifiable): array
    {
        return $this->resolveChannels($notifiable, $this->organizationId);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = Ticket::query()
            ->with(['organization.mailbox'])
            ->findOrFail($this->ticketId);

        $org = $ticket->organization;
        $orgName = $org?->name ?? config('app.name', 'Support');
        $reference = $ticket->shortReference();
        $subject = $reference.' — '.__('tickets.client_routing.email_subject');

        $bodyText = $this->buildClientBodyText();
        $ticketUrl = route('tickets.discussion', ['ticket' => $ticket->public_id]);

        $logoUrl = null;
        $branding = is_array($org?->settings) ? ($org->settings['branding'] ?? []) : [];
        $logoPath = $branding['logo_path'] ?? null;
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $logoUrl = asset('storage/'.$logoPath);
        }

        $mail = new MailMessage;
        $mailReplyMode = $this->applyTicketThreadedOutboundMail($mail, $ticket, $orgName);

        return $mail
            ->subject($subject)
            ->view('emails.notifications.ticket-status', [
                'subject' => $subject,
                'preheader' => $bodyText,
                'heading' => __('tickets.client_routing.email_heading'),
                'bodyText' => $bodyText,
                'ticketReference' => $reference,
                'ticketSubject' => $ticket->subject,
                'ticketUrl' => $ticketUrl,
                'orgName' => $orgName,
                'logoUrl' => $logoUrl,
                'mailReplyMode' => $mailReplyMode,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $ticket = Ticket::query()->find($this->ticketId);

        return [
            'type' => 'ticket_client_routing',
            'ticket_id' => $this->ticketId,
            'ticket_public_id' => $ticket?->public_id,
            'ticket_reference' => $ticket?->shortReference(),
            'ticket_subject' => $ticket?->subject,
            'body_excerpt' => $this->buildClientBodyText(),
        ];
    }

    private function buildClientBodyText(): string
    {
        $ctx = $this->assignmentContext();
        $name = $ctx->targetDisplayName;
        $scenario = $ctx->clientScenario;

        return match ($scenario) {
            ClientAssignmentClientScenario::PersonNamed => $name
                ? __('tickets.client_routing.client_person_named', ['name' => $name])
                : __('tickets.client_routing.client_person_anonymous'),
            ClientAssignmentClientScenario::PersonAnonymous => __('tickets.client_routing.client_person_anonymous'),
            ClientAssignmentClientScenario::Group => $name
                ? __('tickets.client_routing.client_group', ['name' => $name])
                : __('tickets.client_routing.client_generic'),
            ClientAssignmentClientScenario::FunctionTeam => $name
                ? __('tickets.client_routing.client_function', ['name' => $name])
                : __('tickets.client_routing.client_generic'),
            ClientAssignmentClientScenario::MultipleMembers => __('tickets.client_routing.client_multiple'),
            ClientAssignmentClientScenario::Generic => __('tickets.client_routing.client_generic'),
        };
    }
}
