<?php

namespace App\Console\Commands;

use App\Models\OrganizationMailbox;
use App\Services\Email\EmailParser;
use App\Services\Email\ImapService;
use App\Services\Email\InboundEmailService;
use App\Services\Email\MailErrorTranslator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchInboundEmails extends Command
{
    protected $signature = 'email:fetch
        {--mailbox= : ID of a specific mailbox to fetch}
        {--limit=50 : Max messages per mailbox}
        {--reset : Reset last_fetched_uid to re-fetch recent emails (last 7 days)}';

    protected $description = 'Fetch inbound emails from IMAP mailboxes and create/update tickets';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $mailboxId = $this->option('mailbox');
        $reset = $this->option('reset');

        $query = OrganizationMailbox::query()->where('is_active', true);
        if ($mailboxId) {
            $query->where('id', (int) $mailboxId);
        }

        $mailboxes = $query->get();

        if ($mailboxes->isEmpty()) {
            $this->info('No active mailboxes found.');

            return self::SUCCESS;
        }

        // Reset last_fetched_uid if requested
        if ($reset) {
            foreach ($mailboxes as $mailbox) {
                $oldUid = $mailbox->last_fetched_uid;
                $mailbox->update(['last_fetched_uid' => null]);
                $this->warn("Mailbox #{$mailbox->id} ({$mailbox->email}): last_fetched_uid reset ({$oldUid} → null). Will re-fetch last 7 days.");
            }
            $this->newLine();
        }

        $service = new InboundEmailService;
        $totalProcessed = 0;

        foreach ($mailboxes as $mailbox) {
            $this->info("Fetching from mailbox #{$mailbox->id} ({$mailbox->email})...");

            try {
                $messages = ImapService::fetchNewMessages($mailbox, $limit);
                $count = count($messages);
                $this->info("  Found {$count} new message(s).");

                $maxUid = $mailbox->last_fetched_uid;

                foreach ($messages as $imapMessage) {
                    try {
                        $parsed = EmailParser::parse($imapMessage);
                        $service->process($parsed, $mailbox);

                        $uid = $parsed->uid;
                        if ($uid && $uid > $maxUid) {
                            $maxUid = $uid;
                        }

                        $totalProcessed++;
                    } catch (\Throwable $e) {
                        Log::error('Failed to process email', [
                            'mailbox_id' => $mailbox->id,
                            'uid' => $imapMessage->getUid(),
                            'error' => $e->getMessage(),
                        ]);
                        $this->error("  Error processing UID {$imapMessage->getUid()}: {$e->getMessage()}");
                    }
                }

                // Update last fetch info
                $mailbox->update([
                    'last_fetched_at' => now(),
                    'last_fetched_uid' => $maxUid,
                    'last_error_at' => null,
                    'last_error_message' => null,
                ]);

            } catch (\Throwable $e) {
                Log::error('IMAP connection failed', [
                    'mailbox_id' => $mailbox->id,
                    'error' => $e->getMessage(),
                ]);

                $friendly = MailErrorTranslator::translate($e->getMessage(), 'IMAP', $mailbox->imap_port);
                $mailbox->update([
                    'last_error_at' => now(),
                    'last_error_message' => $friendly,
                ]);

                $this->error("  {$friendly}");
            }
        }

        $this->info("Done. Processed {$totalProcessed} email(s) total.");

        return self::SUCCESS;
    }
}
