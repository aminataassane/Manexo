<?php

namespace Tests\Feature\Notifications;

use App\Enums\TicketMessageType;
use App\Enums\TicketSource;
use App\Enums\TicketStatus;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Scopes\OrganizationScope;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketNewMessageNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketNotificationChannelsTest extends TestCase
{
    use RefreshDatabase;

    private function membership(User $user, Organization $org, string $role): void
    {
        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => $role,
        ]);
    }

    public function test_ticket_created_notification_is_database_only_for_assignable_member(): void
    {
        $member = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $member->id]);
        $this->membership($member, $org, 'member');

        $notification = new TicketCreatedNotification(
            ticketId: 1,
            ticketPublicId: 'TCK-TEST',
            ticketReference: 'TCK-TEST',
            ticketSubject: 'Sujet',
            organizationId: $org->id,
            organizationName: 'Org',
        );

        $channels = $notification->via($member);

        $this->assertEquals(['database'], $channels);
    }

    public function test_ticket_created_notification_includes_mail_for_guest(): void
    {
        $guest = User::factory()->create();
        $guest->forceFill(['status' => 'guest'])->saveQuietly();

        $org = Organization::factory()->create(['created_by' => $guest->id]);
        $this->membership($guest, $org, 'member');

        $notification = new TicketCreatedNotification(
            ticketId: 1,
            ticketPublicId: 'TCK-TEST',
            ticketReference: 'TCK-TEST',
            ticketSubject: 'Sujet',
            organizationId: $org->id,
            organizationName: 'Org',
        );

        $channels = $notification->via($guest);

        $this->assertContains('database', $channels);
        $this->assertContains('mail', $channels);
    }

    public function test_ticket_created_notification_is_database_only_for_agent(): void
    {
        $agent = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $agent->id]);
        $this->membership($agent, $org, 'agent');

        $notification = new TicketCreatedNotification(
            ticketId: 1,
            ticketPublicId: 'TCK-TEST',
            ticketReference: 'TCK-TEST',
            ticketSubject: 'Sujet',
            organizationId: $org->id,
            organizationName: 'Org',
        );

        $channels = $notification->via($agent);

        $this->assertEquals(['database'], $channels);
    }

    public function test_new_message_notification_includes_mail_for_member_non_staff(): void
    {
        $member = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $member->id]);
        $this->membership($member, $org, 'member');

        [$categoryId, $priorityId] = $this->createTicketCategoryAndPriority($org->id);

        $ticket = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $member->id,
            'ticket_category_id' => $categoryId,
            'ticket_priority_id' => $priorityId,
            'subject' => 'Test',
            'description' => 'Desc',
            'status' => TicketStatus::Open,
            'source' => TicketSource::Platform,
        ]);

        $message = TicketMessage::query()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $member->id,
            'type' => TicketMessageType::Message,
            'body' => 'Hello',
        ]);

        $notification = new TicketNewMessageNotification($message);
        $channels = $notification->via($member);

        $this->assertContains('database', $channels);
        $this->assertContains('mail', $channels);
    }

    public function test_new_message_notification_is_database_only_for_agent(): void
    {
        $agent = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $agent->id]);
        $this->membership($agent, $org, 'agent');

        $other = User::factory()->create();
        $this->membership($other, $org, 'member');

        [$categoryId, $priorityId] = $this->createTicketCategoryAndPriority($org->id);

        $ticket = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $other->id,
            'ticket_category_id' => $categoryId,
            'ticket_priority_id' => $priorityId,
            'subject' => 'Test',
            'description' => 'Desc',
            'status' => TicketStatus::Open,
            'source' => TicketSource::Platform,
        ]);

        $message = TicketMessage::query()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $other->id,
            'type' => TicketMessageType::Message,
            'body' => 'Hello',
        ]);

        $notification = new TicketNewMessageNotification($message);
        $channels = $notification->via($agent);

        $this->assertEquals(['database'], $channels);
    }
}
