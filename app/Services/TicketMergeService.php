<?php

namespace App\Services;

use App\Enums\TicketMessageType;
use App\Enums\TicketStatus;
use App\Helpers\CacheHelper;
use App\Models\Ticket;
use App\Models\TicketLink;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TicketMergeService
{
    /**
     * Merge a source ticket into a target ticket.
     *
     * - All messages from source are copied to target
     * - Source participants/assignees become target participants
     * - Source is marked as merged and closed
     * - Both tickets are linked with 'merged_into' type
     * - History is preserved on both sides
     */
    public static function merge(Ticket $source, Ticket $target, User $actor): void
    {
        if ($source->id === $target->id) {
            throw new \InvalidArgumentException('Cannot merge a ticket into itself.');
        }

        if ((int) $source->organization_id !== (int) $target->organization_id) {
            throw new \InvalidArgumentException('Cannot merge tickets from different organizations.');
        }

        DB::transaction(function () use ($source, $target, $actor) {
            // 1. Copy messages from source to target (preserving originals)
            $sourceMessages = TicketMessage::where('ticket_id', $source->id)
                ->orderBy('id')
                ->get();

            foreach ($sourceMessages as $msg) {
                TicketMessage::create([
                    'ticket_id' => $target->id,
                    'user_id' => $msg->user_id,
                    'type' => $msg->type,
                    'body' => $msg->body,
                    'attachments' => $msg->attachments,
                    'meta' => array_merge(
                        is_array($msg->meta) ? $msg->meta : [],
                        ['merged_from_ticket_id' => $source->id, 'original_message_id' => $msg->id]
                    ),
                    'email_message_id' => $msg->email_message_id,
                    'created_at' => $msg->created_at,
                    'updated_at' => $msg->updated_at,
                ]);
            }

            // 2. Move source participants to target
            $source->loadMissing(['participants', 'assignees']);
            $existingParticipants = $target->participants()->pluck('users.id')->toArray();
            $existingAssignees = $target->assignees()->pluck('users.id')->toArray();

            foreach ($source->participants as $participant) {
                $pid = (int) $participant->id;
                if ($pid !== (int) $target->created_by && ! in_array($pid, $existingParticipants) && ! in_array($pid, $existingAssignees)) {
                    $target->participants()->attach($pid, ['added_by' => $actor->id]);
                }
            }

            // 3. Copy source attachments to target (if any)
            if (is_array($source->attachments) && ! empty($source->attachments)) {
                $targetAttachments = is_array($target->attachments) ? $target->attachments : ['files' => [], 'links' => []];
                $sourceFiles = $source->attachments['files'] ?? [];
                $sourceLinks = $source->attachments['links'] ?? [];
                $targetAttachments['files'] = array_merge($targetAttachments['files'] ?? [], $sourceFiles);
                $targetAttachments['links'] = array_merge($targetAttachments['links'] ?? [], $sourceLinks);
                $target->update(['attachments' => $targetAttachments]);
            }

            // 4. Create system messages on both tickets
            TicketMessage::create([
                'ticket_id' => $target->id,
                'user_id' => null,
                'type' => TicketMessageType::System,
                'body' => __(':actor a fusionné le ticket :source dans ce ticket.', [
                    'actor' => $actor->name,
                    'source' => $source->shortReference(),
                ]),
                'meta' => ['action' => 'ticket_merged', 'source_ticket_id' => $source->id, 'source_public_id' => $source->public_id],
            ]);

            TicketMessage::create([
                'ticket_id' => $source->id,
                'user_id' => null,
                'type' => TicketMessageType::System,
                'body' => __(':actor a fusionné ce ticket dans :target.', [
                    'actor' => $actor->name,
                    'target' => $target->shortReference(),
                ]),
                'meta' => ['action' => 'ticket_merged_into', 'target_ticket_id' => $target->id, 'target_public_id' => $target->public_id],
            ]);

            // 5. Create bidirectional links
            TicketLink::create([
                'ticket_id' => $source->id,
                'linked_ticket_id' => $target->id,
                'link_type' => 'merged_into',
                'created_by' => $actor->id,
            ]);

            TicketLink::create([
                'ticket_id' => $target->id,
                'linked_ticket_id' => $source->id,
                'link_type' => 'merged_from',
                'created_by' => $actor->id,
            ]);

            // 6. Close the source ticket and mark as merged
            $source->update([
                'status' => TicketStatus::Closed,
                'merged_into_ticket_id' => $target->id,
                'merged_at' => now(),
                'merged_by' => $actor->id,
                'closed_by' => $actor->id,
                'closed_at' => now(),
            ]);

            // 7. Audit
            OrganizationAuditService::log('ticket.merged', 'Ticket', $source->id, [
                'source' => $source->public_id,
                'target' => $target->public_id,
                'merged_by' => $actor->name,
            ]);

            // 8. Invalidate caches
            $orgId = (int) $target->organization_id;
            CacheHelper::invalidateDashboard($orgId);
            CacheHelper::invalidateReports($orgId);
            CacheHelper::invalidateTicketCounts($orgId);
        });
    }

    /**
     * Link two tickets together (related, duplicate — not merge).
     */
    public static function link(Ticket $ticket, Ticket $linkedTicket, string $type, User $actor): void
    {
        if ($ticket->id === $linkedTicket->id) {
            return;
        }

        $validTypes = ['related', 'duplicate'];
        if (! in_array($type, $validTypes, true)) {
            $type = 'related';
        }

        // Create bidirectional link
        TicketLink::firstOrCreate(
            ['ticket_id' => $ticket->id, 'linked_ticket_id' => $linkedTicket->id],
            ['link_type' => $type, 'created_by' => $actor->id]
        );
        TicketLink::firstOrCreate(
            ['ticket_id' => $linkedTicket->id, 'linked_ticket_id' => $ticket->id],
            ['link_type' => $type, 'created_by' => $actor->id]
        );
    }

    /**
     * Remove a link between two tickets.
     */
    public static function unlink(Ticket $ticket, Ticket $linkedTicket): void
    {
        TicketLink::where('ticket_id', $ticket->id)->where('linked_ticket_id', $linkedTicket->id)->delete();
        TicketLink::where('ticket_id', $linkedTicket->id)->where('linked_ticket_id', $ticket->id)->delete();
    }
}
