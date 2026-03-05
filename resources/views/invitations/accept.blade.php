<x-guest-layout>
    <div class="fade-in shadow-slate-200/50 sm:p-6 sm:w-[120%] sm:-ml-[10%] bg-white w-full border-slate-100 border rounded-xl p-5 shadow-2xl">

        <!-- Header -->
        <div class="text-center mb-4">
            <div class="inline-flex items-center gap-1.5 mb-2 group cursor-pointer">
                <div class="relative flex items-center justify-center h-7 w-7 rounded-md bg-[#005F02]/5">
                    <iconify-icon icon="solar:layers-linear" class="text-[#005F02] text-base transition-transform group-hover:rotate-180 duration-700" stroke-width="1.5"></iconify-icon>
                </div>
                <span class="text-base font-semibold tracking-tight text-[#002e01]">{{ config('app.name', 'Manexo') }}</span>
            </div>
        </div>

        @if(!empty($error))
            <!-- Error state -->
            <div class="text-center py-4">
                <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-red-50 mb-3">
                    <iconify-icon icon="solar:danger-triangle-bold-duotone" width="24" class="text-red-500"></iconify-icon>
                </div>
                <h1 class="font-serif text-lg font-medium tracking-tight text-slate-900 mb-2">{{ __('invitations.accept_title') }}</h1>
                <p class="text-sm text-slate-600">{{ $error }}</p>

                @if(!empty($showLogout))
                    <div class="mt-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 transition-colors">
                                <iconify-icon icon="solar:logout-2-linear" width="16"></iconify-icon>
                                {{ __('invitations.logout_and_login') }}
                            </button>
                        </form>
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-[#005F02] hover:underline">{{ __('Back') }}</a>
                </div>
            </div>
        @elseif(!empty($invitation))
            <!-- Accept state -->
            <div class="text-center py-2">
                <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-green-50 mb-3">
                    <iconify-icon icon="solar:letter-bold-duotone" width="24" class="text-green-600"></iconify-icon>
                </div>
                <h1 class="font-serif text-lg font-medium tracking-tight text-[#002e01] mb-2">{{ __('invitations.accept_title') }}</h1>
                <p class="text-sm text-slate-600 mb-1">
                    {{ __('invitations.accept_desc', ['org' => $invitation->organization->name, 'role' => $invitation->role]) }}
                </p>
                @if($invitation->inviter)
                    <p class="text-xs text-slate-400">{{ __('invitations.invited_by', ['name' => $invitation->inviter->name]) }}</p>
                @endif
            </div>

            <form method="POST" action="{{ route('invitations.process-accept', ['token' => $invitation->token]) }}" class="mt-4">
                @csrf
                <button type="submit" class="group flex w-full items-center justify-center gap-2 rounded-lg bg-[#005F02] px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-[#004d02] hover:shadow-lg hover:shadow-[#005F02]/20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#005F02] transition-all duration-200 active:scale-[0.98]">
                    {{ __('invitations.accept_button') }}
                    <iconify-icon icon="solar:arrow-right-linear" class="text-white text-sm transition-transform group-hover:translate-x-1" stroke-width="1.5"></iconify-icon>
                </button>
            </form>
        @endif
    </div>
</x-guest-layout>
