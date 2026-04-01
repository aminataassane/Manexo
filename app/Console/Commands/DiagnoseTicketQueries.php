<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Runs EXPLAIN (ANALYZE, BUFFERS) on representative ticketing queries for local diagnostics.
 * Pair with Laravel Debugbar / Telescope for full request timing.
 */
class DiagnoseTicketQueries extends Command
{
    protected $signature = 'manexo:diagnose-ticket-queries {--org=1 : Sample organization id}';

    protected $description = 'Print PostgreSQL query plans for common ticket list / message search patterns';

    public function handle(): int
    {
        if (! app()->environment('local', 'testing')) {
            $this->warn('Recommended for local/testing only. Use with caution on production.');
        }

        $orgId = (int) $this->option('org');
        $this->info('EXPLAIN (ANALYZE, BUFFERS) — organization_id='.$orgId);
        $this->newLine();

        $this->explain(
            'Tickets: org + not deleted + archived null (active list)',
            'SELECT id FROM tickets WHERE organization_id = ? AND deleted_at IS NULL AND archived_at IS NULL LIMIT 50',
            [$orgId]
        );

        $this->explain(
            'Ticket messages: ticket_id + id DESC (timeline cursor)',
            'SELECT id FROM ticket_messages WHERE ticket_id = (SELECT id FROM tickets WHERE organization_id = ? LIMIT 1) ORDER BY id DESC LIMIT 30',
            [$orgId]
        );

        $this->explain(
            'Ticket messages: body ILIKE (deep search — uses idx_ticket_messages_body_trgm when migration applied)',
            'SELECT ticket_id FROM ticket_messages WHERE body ILIKE ? LIMIT 20',
            ['%test%']
        );

        $this->explain(
            'Ticket assignees: EXISTS join (member scope)',
            'SELECT t.id FROM tickets t WHERE t.organization_id = ? AND EXISTS (SELECT 1 FROM ticket_assignees ta WHERE ta.ticket_id = t.id AND ta.user_id = 1) LIMIT 50',
            [$orgId]
        );

        $this->info('Discussion page focused plans');
        $this->newLine();

        $this->explain(
            'Discussion: ticket by PK + org scope',
            'SELECT id, public_id, organization_id, created_by, ticket_priority_id, ticket_group_id, status, subject, description FROM tickets WHERE id = (SELECT id FROM tickets WHERE organization_id = ? LIMIT 1) AND organization_id = ? LIMIT 1',
            [$orgId, $orgId]
        );

        $this->explain(
            'Discussion timeline: newest 31 messages',
            'SELECT id, ticket_id, user_id, type, body, attachments, meta, email_message_id, created_at FROM ticket_messages WHERE ticket_id = (SELECT id FROM tickets WHERE organization_id = ? LIMIT 1) ORDER BY id DESC LIMIT 31',
            [$orgId]
        );

        $this->explain(
            'Discussion sidebar: all message attachments only',
            'SELECT attachments FROM ticket_messages WHERE ticket_id = (SELECT id FROM tickets WHERE organization_id = ? LIMIT 1) AND attachments IS NOT NULL',
            [$orgId]
        );

        $this->explain(
            'Discussion sidebar: last activity MAX(created_at)',
            'SELECT MAX(created_at) FROM ticket_messages WHERE ticket_id = (SELECT id FROM tickets WHERE organization_id = ? LIMIT 1)',
            [$orgId]
        );

        $this->newLine();
        $this->comment('Tip: install barryvdh/laravel-debugbar or laravel/telescope for per-request HTTP profiling.');

        return self::SUCCESS;
    }

    private function explain(string $label, string $sql, array $bindings): void
    {
        $this->line('<fg=cyan>'.$label.'</>');
        try {
            $rows = DB::select('EXPLAIN (ANALYZE, BUFFERS) '.$sql, $bindings);
            foreach ($rows as $row) {
                $arr = (array) $row;
                $this->line('  '.(string) reset($arr));
            }
        } catch (\Throwable $e) {
            $this->error('  '.$e->getMessage());
        }
        $this->newLine();
    }
}
