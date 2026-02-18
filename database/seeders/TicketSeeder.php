<?php

namespace Database\Seeders;

use App\Enums\TicketMessageType;
use App\Enums\TicketStatus;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use App\Models\TicketPriority;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::query()->first();
        if (! $org) {
            $org = Organization::create([
                'name' => 'Qualité Center',
                'slug' => 'qualite-center',
                'primary_color' => '#005F02',
                'created_by' => null,
            ]);
        }

        $users = $org->users()->get();
        if ($users->isEmpty()) {
            $newUsers = User::factory(4)->create();
            foreach ($newUsers as $i => $u) {
                $org->users()->attach($u->id, ['role' => $i === 0 ? 'owner' : 'agent']);
            }
            $users = $org->users()->get();
        }

        $categories = $this->ensureCategories($org);
        $priorities = $this->ensurePriorities($org);

        $scenarios = [
            [
                'subject' => 'Impossible de se connecter au VPN',
                'description' => "Bonjour, depuis ce matin je n'arrive plus à me connecter au VPN. J'ai bien mes identifiants et le certificat à jour. Le message d'erreur indique « Connexion refusée - timeout ». Pouvez-vous vérifier côté serveur ?",
                'status' => TicketStatus::InProgress,
                'category' => 'IT',
                'priority' => 'Haute',
                'messages' => [
                    ['body' => "Bonjour, depuis ce matin je n'arrive plus à me connecter au VPN. J'ai bien mes identifiants et le certificat à jour. Le message d'erreur indique « Connexion refusée - timeout ». Pouvez-vous vérifier côté serveur ?", 'type' => 'message'],
                    ['body' => "Merci pour votre message 👍 Nous vérifions les logs du serveur VPN. Une mise à jour a été déployée cette nuit, il est possible qu'un paramètre soit en cause.", 'type' => 'message'],
                    ['body' => "J'ai vérifié les logs, c'est une erreur API 503. Je regarde ça. 🔧", 'type' => 'internal_note'],
                ],
            ],
            [
                'subject' => 'Demande de réinitialisation du mot de passe',
                'description' => "J'ai oublié mon mot de passe pour l'accès à l'intranet. Pouvez-vous le réinitialiser ? Mon email : collaborateur@entreprise.com",
                'status' => TicketStatus::Resolved,
                'category' => 'Support',
                'priority' => 'Moyenne',
                'messages' => [
                    ['body' => "J'ai oublié mon mot de passe pour l'accès à l'intranet. Pouvez-vous le réinitialiser ? 🙏", 'type' => 'message'],
                    ['body' => "Mot de passe réinitialisé. Un email avec le lien de réinitialisation vous a été envoyé. N'hésitez pas si vous ne le recevez pas. ✅", 'type' => 'message'],
                ],
            ],
            [
                'subject' => 'Erreur 504 sur l\'API de paiement',
                'description' => "En production, les appels vers l'API Stripe renvoient parfois une 504 Gateway Timeout. Ça impacte le tunnel de checkout. Urgent.",
                'status' => TicketStatus::Open,
                'category' => 'IT',
                'priority' => 'Urgente',
                'messages' => [
                    ['body' => "En prod, l'API Stripe renvoie des 504. Impact sur le checkout. 🔴", 'type' => 'message'],
                    ['body' => "Ticket assigné à l'équipe technique.", 'type' => 'system'],
                ],
            ],
            [
                'subject' => 'Demande de licence Microsoft 365',
                'description' => "Nouveau collaborateur en poste lundi. Il aura besoin d'une licence Microsoft 365 (Word, Excel, Teams). Merci de préparer le compte.",
                'status' => TicketStatus::Pending,
                'category' => 'IT',
                'priority' => 'Moyenne',
                'messages' => [
                    ['body' => "Nouveau collaborateur lundi 📅 Licence M365 (Word, Excel, Teams) à préparer. Merci !", 'type' => 'message'],
                    ['body' => "Demande bien reçue. Compte créé, licence assignée. Accès actif à partir de lundi 8h. 👌", 'type' => 'message'],
                ],
            ],
            [
                'subject' => 'Accès refusé à l\'application RH',
                'description' => "Je n'ai pas les droits pour accéder au module « Congés » dans l'app RH. Mon manager a validé ma demande d'accès la semaine dernière.",
                'status' => TicketStatus::InProgress,
                'category' => 'RH',
                'priority' => 'Moyenne',
                'messages' => [
                    ['body' => "Pas les droits pour le module Congés dans l'app RH. La demande de mon manager a été validée la semaine dernière. 🤔", 'type' => 'message'],
                    ['body' => "Vérification en cours avec l'équipe RH. On vous tient au courant rapidement.", 'type' => 'message'],
                ],
            ],
            [
                'subject' => 'Facture en double',
                'description' => "La facture #2024-0892 a été prélevée deux fois sur notre compte. Pouvez-vous vérifier et procéder au remboursement ?",
                'status' => TicketStatus::Open,
                'category' => 'Facturation',
                'priority' => 'Haute',
                'messages' => [
                    ['body' => "Facture #2024-0892 prélevée deux fois. Remboursement demandé. 📄", 'type' => 'message'],
                ],
            ],
            [
                'subject' => 'Problème d\'affichage sur le portail client',
                'description' => "Sur la page « Mon compte », les graphiques ne s'affichent pas correctement (bloc vide). Testé sur Chrome et Firefox.",
                'status' => TicketStatus::Resolved,
                'category' => 'Support',
                'priority' => 'Basse',
                'messages' => [
                    ['body' => "Les graphiques de la page « Mon compte » ne s'affichent pas (bloc vide). Chrome et Firefox. 🖥️", 'type' => 'message'],
                    ['body' => "Correction déployée. Un cache CDN était en cause. Rechargez la page en vidant le cache (Ctrl+F5). ✅", 'type' => 'message'],
                ],
            ],
        ];

        $creator = $users->first();
        $assignee = $users->skip(1)->first();

        foreach ($scenarios as $index => $scenario) {
            $category = $categories->firstWhere('name', $scenario['category']) ?? $categories->first();
            $priority = $priorities->firstWhere('name', $scenario['priority']) ?? $priorities->first();
            $ticket = Ticket::create([
                'organization_id' => $org->id,
                'created_by' => $creator->id,
                'ticket_category_id' => $category->id,
                'ticket_priority_id' => $priority->id,
                'assigned_to' => $assignee->id,
                'status' => $scenario['status'],
                'subject' => $scenario['subject'],
                'description' => $scenario['description'],
                'attachments' => $index === 0 ? [['name' => 'capture_vpn.png', 'size' => 245000, 'path' => 'tickets/demo/capture_vpn.png']] : null,
            ]);

            foreach ($scenario['messages'] as $i => $m) {
                $type = $m['type'] === 'internal_note' ? TicketMessageType::InternalNote : ($m['type'] === 'system' ? TicketMessageType::System : TicketMessageType::Message);
                $userId = $type === TicketMessageType::System ? null : ($i % 2 === 0 ? $creator->id : $assignee->id);
                $attachments = ($index === 0 && $i === 0) ? [['name' => 'capture_erreur.png', 'size' => 128000, 'path' => 'tickets/demo/capture_erreur.png']] : null;
                TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $userId,
                    'type' => $type,
                    'body' => $m['body'],
                    'attachments' => $attachments,
                ]);
            }
        }
    }

    private function ensureCategories(Organization $org)
    {
        $names = [
            ['name' => 'Support', 'slug' => 'support'],
            ['name' => 'IT', 'slug' => 'it'],
            ['name' => 'RH', 'slug' => 'rh'],
            ['name' => 'Facturation', 'slug' => 'facturation'],
        ];
        $categories = collect();
        foreach ($names as $n) {
            $categories->push(TicketCategory::firstOrCreate(
                ['organization_id' => $org->id, 'slug' => $n['slug']],
                ['name' => $n['name'], 'is_active' => true]
            ));
        }
        return $categories;
    }

    private function ensurePriorities(Organization $org)
    {
        $levels = [
            ['name' => 'Basse', 'level' => 1],
            ['name' => 'Moyenne', 'level' => 2],
            ['name' => 'Haute', 'level' => 3],
            ['name' => 'Urgente', 'level' => 4],
        ];
        $priorities = collect();
        foreach ($levels as $l) {
            $priorities->push(TicketPriority::firstOrCreate(
                ['organization_id' => $org->id, 'level' => $l['level']],
                ['name' => $l['name'], 'is_active' => true]
            ));
        }
        return $priorities;
    }
}
