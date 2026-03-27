<?php

namespace App\Services\Email;

use App\Models\InboundEmailLog;
use App\Models\OrganizationMailbox;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Message;

class ImapService
{
    /**
     * Fetch new messages from the mailbox (UID > last_fetched_uid).
     *
     * @return Message[]
     */
    public static function fetchNewMessages(OrganizationMailbox $mailbox, int $limit = 50): array
    {
        $client = self::connect($mailbox);

        try {
            $folder = $client->getFolder($mailbox->imap_folder ?: 'INBOX');

            $messages = null;

            if ($mailbox->last_fetched_uid) {
                // Try UID-based fetch first (most efficient)
                $startUid = ((int) $mailbox->last_fetched_uid) + 1;
                try {
                    $messages = $folder->messages()
                        ->where("CUSTOM UID {$startUid}:*")
                        ->leaveUnread()
                        ->setFetchOrder('asc')
                        ->limit($limit)
                        ->get();
                } catch (\Throwable $e) {
                    Log::warning('IMAP UID fetch failed, falling back to SINCE', [
                        'mailbox_id' => $mailbox->id,
                        'error' => $e->getMessage(),
                    ]);
                    $messages = null;
                }
            }

            // Fallback: date-based fetch (works on all IMAP servers)
            if ($messages === null || $messages->count() === 0) {
                $sinceDate = $mailbox->last_fetched_at
                    ? Carbon::parse($mailbox->last_fetched_at)->subMinutes(5)
                    : Carbon::now()->subDays(7);

                $messages = $folder->messages()
                    ->all()
                    ->since($sinceDate)
                    ->leaveUnread()
                    ->setFetchOrder('asc')
                    ->limit($limit)
                    ->get();
            }

            // Deduplicate: skip messages already processed (by message_id)
            $processedMessageIds = self::getProcessedMessageIds($mailbox);
            $filtered = [];
            foreach ($messages->all() as $message) {
                $msgId = $message->getMessageId()?->toString();
                if ($msgId && in_array($msgId, $processedMessageIds, true)) {
                    continue;
                }
                // Also skip messages with UID <= last_fetched_uid (dedup for SINCE fallback)
                $uid = $message->getUid();
                if ($mailbox->last_fetched_uid && $uid && $uid <= (int) $mailbox->last_fetched_uid) {
                    continue;
                }
                $filtered[] = $message;
            }

            return $filtered;
        } finally {
            $client->disconnect();
        }
    }

    /**
     * Get already-processed message IDs to avoid duplicates.
     */
    private static function getProcessedMessageIds(OrganizationMailbox $mailbox): array
    {
        return InboundEmailLog::query()
            ->where('organization_mailbox_id', $mailbox->id)
            ->whereNotNull('message_id')
            ->where('created_at', '>=', Carbon::now()->subDays(14))
            ->pluck('message_id')
            ->toArray();
    }

    /**
     * Test connection to an IMAP server.
     *
     * @return true|string True on success, error message on failure.
     */
    public static function testConnection(
        string $host,
        int $port,
        string $username,
        string $password,
        string $encryption,
        string $folder = 'INBOX',
    ): true|string {
        try {
            $cm = new ClientManager;
            $client = $cm->make([
                'host' => $host,
                'port' => $port,
                'encryption' => $encryption === 'none' ? false : $encryption,
                'validate_cert' => true,
                'username' => $username,
                'password' => $password,
                'protocol' => 'imap',
            ]);

            $client->connect();
            $client->getFolder($folder);
            $client->disconnect();

            return true;
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    private static function connect(OrganizationMailbox $mailbox): Client
    {
        $cm = new ClientManager;
        $client = $cm->make([
            'host' => $mailbox->imap_host,
            'port' => $mailbox->imap_port,
            'encryption' => $mailbox->imap_encryption === 'none' ? false : $mailbox->imap_encryption,
            'validate_cert' => true,
            'username' => $mailbox->imap_username,
            'password' => $mailbox->imap_password,
            'protocol' => 'imap',
        ]);

        $client->connect();

        return $client;
    }
}
