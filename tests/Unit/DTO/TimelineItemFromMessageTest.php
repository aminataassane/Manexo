<?php

namespace Tests\Unit\DTO;

use App\DataTransferObjects\TimelineItem;
use App\Enums\TicketMessageType;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class TimelineItemFromMessageTest extends TestCase
{
    public function test_system_message_resolves_system_channel(): void
    {
        $msg = new TicketMessage([
            'ticket_id' => 1,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => 'Automated',
        ]);
        $msg->id = 1;
        $msg->created_at = Carbon::parse('2026-01-10 12:00:00');

        $ticket = $this->makeTicketForDto(99);
        $item = TimelineItem::fromMessage($msg, $ticket, 99, 5, [], $this->roleLabels());

        $this->assertSame('system', $item->channel);
        $this->assertTrue($item->isSystem);
    }

    public function test_internal_note_uses_internal_channel(): void
    {
        $msg = new TicketMessage([
            'ticket_id' => 1,
            'user_id' => 3,
            'type' => TicketMessageType::InternalNote,
            'body' => 'Note',
        ]);
        $msg->id = 2;
        $msg->setRelation('user', new User(['name' => 'Agent', 'email' => 'a@b.com']));
        $msg->created_at = Carbon::parse('2026-01-10 12:00:00');

        $ticket = $this->makeTicketForDto(1);
        $item = TimelineItem::fromMessage($msg, $ticket, 1, 3, [], $this->roleLabels());

        $this->assertSame('internal', $item->channel);
        $this->assertTrue($item->isInternal);
    }

    public function test_email_from_ticket_creator_is_inbound(): void
    {
        $creatorId = 10;
        $msg = new TicketMessage([
            'ticket_id' => 1,
            'user_id' => $creatorId,
            'type' => TicketMessageType::Message,
            'body' => 'Client reply',
            'email_message_id' => '<thread@example.com>',
        ]);
        $msg->id = 3;
        $msg->setRelation('user', new User(['name' => 'Client', 'email' => 'c@example.com']));
        $msg->created_at = Carbon::parse('2026-01-11 09:00:00');

        $ticket = $this->makeTicketForDto($creatorId);
        $item = TimelineItem::fromMessage($msg, $ticket, $creatorId, 99, [], $this->roleLabels());

        $this->assertSame('email_inbound', $item->channel);
        $this->assertTrue($item->hasEmailOrigin);
        $this->assertTrue($item->isCreator);
    }

    public function test_api_meta_resolves_api_channel(): void
    {
        $msg = new TicketMessage([
            'ticket_id' => 1,
            'user_id' => 7,
            'type' => TicketMessageType::Message,
            'body' => 'Via API',
            'meta' => ['source' => 'api'],
        ]);
        $msg->id = 4;
        $msg->setRelation('user', new User(['name' => 'API', 'email' => 'api@example.com']));
        $msg->created_at = Carbon::parse('2026-01-12 08:00:00');

        $ticket = $this->makeTicketForDto(1);
        $item = TimelineItem::fromMessage($msg, $ticket, 1, 7, [], $this->roleLabels());

        $this->assertSame('api', $item->channel);
    }

    /**
     * @return array<string, string>
     */
    private function roleLabels(): array
    {
        return [
            'owner' => 'Admin',
            'admin' => 'Admin',
            'agent' => 'Agent',
            'member' => 'Membre',
        ];
    }

    private function makeTicketForDto(int $createdBy): Ticket
    {
        $ticket = new Ticket([
            'id' => 1,
            'organization_id' => 1,
            'created_by' => $createdBy,
        ]);
        $ticket->setRelation('assignees', collect());
        $ticket->setRelation('participants', collect());

        return $ticket;
    }
}
