<?php

namespace App\Console\Commands;

use App\Enums\TicketSource;
use App\Models\InboundEmailLog;
use App\Models\OrganizationMembership;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurgeSpamTickets extends Command
{
    protected $signature = 'email:purge-spam
        {--dry-run : Show what would be deleted without actually deleting}
        {--org= : Limit to a specific organization ID}';

    protected $description = 'Purge spam/newsletter/promo tickets created from inbound emails';

    /** Known marketing/spam domains — emails from these domains create spam tickets */
    private const SPAM_DOMAINS = [
        // E-commerce / Fast fashion
        'shein.com', 'mail.shein.com', 'sheingroup.com',
        'aliexpress.com', 'mail.aliexpress.com',
        'wish.com', 'temu.com', 'mail.temu.com',
        'amazon.com', 'amazon.fr', 'amazonses.com',
        'ebay.com', 'ebay.fr',
        // Social media
        'facebookmail.com', 'facebook.com',
        'instagram.com', 'mail.instagram.com',
        'twitter.com', 'x.com',
        'linkedin.com', 'e.linkedin.com',
        'tiktok.com', 'mail.tiktok.com',
        'pinterest.com',
        'snapchat.com',
        // Marketing platforms
        'mailchimp.com', 'mail.mailchimp.com',
        'sendinblue.com', 'brevo.com',
        'sendgrid.net', 'sendgrid.com',
        'mailgun.org', 'mailgun.com',
        'mailjet.com',
        'hubspot.com', 'hubspotemail.net',
        'constantcontact.com',
        'campaignmonitor.com',
        'klaviyo.com',
        'activecampaign.com',
        // Services / SaaS notifications
        'spotify.com',
        'netflix.com',
        'apple.com', 'email.apple.com',
        'google.com', 'accounts.google.com',
        'microsoft.com', 'outlook.com',
        'paypal.com', 'paypal.fr',
        'uber.com',
        'booking.com',
        'airbnb.com',
        // Telecom / ISP
        'orange.fr', 'sfr.fr', 'free.fr', 'bouyguestelecom.fr',
        // Banks
        'bnpparibas.com', 'societegenerale.fr', 'credit-agricole.fr',
        'lcl.fr', 'labanquepostale.fr',
        // Newsletter platforms
        'substack.com', 'substackmail.com',
        'medium.com',
        'revue.email',
    ];

    /** Subject patterns typical of spam/marketing emails */
    private const SPAM_SUBJECT_PATTERNS = [
        '/\b\d+%\s*(de\s+)?(réduction|remise|off|discount)/iu',
        '/\b(soldes|promo|promotion|offre|deal|bon\s+plan|vente\s+flash|flash\s+sale|black\s+friday|cyber\s+monday)\b/iu',
        '/\b(newsletter|unsubscribe|désabonne|se\s+désinscrire)\b/iu',
        '/\b(livraison\s+gratuite|free\s+shipping|frais\s+de\s+port\s+offerts)\b/iu',
        '/\b(code\s+promo|coupon|voucher|bon\s+de\s+réduction)\b/iu',
        '/\b(dernière\s+chance|last\s+chance|ne\s+ratez\s+pas|don\'?t\s+miss)\b/iu',
        '/\b(offre\s+exclusive|exclusive\s+offer|limited\s+time|temps\s+limité|durée\s+limitée)\b/iu',
        '/\b(best\s*seller|tendance|nouvelle\s+collection|new\s+arrival)\b/iu',
        '/ça\s+ne\s+durera\s+pas/iu',
        '/dès\s+\d+[€$£]\s+d\'?achat/iu',
    ];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $orgId = $this->option('org');

        if ($dryRun) {
            $this->warn('=== MODE DRY-RUN — Aucune suppression ne sera effectuée ===');
        }

        // Find email-sourced tickets
        $query = Ticket::query()
            ->where('source', TicketSource::Email)
            ->with('creator');

        if ($orgId) {
            $query->where('organization_id', (int) $orgId);
        }

        $tickets = $query->get();
        $this->info("Tickets email trouvés : {$tickets->count()}");

        $toDelete = collect();

        foreach ($tickets as $ticket) {
            $reason = $this->isSpamTicket($ticket);
            if ($reason) {
                $toDelete->push(['ticket' => $ticket, 'reason' => $reason]);
            }
        }

        if ($toDelete->isEmpty()) {
            $this->info('Aucun ticket spam détecté.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->warn("Tickets spam détectés : {$toDelete->count()}");
        $this->newLine();

        // Display table
        $rows = $toDelete->map(fn ($item) => [
            $item['ticket']->id,
            $item['ticket']->public_id,
            \Illuminate\Support\Str::limit($item['ticket']->subject, 50),
            $item['ticket']->creator?->email ?? 'N/A',
            $item['reason'],
            $item['ticket']->created_at->format('d/m/Y H:i'),
        ]);

        $this->table(
            ['ID', 'Public ID', 'Sujet', 'Email expéditeur', 'Raison', 'Créé le'],
            $rows->toArray()
        );

        if ($dryRun) {
            $this->warn('Dry-run terminé. Relancez sans --dry-run pour supprimer.');

            return self::SUCCESS;
        }

        if (! $this->confirm("Supprimer ces {$toDelete->count()} tickets spam et leurs données ?")) {
            $this->info('Annulé.');

            return self::SUCCESS;
        }

        $deletedCount = 0;
        $guestsCleaned = 0;

        foreach ($toDelete as $item) {
            $ticket = $item['ticket'];

            DB::transaction(function () use ($ticket, &$deletedCount, &$guestsCleaned) {
                $ticketId = $ticket->id;
                $creatorId = $ticket->created_by;
                $orgId = $ticket->organization_id;

                // Delete attachments from storage
                $dir = "ticket-attachments/org-{$orgId}/ticket-{$ticketId}";
                if (Storage::disk('local')->exists($dir)) {
                    Storage::disk('local')->deleteDirectory($dir);
                }

                // Delete messages
                TicketMessage::where('ticket_id', $ticketId)->delete();

                // Delete participants & assignees (pivot tables)
                DB::table('ticket_participants')->where('ticket_id', $ticketId)->delete();
                DB::table('ticket_assignees')->where('ticket_id', $ticketId)->delete();

                // Delete checklist items
                DB::table('ticket_checklist_items')->where('ticket_id', $ticketId)->delete();

                // Update inbound email logs (keep for audit, remove ticket reference)
                InboundEmailLog::where('ticket_id', $ticketId)->update([
                    'ticket_id' => null,
                    'ticket_message_id' => null,
                    'metadata' => DB::raw("jsonb_set(COALESCE(metadata, '{}')::jsonb, '{purged}', 'true')"),
                ]);

                // Hard-delete the ticket (not soft-delete, it's spam)
                $ticket->forceDelete();
                $deletedCount++;

                // Clean up guest user if they have no other tickets
                if ($creatorId) {
                    $creator = User::find($creatorId);
                    if ($creator && $creator->status === 'guest') {
                        $hasOtherTickets = Ticket::withTrashed()
                            ->where('created_by', $creatorId)
                            ->exists();

                        if (! $hasOtherTickets) {
                            // Remove membership
                            OrganizationMembership::where('user_id', $creatorId)
                                ->where('organization_id', $orgId)
                                ->delete();

                            // Delete guest if no memberships left
                            $hasMemberships = OrganizationMembership::where('user_id', $creatorId)->exists();
                            if (! $hasMemberships) {
                                $creator->delete();
                                $guestsCleaned++;
                            }
                        }
                    }
                }
            });
        }

        $this->newLine();
        $this->info('Terminé :');
        $this->info("  - {$deletedCount} tickets spam supprimés");
        $this->info("  - {$guestsCleaned} utilisateurs guest orphelins nettoyés");

        return self::SUCCESS;
    }

    private function isSpamTicket(Ticket $ticket): ?string
    {
        $creatorEmail = $ticket->creator?->email ?? '';
        $subject = $ticket->subject ?? '';
        $description = $ticket->description ?? '';

        // Check creator email domain
        $domain = strtolower(substr(strrchr($creatorEmail, '@'), 1));
        foreach (self::SPAM_DOMAINS as $spamDomain) {
            if ($domain === $spamDomain || str_ends_with($domain, '.'.$spamDomain)) {
                return "domaine: {$domain}";
            }
        }

        // Check subject patterns
        foreach (self::SPAM_SUBJECT_PATTERNS as $pattern) {
            if (preg_match($pattern, $subject)) {
                return 'sujet: '.\Illuminate\Support\Str::limit($subject, 40);
            }
        }

        // Check for MIME-encoded subjects that were never decoded (legacy)
        if (preg_match('/^=\?[^?]+\?[BQ]\?/i', $subject)) {
            return 'sujet MIME non décodé';
        }

        // Check for HTML-heavy descriptions (typical of marketing emails)
        if (strlen($description) > 2000) {
            $htmlTagCount = preg_match_all('/<[a-z][^>]*>/i', $description);
            $linkCount = preg_match_all('/https?:\/\//i', $description);
            if ($htmlTagCount > 20 || $linkCount > 10) {
                return "contenu HTML marketing ({$htmlTagCount} tags, {$linkCount} liens)";
            }
        }

        // Check for tracking pixels / 1x1 images
        if (preg_match('/width=["\']?1["\']?\s+height=["\']?1["\']?/i', $description) ||
            preg_match('/height=["\']?1["\']?\s+width=["\']?1["\']?/i', $description)) {
            return 'pixel de tracking détecté';
        }

        return null;
    }
}
