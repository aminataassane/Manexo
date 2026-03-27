{{-- Skeleton de chargement de page (réutilisable) --}}
@props(['variant' => 'list'])

<div class="animate-pulse space-y-6">
    {{-- Header skeleton --}}
    <div class="flex items-center justify-between">
        <div class="space-y-2">
            <div class="h-6 bg-slate-200 rounded-lg w-48"></div>
            <div class="h-3 bg-slate-100 rounded w-64"></div>
        </div>
        <div class="flex gap-2">
            <div class="h-10 w-24 bg-slate-100 rounded-xl"></div>
            <div class="h-10 w-32 bg-slate-200 rounded-xl"></div>
        </div>
    </div>

    @if($variant === 'list')
        {{-- Stats skeleton --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @for($i = 0; $i < 4; $i++)
                <div class="rounded-xl border border-slate-100 bg-white p-4">
                    <div class="h-3 bg-slate-100 rounded w-16 mb-2"></div>
                    <div class="h-7 bg-slate-200 rounded w-12"></div>
                </div>
            @endfor
        </div>

        {{-- Table skeleton --}}
        <div class="rounded-2xl border border-slate-100 bg-white overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-3 flex gap-4">
                @for($i = 0; $i < 5; $i++)
                    <div class="h-3 bg-slate-100 rounded w-20"></div>
                @endfor
            </div>
            @for($i = 0; $i < 8; $i++)
                <div class="border-b border-slate-50 px-5 py-4 flex items-center gap-4">
                    <div class="h-4 w-4 bg-slate-100 rounded"></div>
                    <div class="h-4 bg-slate-{{ $i % 2 === 0 ? '200' : '100' }} rounded flex-1 max-w-xs"></div>
                    <div class="h-5 bg-slate-100 rounded-full w-20"></div>
                    <div class="h-3 bg-slate-100 rounded w-24 hidden sm:block"></div>
                    <div class="h-6 w-6 bg-slate-100 rounded-full"></div>
                </div>
            @endfor
        </div>

    @elseif($variant === 'editor')
        {{-- Editor 3-col skeleton --}}
        <div class="flex gap-0 rounded-2xl border border-slate-100 bg-white overflow-hidden" style="min-height: 60vh;">
            <div class="w-56 border-r border-slate-100 p-4 space-y-3 hidden lg:block">
                @for($i = 0; $i < 6; $i++)
                    <div class="h-8 bg-slate-100 rounded-lg"></div>
                @endfor
            </div>
            <div class="flex-1 p-8 space-y-4">
                <div class="h-6 bg-slate-200 rounded w-48 mb-6"></div>
                @for($i = 0; $i < 4; $i++)
                    <div class="rounded-xl border border-slate-100 p-4 space-y-2">
                        <div class="h-3 bg-slate-100 rounded w-24"></div>
                        <div class="h-9 bg-slate-50 rounded-lg"></div>
                    </div>
                @endfor
            </div>
            <div class="w-72 border-l border-slate-100 p-4 space-y-4 hidden lg:block">
                <div class="h-4 bg-slate-200 rounded w-32 mb-4"></div>
                @for($i = 0; $i < 5; $i++)
                    <div class="space-y-1.5">
                        <div class="h-3 bg-slate-100 rounded w-20"></div>
                        <div class="h-8 bg-slate-50 rounded-lg"></div>
                    </div>
                @endfor
            </div>
        </div>

    @elseif($variant === 'discussions')
        {{-- Discussions 2-col skeleton --}}
        <div class="flex gap-0 rounded-2xl border border-slate-100 bg-white overflow-hidden" style="min-height: 60vh;">
            <div class="w-80 border-r border-slate-100 p-3 space-y-2 hidden md:block">
                @for($i = 0; $i < 8; $i++)
                    <div class="flex items-center gap-3 p-3 rounded-xl">
                        <div class="h-10 w-10 bg-slate-100 rounded-full shrink-0"></div>
                        <div class="flex-1 space-y-1.5">
                            <div class="h-3 bg-slate-{{ $i === 0 ? '200' : '100' }} rounded w-3/4"></div>
                            <div class="h-2.5 bg-slate-100 rounded w-1/2"></div>
                        </div>
                    </div>
                @endfor
            </div>
            <div class="flex-1 p-8 flex items-center justify-center">
                <div class="text-center space-y-3">
                    <div class="h-16 w-16 bg-slate-100 rounded-2xl mx-auto"></div>
                    <div class="h-4 bg-slate-200 rounded w-40 mx-auto"></div>
                    <div class="h-3 bg-slate-100 rounded w-56 mx-auto"></div>
                </div>
            </div>
        </div>

    @elseif($variant === 'settings')
        {{-- Settings with left nav --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-3 space-y-2">
                @for($i = 0; $i < 8; $i++)
                    <div class="h-10 bg-slate-{{ $i === 0 ? '200' : '100' }} rounded-xl"></div>
                @endfor
            </div>
            <div class="lg:col-span-9 space-y-5">
                <div class="rounded-2xl border border-slate-100 bg-white p-6 space-y-4">
                    @for($i = 0; $i < 5; $i++)
                        <div class="space-y-1.5">
                            <div class="h-3 bg-slate-100 rounded w-24"></div>
                            <div class="h-10 bg-slate-50 rounded-lg"></div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    @endif
</div>
