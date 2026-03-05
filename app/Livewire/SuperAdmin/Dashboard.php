<?php

namespace App\Livewire\SuperAdmin;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.super-admin', ['title' => 'super_admin.dashboard.title'])]
class Dashboard extends Component
{
    #[Computed]
    public function stats(): array
    {
        $orgCounts = DB::table('organizations')
            ->select([
                DB::raw("COUNT(*) as total"),
                DB::raw("COUNT(*) FILTER (WHERE status = 'active' OR status IS NULL) as active"),
                DB::raw("COUNT(*) FILTER (WHERE status = 'suspended') as suspended"),
                DB::raw("COUNT(*) FILTER (WHERE status = 'disabled') as disabled"),
            ])
            ->first();

        $totalUsers = DB::table('users')->count();

        $totalTickets = 0;
        $tickets7d = 0;
        $ticketsClosed7d = 0;
        try {
            $ticketStats = DB::table('tickets')
                ->select([
                    DB::raw('COUNT(*) as total'),
                    DB::raw("COUNT(*) FILTER (WHERE created_at >= NOW() - INTERVAL '7 days') as created_7d"),
                    DB::raw("COUNT(*) FILTER (WHERE closed_at IS NOT NULL AND closed_at >= NOW() - INTERVAL '7 days') as closed_7d"),
                ])
                ->first();
            $totalTickets = (int) $ticketStats->total;
            $tickets7d = (int) $ticketStats->created_7d;
            $ticketsClosed7d = (int) $ticketStats->closed_7d;
        } catch (\Throwable) {
        }

        $failedJobs = 0;
        $pendingJobs = 0;
        try {
            $failedJobs = DB::table('failed_jobs')->count();
        } catch (\Throwable) {
        }
        try {
            $pendingJobs = DB::table('jobs')->count();
        } catch (\Throwable) {
        }

        return [
            'total_orgs' => (int) $orgCounts->total,
            'active_orgs' => (int) $orgCounts->active,
            'suspended_orgs' => (int) $orgCounts->suspended,
            'disabled_orgs' => (int) $orgCounts->disabled,
            'total_users' => $totalUsers,
            'total_tickets' => $totalTickets,
            'tickets_7d' => $tickets7d,
            'tickets_closed_7d' => $ticketsClosed7d,
            'failed_jobs' => $failedJobs,
            'pending_jobs' => $pendingJobs,
        ];
    }

    #[Computed]
    public function activityChart(): array
    {
        $days = [];
        $created = [];
        $closed = [];

        try {
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->toDateString();
                $days[] = now()->subDays($i)->format('d/m');
                $created[$date] = 0;
                $closed[$date] = 0;
            }

            $createdRows = DB::table('tickets')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', now()->subDays(7)->startOfDay())
                ->groupBy(DB::raw('DATE(created_at)'))
                ->get();

            foreach ($createdRows as $row) {
                if (isset($created[$row->date])) {
                    $created[$row->date] = (int) $row->count;
                }
            }

            $closedRows = DB::table('tickets')
                ->select(DB::raw('DATE(closed_at) as date'), DB::raw('COUNT(*) as count'))
                ->whereNotNull('closed_at')
                ->where('closed_at', '>=', now()->subDays(7)->startOfDay())
                ->groupBy(DB::raw('DATE(closed_at)'))
                ->get();

            foreach ($closedRows as $row) {
                if (isset($closed[$row->date])) {
                    $closed[$row->date] = (int) $row->count;
                }
            }
        } catch (\Throwable) {
            $days = array_map(fn ($i) => now()->subDays(6 - $i)->format('d/m'), range(0, 6));
        }

        return [
            'labels' => $days,
            'created' => array_values($created),
            'closed' => array_values($closed),
        ];
    }

    #[Computed]
    public function recentActions(): array
    {
        return DB::table('super_admin_audit_logs')
            ->leftJoin('users', 'super_admin_audit_logs.user_id', '=', 'users.id')
            ->select('super_admin_audit_logs.*', 'users.name as user_name')
            ->orderByDesc('super_admin_audit_logs.created_at')
            ->limit(5)
            ->get()
            ->toArray();
    }

    #[Computed]
    public function todayHighlights(): array
    {
        $ticketsToday = 0;
        $closedToday = 0;
        try {
            $row = DB::table('tickets')
                ->select([
                    DB::raw("COUNT(*) FILTER (WHERE created_at >= CURRENT_DATE) as created"),
                    DB::raw("COUNT(*) FILTER (WHERE closed_at IS NOT NULL AND closed_at >= CURRENT_DATE) as closed"),
                ])
                ->first();
            $ticketsToday = (int) ($row->created ?? 0);
            $closedToday = (int) ($row->closed ?? 0);
        } catch (\Throwable) {
        }

        return [
            'tickets_created_today' => $ticketsToday,
            'tickets_closed_today' => $closedToday,
        ];
    }

    public function render()
    {
        return view('livewire.super-admin.dashboard');
    }
}
