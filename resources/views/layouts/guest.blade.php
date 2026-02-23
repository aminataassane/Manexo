<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 antialiased selection:bg-[#F2E3BB] selection:text-[#005F02]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Manexo') }}</title>

    <!-- Scripts / Styles -->
    <script defer src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet"></noscript>
    
    <style>
        body, .font-serif {
            font-family: "Mona Sans", sans-serif;
            font-optical-sizing: auto;
            font-style: normal;
            font-variation-settings: "wdth" 100;
        }
        
        /* Smooth Floating Animations */
        .animate-float-slow { animation: float 8s ease-in-out infinite; }
        .animate-float-medium { animation: float 6s ease-in-out infinite; animation-delay: 1s; }
        .animate-float-fast { animation: float 5s ease-in-out infinite; animation-delay: 2s; }
        
        @keyframes float { 
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        /* Subtle Entrance */
        .fade-in { animation: fadeIn 0.6s ease-out forwards; opacity: 0; transform: translateY(10px); }
        @keyframes fadeIn { to { opacity: 1; transform: translateY(0); } }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="overflow-x-hidden flex items-center justify-center bg-slate-50 w-full min-h-screen min-h-[100dvh] relative py-6 px-4 sm:py-8" style="padding-left: max(1rem, env(safe-area-inset-left)); padding-right: max(1rem, env(safe-area-inset-right)); padding-top: max(1.5rem, env(safe-area-inset-top)); padding-bottom: max(1.5rem, env(safe-area-inset-bottom));">

    <!-- 1. Background Universe -->
    <div class="opacity-40 z-0 absolute top-0 right-0 bottom-0 left-0"></div>
    
    <!-- Ambient Glows -->
    <div class="-ml-20 -mt-20 bg-[#bbf7d0] opacity-20 w-96 h-96 rounded-full absolute top-0 left-0 blur-3xl"></div>
    <div class="-mr-20 -mb-20 bg-[#F2E3BB] opacity-30 w-96 h-96 rounded-full absolute right-0 bottom-0 blur-3xl"></div>

    <!-- Floating UI Elements -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none hidden md:block">
        <!-- Ticket Card -->
        <div class="absolute top-[15%] left-[10%] w-64 animate-float-slow opacity-80 blur-[0.5px]">
            <div class="rounded-xl border border-white/60 bg-white/70 backdrop-blur-sm p-4 shadow-lg">
                <div class="flex items-center justify-between mb-3">
                    <span class="rounded bg-green-100 px-2 py-0.5 text-[10px] font-medium text-green-700">#1245</span>
                    <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                </div>
                <div class="space-y-2">
                    <div class="h-2 w-3/4 rounded bg-slate-200"></div>
                    <div class="h-2 w-1/2 rounded bg-slate-200"></div>
                </div>
            </div>
        </div>

        <!-- Status Notification -->
        <div class="absolute top-[20%] right-[12%] animate-float-fast opacity-80 blur-[0.5px]">
            <div class="flex items-center gap-3 rounded-lg border border-white/60 bg-white/70 backdrop-blur-sm px-4 py-3 shadow-lg">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#005F02]/10 text-[#005F02]">
                    <iconify-icon icon="solar:check-circle-linear" stroke-width="1.5"></iconify-icon>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-800">Système opérationnel</div>
                    <div class="text-[10px] text-slate-500">Tous les services actifs</div>
                </div>
            </div>
        </div>

        <!-- Mini Graph -->
        <div class="absolute bottom-[15%] left-[15%] animate-float-medium opacity-70 blur-[0.5px]">
            <div class="rounded-xl border border-white/60 bg-white/70 backdrop-blur-sm p-4 shadow-lg w-56">
                <div class="flex items-end gap-2 h-16 justify-between px-2">
                    <div class="w-2 bg-[#005F02]/20 rounded-t h-8"></div>
                    <div class="w-2 bg-[#005F02]/30 rounded-t h-12"></div>
                    <div class="w-2 bg-[#005F02]/60 rounded-t h-10"></div>
                    <div class="w-2 bg-[#005F02] rounded-t h-14"></div>
                    <div class="w-2 bg-[#005F02]/40 rounded-t h-6"></div>
                </div>
            </div>
        </div>

        <!-- User Pill -->
        <div class="absolute bottom-[20%] right-[15%] animate-float-slow opacity-80 blur-[0.5px]">
            <div class="flex items-center gap-3 rounded-full border border-white/60 bg-white/70 backdrop-blur-sm pl-1 pr-4 py-1 shadow-lg">
                <div class="h-8 w-8 rounded-full bg-[#F2E3BB] flex items-center justify-center text-[#005F02] text-xs font-bold border border-white">S</div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-slate-700">Sophie D.</span>
                    <span class="text-[9px] text-green-600 font-medium">Connectée</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Central Card (responsive width: mobile full, desktop/TV capped) -->
    <div class="relative z-10 w-full min-w-0 max-w-[420px] sm:max-w-[440px] min-[1920px]:max-w-[480px] px-0 sm:px-4">
        
        <!-- Back Button (Fixed, safe-area aware) -->
        <div class="fixed z-50 fade-in top-4 left-4 sm:top-6 sm:left-6 min-[1920px]:top-8 min-[1920px]:left-8" style="animation-delay: 0.1s; top: max(1rem, env(safe-area-inset-top)); left: max(1rem, env(safe-area-inset-left));">
            <a href="{{ route('home') }}" class="group inline-flex items-center gap-2 px-3 py-2.5 sm:py-1.5 min-h-[44px] sm:min-h-0 rounded-full bg-white/50 border border-white/60 hover:bg-white hover:border-slate-200 transition-all duration-200 shadow-sm backdrop-blur-sm">
                <iconify-icon icon="solar:arrow-left-linear" class="text-slate-500 group-hover:text-[#005F02] transition-colors" width="14"></iconify-icon>
                <span class="text-[11px] font-medium text-slate-600 group-hover:text-slate-900">{{ __('Back') }}</span>
            </a>
        </div>
        <!-- Language switcher (guest) -->
        <div class="fixed z-50 fade-in top-4 right-4 sm:top-6 sm:right-6 min-[1920px]:top-8 min-[1920px]:right-8 flex items-center gap-1 rounded-full bg-white/50 border border-white/60 px-2 py-1.5 shadow-sm backdrop-blur-sm" style="top: max(1rem, env(safe-area-inset-top)); right: max(1rem, env(safe-area-inset-right));">
            <a href="{{ route('locale.switch', 'fr') }}" class="rounded-full px-2.5 py-1 text-[11px] font-semibold transition-colors {{ app()->getLocale() === 'fr' ? 'bg-[#005F02]/15 text-[#005F02]' : 'text-slate-500 hover:text-slate-800' }}">{{ __('French') }}</a>
            <span class="text-slate-300">|</span>
            <a href="{{ route('locale.switch', 'en') }}" class="rounded-full px-2.5 py-1 text-[11px] font-semibold transition-colors {{ app()->getLocale() === 'en' ? 'bg-[#005F02]/15 text-[#005F02]' : 'text-slate-500 hover:text-slate-800' }}">{{ __('English') }}</a>
        </div>

        {{ $slot }}

        <p class="mt-8 text-center text-[10px] text-slate-400 tracking-wide font-medium">© {{ date('Y') }} {{ config('app.name', 'Manexo') }} INC.</p>
    </div>

    <script>if(!window.Echo){var _c={listen:function(){return _c},stopListening:function(){return _c},notification:function(){return _c},listenForWhisper:function(){return _c},subscribed:function(){return _c},error:function(){return _c}};window.Echo={private:function(){return _c},channel:function(){return _c},encryptedPrivate:function(){return _c},join:function(){return _c},leave:function(){},leaveChannel:function(){},leaveAllChannels:function(){},socketId:function(){return null},connector:{pusher:{connection:{state:"stub"}}}}}</script>
    @livewireScripts
</body>
</html>