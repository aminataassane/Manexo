<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 antialiased selection:bg-[#F2E3BB] selection:text-[#005F02]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts / Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;family=Playfair+Display:ital,wght@0,400;0,600;1,400&amp;display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
        
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

    @vite(['resources/js/app.js']) <!-- Gardé pour Livewire/Alpine -->
    @livewireStyles
</head>
<body class="overflow-hidden flex items-center justify-center bg-slate-50 w-screen h-screen relative">

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

    <!-- 2. Central Card (Inject Slot Here) -->
    <div class="relative z-10 w-full max-w-[420px] px-4">
        
        <!-- Back Button (Fixed) -->
        <div class="fixed top-6 left-6 z-50 fade-in" style="animation-delay: 0.1s">
            <a href="{{ route('home') }}" wire:navigate class="group inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/50 border border-white/60 hover:bg-white hover:border-slate-200 transition-all duration-200 shadow-sm backdrop-blur-sm">
                <iconify-icon icon="solar:arrow-left-linear" class="text-slate-500 group-hover:text-[#005F02] transition-colors" width="14"></iconify-icon>
                <span class="text-[11px] font-medium text-slate-600 group-hover:text-slate-900">Retour à l'accueil</span>
            </a>
        </div>

        {{ $slot }}

        <p class="mt-8 text-center text-[10px] text-slate-400 tracking-wide font-medium">© {{ date('Y') }} {{ config('app.name') }} INC.</p>
    </div>

    @livewireScripts
</body>
</html>