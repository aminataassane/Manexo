<?php

namespace App\Livewire\SuperAdmin;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.super-admin', ['title' => 'super_admin.notifications.title'])]
class NotificationsGlobal extends Component
{
    #[Computed]
    public function stats(): array
    {
        $counts = DB::table('notifications')
            ->select([
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(*) FILTER (WHERE read_at IS NOT NULL) as read'),
                DB::raw('COUNT(*) FILTER (WHERE read_at IS NULL) as unread'),
                DB::raw("COUNT(*) FILTER (WHERE created_at >= NOW() - INTERVAL '24 hours') as last_24h"),
                DB::raw("COUNT(*) FILTER (WHERE created_at >= NOW() - INTERVAL '7 days') as last_7d"),
            ])
            ->first();

        return [
            'total' => (int) $counts->total,
            'read' => (int) $counts->read,
            'unread' => (int) $counts->unread,
            'last_24h' => (int) $counts->last_24h,
            'last_7d' => (int) $counts->last_7d,
        ];
    }

    #[Computed]
    public function byType(): array
    {
        return DB::table('notifications')
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->orderByDesc('count')
            ->get()
            ->toArray();
    }

    #[Computed]
    public function recentNotifications(): array
    {
        return DB::table('notifications')
            ->leftJoin('users', 'notifications.notifiable_id', '=', 'users.id')
            ->select('notifications.*', 'users.name as user_name')
            ->where('notifications.notifiable_type', 'App\\Models\\User')
            ->orderByDesc('notifications.created_at')
            ->limit(20)
            ->get()
            ->map(function ($n) {
                $n->data = json_decode($n->data, true);
                return $n;
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.super-admin.notifications-global');
    }
}
