<?php

namespace App\Console\Commands;

use App\Enums\TicketMessageType;
use App\Events\UserNotificationReceived;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\SlaAtRiskNotification;
use App\Notifications\SlaBreachedNotification;
use App\Services\AutomationService;
use App\Services\OrganizationAuditService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CheckSlaBreaches extends Command
{
    protected $signature = 'sla:check';

    protected $description = 'Check for SLA breaches and at-risk tickets, send notifications';

    public function handle(): int
    {
        $orgs = Organization::query()
            ->whereNotNull('settings')
            ->get()
            ->filter(function (Organization $org) {
                $settings = is_array($org->settings) ? $org->settings : [];

                return $settings['sla']['enabled'] ?? false;
            });

        if ($orgs->isEmpty()) {
            $this->info('No organizations with SLA enabled.');

            return self::SUCCESS;
        }

        $now = now();
        $breachCount = 0;
        $atRiskCount = 0;

        foreach ($orgs as $org) {
            $settings = is_array($org->settings) ? $org->settings : [];
            $threshold = (int) ($settings['sla']['at_risk_threshold_percent'] ?? 80);

            // ── First response breaches ──
            $frBreached = Ticket::withoutOrganizationScope()
                ->where('organization_id', $org->id)
                ->whereNotNull('sla_first_response_deadline')
                ->where('sla_first_response_deadline', '<', $now)
                ->whereNull('sla_first_response_met_at')
                ->where('sla_first_response_breached', false)
                ->whereNull('deleted_at')
                ->get();

            foreach ($frBreached as $ticket) {
                $ticket->update(['sla_first_response_breached' => true]);
                $minutesOverdue = (int) $now->diffInMinutes($ticket->sla_first_response_deadline);
                $this->notifyBreached($ticket, 'first_response', $minutesOverdue);
                $this->createBreachSystemMessage($ticket, 'Première réponse');
                AutomationService::evaluate($ticket, 'sla_breached');
                OrganizationAuditService::log('sla.first_response_breached', 'Ticket', $ticket->id, [
                    'ticket' => $ticket->subject,
                    'minutes_overdue' => $minutesOverdue,
                ]);
                $breachCount++;
            }

            // ── Resolution breaches ──
            $resBreached = Ticket::withoutOrganizationScope()
                ->where('organization_id', $org->id)
                ->whereNotNull('sla_resolution_deadline')
                ->where('sla_resolution_deadline', '<', $now)
                ->whereNull('sla_resolution_met_at')
                ->where('sla_resolution_breached', false)
                ->whereNull('sla_paused_at') // Don't breach while paused
                ->whereNull('deleted_at')
                ->get();

            foreach ($resBreached as $ticket) {
                $ticket->update(['sla_resolution_breached' => true]);
                $minutesOverdue = (int) $now->diffInMinutes($ticket->sla_resolution_deadline);
                $this->notifyBreached($ticket, 'resolution', $minutesOverdue);
                $this->createBreachSystemMessage($ticket, 'Résolution');
                AutomationService::evaluate($ticket, 'sla_breached');
                OrganizationAuditService::log('sla.resolution_breached', 'Ticket', $ticket->id, [
                    'ticket' => $ticket->subject,
                    'minutes_overdue' => $minutesOverdue,
                ]);
                $breachCount++;
            }

            // ── At-risk tickets (first response) ──
            $frAtRisk = Ticket::withoutOrganizationScope()
                ->where('organization_id', $org->id)
                ->whereNotNull('sla_first_response_deadline')
                ->where('sla_first_response_deadline', '>', $now)
                ->whereNull('sla_first_response_met_at')
                ->where('sla_first_response_breached', false)
                ->whereNull('deleted_at')
                ->get();

            foreach ($frAtRisk as $ticket) {
                $totalSeconds = $ticket->sla_first_response_deadline->diffInSeconds($ticket->created_at);
                $elapsed = $now->diffInSeconds($ticket->created_at);
                if ($totalSeconds > 0 && ($elapsed / $totalSeconds) * 100 >= $threshold) {
                    $cacheKey = "sla:at_risk:{$ticket->id}:first_response";
                    if (! Cache::has($cacheKey)) {
                        $remaining = (int) $now->diffInMinutes($ticket->sla_first_response_deadline, false);
                        $this->notifyAtRisk($ticket, 'first_response', max(0, $remaining));
                        Cache::put($cacheKey, true, 3600);
                        AutomationService::evaluate($ticket, 'sla_at_risk');
                        $atRiskCount++;
                    }
                }
            }

            // ── At-risk tickets (resolution) ──
            $resAtRisk = Ticket::withoutOrganizationScope()
                ->where('organization_id', $org->id)
                ->whereNotNull('sla_resolution_deadline')
                ->where('sla_resolution_deadline', '>', $now)
                ->whereNull('sla_resolution_met_at')
                ->where('sla_resolution_breached', false)
                ->whereNull('sla_paused_at')
                ->whereNull('deleted_at')
                ->get();

            foreach ($resAtRisk as $ticket) {
                $policy = $ticket->slaPolicy;
                if (! $policy || ! $policy->resolution_minutes) {
                    continue;
                }
                $totalAllowed = $policy->resolution_minutes * 60;
                $elapsed = $now->diffInSeconds($ticket->created_at) - (int) $ticket->sla_paused_seconds;
                if ($totalAllowed > 0 && ($elapsed / $totalAllowed) * 100 >= $threshold) {
                    $cacheKey = "sla:at_risk:{$ticket->id}:resolution";
                    if (! Cache::has($cacheKey)) {
                        $remaining = (int) $now->diffInMinutes($ticket->sla_resolution_deadline, false);
                        $this->notifyAtRisk($ticket, 'resolution', max(0, $remaining));
                        Cache::put($cacheKey, true, 3600);
                        AutomationService::evaluate($ticket, 'sla_at_risk');
                        $atRiskCount++;
                    }
                }
            }
        }

        $this->info("SLA check complete: {$breachCount} breach(es), {$atRiskCount} at-risk notification(s).");

        return self::SUCCESS;
    }

    private function notifyBreached(Ticket $ticket, string $slaType, int $minutesOverdue): void
    {
        $recipients = $this->getTicketRecipients($ticket);

        foreach ($recipients as $user) {
            $user->notify(new SlaBreachedNotification(
                ticketId: (int) $ticket->id,
                ticketPublicId: $ticket->public_id,
                ticketSubject: $ticket->subject,
                slaType: $slaType,
                minutesOverdue: $minutesOverdue,
            ));
            event(new UserNotificationReceived((int) $user->id, 'sla_breached'));
        }
    }

    private function notifyAtRisk(Ticket $ticket, string $slaType, int $minutesRemaining): void
    {
        $recipients = $this->getTicketRecipients($ticket);

        foreach ($recipients as $user) {
            $user->notify(new SlaAtRiskNotification(
                ticketId: (int) $ticket->id,
                ticketPublicId: $ticket->public_id,
                ticketSubject: $ticket->subject,
                slaType: $slaType,
                minutesRemaining: $minutesRemaining,
            ));
            event(new UserNotificationReceived((int) $user->id, 'sla_at_risk'));
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    private function getTicketRecipients(Ticket $ticket): \Illuminate\Database\Eloquent\Collection
    {
        $ticket->loadMissing('assignees');

        $ids = $ticket->assignees->pluck('id')->values();

        // Also notify org admins/owners
        $staffIds = \App\Models\OrganizationMembership::query()
            ->where('organization_id', $ticket->organization_id)
            ->whereIn('role', ['owner', 'admin'])
            ->pluck('user_id');

        $allIds = $ids->merge($staffIds)->unique()->values();

        return User::whereIn('id', $allIds)->get();
    }

    private function createBreachSystemMessage(Ticket $ticket, string $typeLabel): void
    {
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => "SLA {$typeLabel} dépassé pour ce ticket.",
            'meta' => ['action' => 'sla_breached', 'sla_type' => $typeLabel],
        ]);
    }
}
