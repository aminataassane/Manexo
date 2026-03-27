<?php

namespace App\Livewire\Dashboard;

use App\Helpers\CacheHelper;
use App\Livewire\Dashboard\Concerns\WithOrganization;
use App\Models\DiscussionThread;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DiscussionsPanel extends Component
{
    use WithOrganization;

    #[Computed]
    public function recentDiscussions(): \Illuminate\Support\Collection
    {
        $orgId = $this->orgId;
        if (! $orgId) {
            return collect();
        }

        return Cache::remember(CacheHelper::dashboardRecentDiscussionsKey($orgId), CacheHelper::TTL, function () use ($orgId) {
            $threads = DiscussionThread::query()
                ->where('organization_id', $orgId)
                ->active()
                ->with(['messages' => fn ($q) => $q->latest('created_at')->limit(1), 'messages.user:id,name'])
                ->orderByDesc('updated_at')
                ->limit(5)
                ->get();

            return $threads
                ->map(function (DiscussionThread $t) {
                    $last = $t->messages->first();

                    return (object) [
                        'id' => $t->id,
                        'name' => $t->name,
                        'last_body' => $last ? Str::limit($last->body, 50) : null,
                        'last_at' => $last?->created_at,
                        'last_user_name' => $last?->user?->name,
                        'url' => route('discussions.index', ['discussionParam' => 'd-'.$t->id]),
                    ];
                })
                ->sortByDesc('last_at')
                ->values();
        });
    }

    #[Computed]
    public function discussionsUnreadCount(): int
    {
        $orgId = $this->orgId;
        if (! $orgId) {
            return 0;
        }

        return Cache::remember(CacheHelper::dashboardDiscussionsUnreadKey($orgId), CacheHelper::TTL, function () use ($orgId) {
            return DiscussionThread::query()
                ->where('organization_id', $orgId)
                ->active()
                ->whereHas('messages', fn ($q) => $q->where('created_at', '>=', now()->subDay()))
                ->count();
        });
    }

    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 animate-pulse">
            <div class="h-4 bg-slate-200 rounded w-1/3 mb-4"></div>
            <div class="space-y-3">
                <div class="h-3 bg-slate-100 rounded w-full"></div>
                <div class="h-3 bg-slate-100 rounded w-5/6"></div>
                <div class="h-3 bg-slate-100 rounded w-4/6"></div>
            </div>
        </div>
        HTML;
    }

    public function render(): View
    {
        return view('livewire.dashboard.discussions-panel');
    }
}
