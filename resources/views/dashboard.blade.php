<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - MANEXO</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }

        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #E5E7EB; border-radius: 20px; }

        .cursor-blink { animation: blink 1s step-end infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }

        /* Organization accent color (CSS variable: --accent) */
        ::selection { background: #F2E3BB; color: var(--accent); }

        .accent-ring-soft {
            --tw-ring-color: rgba(0, 95, 2, 0.10);
            --tw-ring-color: color-mix(in srgb, var(--accent) 10%, transparent);
        }
        .accent-focus:focus-within {
            --tw-ring-color: rgba(0, 95, 2, 0.22);
            --tw-ring-color: color-mix(in srgb, var(--accent) 22%, transparent);
            border-color: var(--accent);
        }
        .accent-hover-soft:hover {
            background-color: rgba(0, 95, 2, 0.06);
            background-color: color-mix(in srgb, var(--accent) 8%, transparent);
        }
    </style>
</head>
@php
    $accent = ($currentOrganization?->primary_color ?? '#005F02');
@endphp
<body class="flex h-screen w-full flex-col overflow-hidden bg-white text-slate-800 antialiased" style="--accent: {{ $accent }};">

    <!-- Top Bar -->
    <div class="flex h-14 shrink-0 items-center justify-between border-b border-white/10 bg-[#002e01] px-4 text-white">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="flex h-8 w-8 items-center justify-center rounded bg-gradient-to-br from-[#F2E3BB] to-[#d6c79e] text-[#002e01] shadow-sm hover:opacity-90 transition-opacity">
                <span class="font-serif font-bold">M</span>
            </a>
        </div>

        <div class="flex w-96 items-center gap-2 rounded-md bg-white/10 px-3 py-1.5 text-xs text-white/60 ring-1 ring-white/10 focus-within:ring-[#F2E3BB] focus-within:bg-white/15 transition-all">
            <iconify-icon icon="solar:magnifer-linear" class="text-base"></iconify-icon>
            <input type="text" placeholder="Rechercher..." class="bg-transparent w-full border-none outline-none placeholder-white/40 text-white focus:ring-0">
        </div>

        <div class="flex items-center gap-4">
            <div class="relative group">
                <iconify-icon icon="solar:bell-linear" class="text-xl text-white/60 group-hover:text-white cursor-pointer transition-colors"></iconify-icon>
                <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500 border border-[#002e01]"></span>
            </div>

            <!-- User Profile Link -->
            <a href="{{ route('profile') }}" class="flex items-center gap-2 cursor-pointer hover:bg-white/5 rounded-full pr-3 pl-1 py-1 transition-colors">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff" alt="Profile" class="h-7 w-7 rounded-full border border-white/20">
                <span class="text-xs font-medium hidden sm:block">{{ Auth::user()->name }}</span>
            </a>
        </div>
    </div>

    <!-- Interface -->
    <div class="flex flex-1 overflow-hidden">

        <!-- Sidebar -->
        <div class="flex w-16 flex-col items-center border-r border-white/10 bg-[#002e01] py-4 gap-6 z-10">
            <a href="{{ route('tickets.index') }}" class="group flex h-10 w-10 cursor-pointer items-center justify-center rounded-lg bg-[#F2E3BB]/10 text-[#F2E3BB] transition-all hover:bg-[#F2E3BB] hover:text-[#002e01]" title="Tickets">
                <iconify-icon icon="solar:inbox-line-linear" class="text-xl"></iconify-icon>
            </a>
            <a href="#" class="group flex h-10 w-10 cursor-pointer items-center justify-center rounded-lg text-white/40 transition-all hover:bg-white/10 hover:text-[#F2E3BB]" title="Tâches">
                <iconify-icon icon="solar:checklist-minimalistic-linear" class="text-xl"></iconify-icon>
            </a>
            <a href="#" class="group flex h-10 w-10 cursor-pointer items-center justify-center rounded-lg text-white/40 transition-all hover:bg-white/10 hover:text-[#F2E3BB]" title="Équipe">
                <iconify-icon icon="solar:users-group-rounded-linear" class="text-xl"></iconify-icon>
            </a>
            <a href="#" class="group flex h-10 w-10 cursor-pointer items-center justify-center rounded-lg text-white/40 transition-all hover:bg-white/10 hover:text-[#F2E3BB]" title="Rapports">
                <iconify-icon icon="solar:graph-up-linear" class="text-xl"></iconify-icon>
            </a>

            <div class="mt-auto">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="group flex h-10 w-10 cursor-pointer items-center justify-center rounded-lg text-white/40 transition-all hover:bg-red-500/10 hover:text-red-400" title="Déconnexion">
                        <iconify-icon icon="solar:logout-2-linear" class="text-xl"></iconify-icon>
                    </button>
                </form>
            </div>
        </div>

        <!-- Ticket List (Static Mockup for now, as requested "visual dashboard") -->
        <div class="flex w-80 flex-col border-r border-slate-200 bg-slate-50/50 hidden md:flex">
            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                <div class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-tight">
                    Vues Tickets
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] text-slate-400">En direct</span>
                    <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
                </div>
            </div>

            <!-- Ticket Items -->
            <div class="flex-1 overflow-y-auto px-2 py-2 space-y-2 custom-scrollbar">
                <!-- Active Ticket -->
                <div class="group cursor-pointer rounded-lg border bg-white p-3 shadow-md ring-1 accent-ring-soft relative overflow-hidden transition-all hover:-translate-y-0.5" style="border-color: color-mix(in srgb, var(--accent) 20%, transparent);">
                    <div class="absolute left-0 top-0 bottom-0 w-1" style="background-color: var(--accent);"></div>
                    <div class="flex justify-between mb-1 pl-2">
                        <div class="flex items-center gap-1">
                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                            <span class="text-[10px] text-slate-400 font-medium">En cours</span>
                        </div>
                        <span class="text-[10px] text-slate-400">12m</span>
                    </div>
                    <h4 class="text-sm font-semibold text-slate-900 line-clamp-1 pl-2">Erreur 504 Gateway Timeout</h4>
                    <p class="text-[11px] text-slate-500 pl-2 mt-1 line-clamp-1">Le serveur ne répond pas lors de la requête API...</p>
                    <div class="mt-2 flex items-center gap-2 pl-2">
                        <div class="flex items-center gap-1 text-[10px] font-medium text-[color:var(--accent)] bg-[#F2E3BB]/30 px-1.5 py-0.5 rounded">
                            OPS-102
                        </div>
                        <div class="ml-auto flex -space-x-1">
                            <div class="h-5 w-5 rounded-full bg-slate-200 border border-white"></div>
                        </div>
                    </div>
                </div>

                <!-- Other Tickets -->
                <div class="group cursor-pointer rounded-lg border border-transparent bg-white/50 p-3 hover:bg-white hover:border-slate-200 hover:shadow-sm transition-all duration-200">
                    <div class="flex justify-between mb-1">
                        <span class="text-[10px] text-slate-400">2h</span>
                    </div>
                    <h4 class="text-sm font-medium text-slate-700 group-hover:text-[color:var(--accent)] transition-colors">Problème d'authentification SSO</h4>
                    <p class="text-[11px] text-slate-500 mt-1 line-clamp-1">Impossible de se connecter via Okta ce matin.</p>
                    <div class="mt-2 flex items-center gap-2">
                        <div class="flex items-center gap-1 text-[10px] text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                            APPS-216
                        </div>
                    </div>
                </div>

                <div class="group cursor-pointer rounded-lg border border-transparent bg-white/50 p-3 hover:bg-white hover:border-slate-200 hover:shadow-sm transition-all duration-200">
                    <div class="flex justify-between mb-1">
                        <span class="text-[10px] text-slate-400">1j</span>
                    </div>
                    <h4 class="text-sm font-medium text-slate-700 group-hover:text-[color:var(--accent)] transition-colors">Demande de licence Adobe</h4>
                    <div class="mt-2 flex items-center gap-2">
                        <div class="flex items-center gap-1 text-[10px] text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                            LIC-921
                        </div>
                    </div>
                </div>

                <div class="group cursor-pointer rounded-lg border border-transparent bg-white/50 p-3 hover:bg-white hover:border-slate-200 hover:shadow-sm transition-all duration-200">
                    <div class="flex justify-between mb-1">
                        <span class="text-[10px] text-slate-400">2j</span>
                    </div>
                    <h4 class="text-sm font-medium text-slate-700 group-hover:text-[color:var(--accent)] transition-colors">Exportation données incomplète</h4>
                    <div class="mt-2 flex items-center gap-2">
                        <div class="flex items-center gap-1 text-[10px] text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                            DATA-044
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversation Area (Animated) -->
        <div class="flex flex-1 flex-col bg-white">
            <div class="border-b border-slate-100 px-6 py-4 flex justify-between items-center bg-white sticky top-0 z-10">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Erreur 504 Gateway Timeout</h2>
                    <div class="flex items-center gap-2 text-xs text-slate-500 mt-1">
                        <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-medium">Haute Priorité</span>
                        <span>•</span>
                        <span>Client: TechFlow SAS</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button class="text-slate-400 hover:text-[color:var(--accent)]"><iconify-icon icon="solar:menu-dots-bold" class="text-xl"></iconify-icon></button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-6 bg-slate-50 custom-scrollbar">
                <div class="space-y-6 max-w-3xl mx-auto">
                    <!-- Date Separator -->
                    <div class="flex items-center justify-center">
                        <span class="text-[10px] font-medium text-slate-400 bg-slate-100 px-3 py-1 rounded-full">Aujourd'hui</span>
                    </div>

                    <!-- Message Client -->
                    <div class="flex gap-4">
                        <div class="h-10 w-10 rounded-full bg-slate-200 flex-shrink-0 flex items-center justify-center text-slate-500 text-xs font-bold">AH</div>
                        <div class="flex-1 max-w-xl">
                            <div class="flex items-baseline justify-between mb-1">
                                <h3 class="text-sm font-bold text-slate-900">Allie Harmon</h3>
                                <span class="text-xs text-slate-400">13:30</span>
                            </div>
                            <div class="rounded-bl-xl rounded-r-xl bg-white p-4 shadow-sm border border-slate-100 text-sm text-slate-600">
                                <p>Bonjour, nous rencontrons toujours des latences sur le serveur principal depuis la mise à jour de ce matin. Impossible d'accéder au back-office par moment.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Message Agent -->
                    <div class="flex gap-4 flex-row-reverse">
                        <div class="h-10 w-10 rounded-full flex-shrink-0 flex items-center justify-center text-white font-bold text-xs" style="background-color: var(--accent);">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                        <div class="flex-1 text-right max-w-xl">
                            <div class="flex items-baseline justify-between flex-row-reverse mb-1">
                                <h3 class="text-sm font-bold text-slate-900">Vous</h3>
                                <span class="text-xs text-slate-400">14:02</span>
                            </div>
                            <div class="rounded-br-xl rounded-l-xl p-4 shadow-md text-sm text-white text-left inline-block" style="background-color: var(--accent);">
                                <p>Merci pour le signalement. Nous avons identifié un pic de charge sur le load balancer. Je regarde ça immédiatement.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Typing Indicator -->
                    <div class="flex gap-4">
                        <div class="h-8 w-8 flex items-center justify-center">
                            <div class="flex gap-1">
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-300 animate-bounce"></span>
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-300 animate-bounce" style="animation-delay: 0.2s"></span>
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-300 animate-bounce" style="animation-delay: 0.4s"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reply Box -->
            <div class="border-t border-slate-200 bg-white p-4">
                <div class="mx-auto max-w-3xl rounded-lg border border-slate-300 bg-white shadow-sm ring-4 accent-ring-soft accent-focus transition-all">
                    <div class="flex items-center gap-2 border-b border-slate-100 bg-slate-50/50 px-3 py-2">
                        <button class="p-1 rounded hover:bg-slate-200 text-slate-500"><iconify-icon icon="solar:text-bold-linear"></iconify-icon></button>
                        <button class="p-1 rounded hover:bg-slate-200 text-slate-500"><iconify-icon icon="solar:link-linear"></iconify-icon></button>
                        <button class="p-1 rounded hover:bg-slate-200 text-slate-500"><iconify-icon icon="solar:paperclip-linear"></iconify-icon></button>
                    </div>
                    <div class="p-3">
                        <textarea class="w-full resize-none border-none bg-transparent p-0 text-sm text-slate-600 focus:ring-0 min-h-[80px]" placeholder="Rédigez votre réponse..."></textarea>
                    </div>
                    <div class="flex justify-between items-center px-3 py-2 border-t border-slate-50">
                        <div class="flex items-center gap-2">
                            <button class="text-xs font-medium text-slate-500 hover:text-[color:var(--accent)] flex items-center gap-1 px-2 py-1 rounded accent-hover-soft transition-colors">
                                <iconify-icon icon="solar:magic-stick-linear"></iconify-icon> IA Suggestion
                            </button>
                        </div>
                        <button class="text-white px-4 py-1.5 rounded-lg text-xs font-bold transition-all hover:brightness-95 shadow-lg flex items-center gap-2" style="background-color: var(--accent); box-shadow: 0 18px 35px color-mix(in srgb, var(--accent) 22%, transparent);">
                            Envoyer <iconify-icon icon="solar:plain-linear"></iconify-icon>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>

