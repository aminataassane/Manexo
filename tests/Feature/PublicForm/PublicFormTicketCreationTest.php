<?php

namespace Tests\Feature\PublicForm;

use App\Enums\FormStatus;
use App\Enums\TicketSource;
use App\Models\Form;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicFormTicketCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_submission_creates_ticket_and_form_response_with_form_source(): void
    {
        $owner = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $owner->id]);
        OrganizationMembership::query()->create([
            'organization_id' => $org->id,
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);

        $category = TicketCategory::query()->create([
            'organization_id' => $org->id,
            'name' => 'General',
            'slug' => 'general',
            'is_active' => true,
        ]);

        TicketPriority::query()->create([
            'organization_id' => $org->id,
            'name' => 'Normal',
            'level' => 1,
            'is_active' => true,
        ]);

        $form = Form::query()->create([
            'organization_id' => $org->id,
            'name' => 'Contact support',
            'slug' => 'contact-support-test-'.uniqid(),
            'status' => FormStatus::Published,
            'is_public' => true,
            'creates_ticket' => true,
            'ticket_category_id' => $category->id,
            'current_version' => 1,
            'public_thank_you' => 'Merci, nous avons bien reçu votre demande.',
        ]);

        $this->assertTrue($form->fresh()->creates_ticket);

        $response = $this->post(route('forms.public.submit', ['slug' => $form->slug]), [
            'subject' => 'Problème de connexion',
            'description' => 'Je ne peux pas me connecter depuis ce matin.',
            'guest_name' => 'Alex Dupont',
            'guest_email' => 'alex.dupont.publicform@example.com',
            'website' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('public_form_success');
        $response->assertSessionHas('public_form_ticket_ref');

        $this->assertDatabaseHas('tickets', [
            'organization_id' => $org->id,
            'source' => TicketSource::Form->value,
        ]);

        $ticketRef = session('public_form_ticket_ref');
        $this->assertNotNull($ticketRef);

        $ticket = Ticket::query()->where('organization_id', $org->id)->firstOrFail();
        $this->assertSame($ticket->shortReference(), $ticketRef);
        $this->assertSame('Problème de connexion', $ticket->subject);
        $this->assertStringContainsString('connecter', $ticket->description);

        $this->assertDatabaseHas('form_responses', [
            'form_id' => $form->id,
            'ticket_id' => $ticket->id,
            'submitted_from' => 'public',
        ]);
    }

    public function test_honeypot_filled_returns_unprocessable(): void
    {
        $owner = User::factory()->create();
        $org = Organization::factory()->create(['created_by' => $owner->id]);

        $category = TicketCategory::query()->create([
            'organization_id' => $org->id,
            'name' => 'General',
            'slug' => 'general-2',
            'is_active' => true,
        ]);

        TicketPriority::query()->create([
            'organization_id' => $org->id,
            'name' => 'Normal',
            'level' => 1,
            'is_active' => true,
        ]);

        $form = Form::query()->create([
            'organization_id' => $org->id,
            'name' => 'Contact',
            'slug' => 'contact-honeypot',
            'status' => FormStatus::Published,
            'is_public' => true,
            'creates_ticket' => true,
            'ticket_category_id' => $category->id,
            'current_version' => 1,
        ]);

        $response = $this->post(route('forms.public.submit', ['slug' => $form->slug]), [
            'subject' => 'Spam',
            'description' => 'Spam body',
            'guest_name' => 'Bot',
            'guest_email' => 'bot@example.com',
            'website' => 'http://spam.example',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('tickets', 0);
    }
}
