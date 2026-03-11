<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ __('super_admin.profile.title') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('super_admin.profile.subtitle') }}</p>
    </div>

    @if (session('profile_status'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm flex items-center gap-3">
            <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
            {{ session('profile_status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- LEFT COLUMN -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Personal Info -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-base font-semibold text-slate-900">{{ __('pages.profile.personal_info_title') }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ __('pages.profile.personal_info_subtitle') }}</p>
                </div>
                <div class="p-6">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <!-- Organizations -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">{{ __('pages.profile.my_companies') }}</h2>
                        <p class="text-xs text-slate-500 mt-0.5">{{ __('pages.profile.companies_subtitle') }}</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid gap-3 sm:grid-cols-2">
                        @forelse ($this->organizations as $org)
                            <div class="group flex items-center gap-4 rounded-xl border border-slate-200 p-4 transition-all hover:border-[#005F02]/30 hover:bg-emerald-50/30">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-sm font-bold shadow-sm"
                                     style="background: {{ $org->primary_color ? 'color-mix(in srgb, '.$org->primary_color.' 15%, white)' : '#F3F4F6' }}; color: {{ $org->primary_color ?: '#4B5563' }};">
                                    {{ mb_strtoupper(mb_substr($org->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-slate-900 truncate">{{ $org->name }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                                        <iconify-icon icon="solar:shield-user-linear" width="12"></iconify-icon>
                                        {{ ucfirst((string) $org->pivot->role) }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 rounded-xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-500">
                                {{ __('pages.profile.no_organization') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="space-y-8">
            <!-- Language -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-4">{{ __('pages.profile.language') }}</h2>
                <div class="grid grid-cols-2 gap-2">
                    @php $currentLocale = strtoupper((string) app()->getLocale()); @endphp
                    <a href="{{ route('locale.switch', 'fr') }}"
                       class="flex items-center justify-center gap-2 rounded-xl border p-2 text-sm font-medium transition-all {{ $currentLocale === 'FR' ? 'border-[#005F02] bg-emerald-50/50 text-[#005F02]' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                        <span class="text-lg">🇫🇷</span> {{ __('pages.profile.french') }}
                    </a>
                    <a href="{{ route('locale.switch', 'en') }}"
                       class="flex items-center justify-center gap-2 rounded-xl border p-2 text-sm font-medium transition-all {{ $currentLocale === 'EN' ? 'border-[#005F02] bg-emerald-50/50 text-[#005F02]' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                        <span class="text-lg">🇬🇧</span> {{ __('pages.profile.english') }}
                    </a>
                </div>
            </div>

            <!-- Security -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-4">{{ __('pages.profile.security') }}</h2>
                <div class="space-y-4">
                    <div class="rounded-xl bg-slate-50 p-4 border border-slate-100">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">{{ __('pages.profile.active_sessions') }}</h3>
                        <div class="space-y-3">
                            @foreach ($this->sessions as $s)
                                @php
                                    $isCurrent = (string) $s->id === (string) $this->currentSessionId;
                                    $dt = \Illuminate\Support\Carbon::createFromTimestamp((int) $s->last_activity);
                                @endphp
                                <div class="flex items-start gap-3 text-xs">
                                    <div class="mt-0.5">
                                        @if($isCurrent)
                                            <div class="h-2 w-2 rounded-full bg-emerald-500 ring-2 ring-emerald-100"></div>
                                        @else
                                            <div class="h-2 w-2 rounded-full bg-slate-300"></div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-slate-900">
                                            {{ $s->ip_address ?? __('pages.profile.unknown_ip') }}
                                            @if($isCurrent) <span class="text-emerald-600 ml-1">({{ __('pages.profile.current_session') }})</span> @endif
                                        </p>
                                        <p class="text-slate-500 truncate">{{ $s->user_agent }}</p>
                                        <p class="text-slate-400 mt-0.5">{{ $dt->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <form method="POST" action="{{ route('profile.sessions.logout_all') }}" class="mt-4 pt-4 border-t border-slate-200">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-white border border-slate-200 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm">
                                {{ __('pages.profile.logout_other_sessions') }}
                            </button>
                        </form>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">{{ __('pages.profile.password') }}</h3>
                        <livewire:profile.update-password-form />
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 rounded-2xl border border-red-100 p-6">
                <h2 class="text-sm font-semibold text-red-900 mb-2">{{ __('pages.profile.danger_zone') }}</h2>
                <p class="text-xs text-red-700 mb-4">{{ __('pages.profile.danger_zone_text') }}</p>
                <div x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="text-xs font-bold text-red-600 hover:text-red-800 underline">
                        {{ __('pages.profile.delete_my_account') }}
                    </button>
                    <div x-show="open" x-collapse class="mt-4">
                        <livewire:profile.delete-user-form />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
