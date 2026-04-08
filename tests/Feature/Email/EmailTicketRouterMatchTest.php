<?php

namespace Tests\Feature\Email;

use App\Enums\TicketMessageType;
use App\Enums\TicketSource;
use App\Enums\TicketStatus;
use App\Models\Organization;
use App\Models\Scopes\OrganizationScope;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use App\Models\TicketPriority;
use App\Models\User;
use App\Services\Email\EmailTicketRouter;
use App\Services\Email\ParsedEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTicketRouterMatchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: int, 1: int}
     */
    private function seedCategoryAndPriority(int $organizationId): array
    {
        $category = TicketCategory::query()->create([
            'organization_id' => $organizationId,
            'name' => 'General',
            'slug' => 'cat-'.uniqid('', true),
            'is_active' => true,
        ]);

        $priority = TicketPriority::query()->create([
            'organization_id' => $organizationId,
            'name' => 'Normal',
            'level' => 1,
            'is_active' => true,
        ]);

        return [$category->id, $priority->id];
    }

    public function test_matches_ticket_by_plus_address_in_to_header(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        [$categoryId, $priorityId] = $this->seedCategoryAndPriority($org->id);

        $ticket = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $user->id,
            'ticket_category_id' => $categoryId,
            'ticket_priority_id' => $priorityId,
            'public_id' => 'TCK-ROUTERMATCH01',
            'subject' => 'Subject',
            'description' => 'Desc',
            'status' => TicketStatus::Open,
            'source' => TicketSource::Email,
        ]);

        $email = new ParsedEmail(
            messageId: '<msg@example.com>',
            fromEmail: 'client@example.com',
            fromName: 'Client',
            subject: 'Re: hello',
            textBody: 'Reply body',
            htmlBody: null,
            inReplyTo: null,
            references: [],
            headers: [
                'To' => 'support+TCK-ROUTERMATCH01@mail.example.com',
            ],
            attachments: [],
            uid: 1,
        );

        $matched = EmailTicketRouter::matchTicket($email, $org->id);

        $this->assertNotNull($matched);
        $this->assertTrue($matched->is($ticket));
    }

    public function test_matches_ticket_by_subject_ref_tag(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        [$categoryId, $priorityId] = $this->seedCategoryAndPriority($org->id);

        $ticket = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $user->id,
            'ticket_category_id' => $categoryId,
            'ticket_priority_id' => $priorityId,
            'public_id' => 'TCK-SUBREF00001',
            'subject' => 'Original',
            'description' => 'Desc',
            'status' => TicketStatus::Open,
            'source' => TicketSource::Email,
        ]);

        $email = new ParsedEmail(
            messageId: '<msg2@example.com>',
            fromEmail: 'client@example.com',
            fromName: 'Client',
            subject: 'Re: [REF-TCK-SUBREF00001] follow up',
            textBody: 'Reply',
            htmlBody: null,
            inReplyTo: null,
            references: [],
            headers: [],
            attachments: [],
            uid: 2,
        );

        $matched = EmailTicketRouter::matchTicket($email, $org->id);

        $this->assertNotNull($matched);
        $this->assertTrue($matched->is($ticket));
    }

    public function test_matches_ticket_by_in_reply_to_message_id(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $user->id]);
        [$categoryId, $priorityId] = $this->seedCategoryAndPriority($org->id);

        $ticket = Ticket::withoutGlobalScope(OrganizationScope::class)->create([
            'organization_id' => $org->id,
            'created_by' => $user->id,
            'ticket_category_id' => $categoryId,
            'ticket_priority_id' => $priorityId,
            'public_id' => 'TCK-MSGREF00001',
            'subject' => 'Thread',
            'description' => 'Desc',
            'status' => TicketStatus::Open,
            'source' => TicketSource::Email,
        ]);

        TicketMessage::query()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'type' => TicketMessageType::Message,
            'body' => 'First',
            'email_message_id' => '<thread-abc@thread.example.com>',
        ]);

        $email = new ParsedEmail(
            messageId: '<reply@example.com>',
            fromEmail: 'client@example.com',
            fromName: 'Client',
            subject: 'Re: Thread',
            textBody: 'Second',
            htmlBody: null,
            inReplyTo: 'thread-abc@thread.example.com',
            references: [],
            headers: [],
            attachments: [],
            uid: 3,
        );

        $matched = EmailTicketRouter::matchTicket($email, $org->id);

        $this->assertNotNull($matched);
        $this->assertTrue($matched->is($ticket));
    }
}
