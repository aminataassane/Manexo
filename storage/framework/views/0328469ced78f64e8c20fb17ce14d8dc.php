<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MANEXO - Gestion de tickets Multi-entreprises</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
    <style>
        body, .font-serif {
            font-family: "Mona Sans", sans-serif;
            font-optical-sizing: auto;
            font-style: normal;
            font-variation-settings: "wdth" 100;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #E5E7EB;
            border-radius: 20px;
        }

        /* Animations */
        .fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .float-element {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .cursor-blink {
            animation: blink 1s step-end infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }

        .slide-in-toast {
            animation: slideInToast 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateX(20px);
            animation-delay: 1.5s;
        }

        @keyframes slideInToast {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .grow-bar {
            transform-origin: bottom;
            animation: growBar 1s ease-out forwards;
            transform: scaleY(0);
        }

        @keyframes growBar {
            to { transform: scaleY(1); }
        }

        /* Hover Glow Effect */
        .glow-hover:hover {
            box-shadow: 0 0 20px rgba(0, 95, 2, 0.15);
        }
    </style>
</head>

<body class="bg-white text-slate-900 antialiased selection:bg-[#F2E3BB] selection:text-[#005F02]">

    <!-- Floating Navbar -->
    <nav class="fixed top-6 left-1/2 z-50 w-[95%] max-w-5xl -translate-x-1/2 transform transition-all duration-300">
        <div class="relative flex items-center justify-between rounded-full border border-white/10 bg-[#002e01]/85 p-2 pl-6 pr-2 shadow-2xl backdrop-blur-xl ring-1 ring-white/5 transition-all hover:bg-[#002e01]/95 hover:shadow-[0_0_40px_rgba(0,95,2,0.4)]">
            
            <!-- Logo -->
            <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2 group cursor-pointer">
                <div class="relative">
                    <iconify-icon icon="solar:layers-minimalistic-linear" class="text-[#F2E3BB] text-2xl transition-transform group-hover:rotate-180 duration-700"></iconify-icon>
                    <div class="absolute inset-0 bg-[#F2E3BB] blur-sm opacity-0 group-hover:opacity-50 transition-opacity"></div>
                </div>
                <span class="text-lg font-semibold tracking-tight text-white">Manexo</span>
            </a>

            <!-- Centered Links -->
            <div class="hidden items-center gap-8 md:flex absolute left-1/2 -translate-x-1/2">
                <a href="#" class="text-xs font-medium text-white/70 hover:text-white transition-colors">Fonctionnalités</a>
                <a href="#" class="text-xs font-medium text-white/70 hover:text-white transition-colors">Multi-entreprises</a>
                <a href="#" class="text-xs font-medium text-white/70 hover:text-white transition-colors">Ressources</a>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('organizations.select')); ?>" class="hidden px-4 py-2 text-xs font-medium text-white hover:text-[#F2E3BB] transition-colors md:block">Tableau de bord</a>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="hidden px-4 py-2 text-xs font-medium text-white hover:text-[#F2E3BB] transition-colors md:block">Connexion</a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <a href="<?php echo e(route('register')); ?>" class="group relative overflow-hidden rounded-full bg-white px-5 py-2.5 text-xs font-bold text-[#005F02] transition-all hover:bg-[#F2E3BB] hover:shadow-[0_0_15px_rgba(242,227,187,0.4)]">
                    <span class="relative z-10">Réserver</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section (Height increased significantly) -->
    <header class="relative overflow-hidden bg-[#002e01] pt-48 pb-32 lg:pt-64 lg:pb-48 text-white">
        <!-- Animated Background Pattern -->
        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#F2E3BB 0.5px, transparent 0.5px); background-size: 24px 24px;">
        </div>
        <div class="absolute top-0 right-0 -mt-20 -mr-20 h-96 w-96 rounded-full bg-[#005F02] blur-3xl opacity-40 mix-blend-screen animate-pulse"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 h-96 w-96 rounded-full bg-[#F2E3BB] blur-3xl opacity-10 mix-blend-screen"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-6 text-center">
            <div class="mb-10 flex justify-center fade-in-up">
                <span class="group inline-flex cursor-pointer items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-[#F2E3BB] backdrop-blur-sm transition-all hover:bg-white/10 hover:border-[#F2E3BB]/50">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#F2E3BB] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#F2E3BB]"></span>
                    </span>
                    NOUVEAU : Workflow Designer 2.0
                    <iconify-icon icon="solar:arrow-right-linear" class="transition-transform group-hover:translate-x-1"></iconify-icon>
                </span>
            </div>

            <h1 class="fade-in-up mx-auto max-w-5xl font-serif text-6xl font-normal leading-tight tracking-tight sm:text-8xl lg:leading-[1.1]">
                Le support client,<br>
                <span class="italic text-[#F2E3BB] relative inline-block">
                    enfin synchronisé.
                    <svg class="absolute -bottom-4 left-0 w-full text-[#F2E3BB]" height="8" viewBox="0 0 100 6" preserveAspectRatio="none"><path d="M0,5 Q50,0 100,5" fill="none" stroke="currentColor" stroke-width="2"></path></svg>
                </span>
            </h1>

            <p class="fade-in-up mx-auto mt-8 max-w-2xl text-xl font-light leading-relaxed text-white/70" style="animation-delay: 0.1s;">
                Manexo transforme le chaos des tickets en une symphonie opérationnelle. Centralisez, automatisez et résolvez plus vite avec la première plateforme conçue pour le multi-entreprises.
            </p>

            <div class="fade-in-up mx-auto mt-12 flex max-w-md flex-col items-center gap-4 sm:flex-row" style="animation-delay: 0.2s;">
                <div class="relative w-full group">
                    <input type="email" placeholder="email@entreprise.com" class="peer w-full rounded bg-white/10 border border-white/20 py-4 pl-4 pr-32 text-sm text-white placeholder-white/40 focus:border-[#F2E3BB] focus:bg-white/15 focus:outline-none focus:ring-1 focus:ring-[#F2E3BB] transition-all">
                    <button class="absolute right-1.5 top-1.5 bottom-1.5 rounded bg-[#005F02] px-6 text-sm font-medium text-white shadow-lg hover:bg-[#427A43] transition-all hover:scale-105 active:scale-95">
                        Essayer
                    </button>
                </div>
            </div>
        </div>

        <!-- Dynamic Hero Dashboard Visual -->
        <div class="relative mx-auto mt-24 max-w-[1200px] px-2 lg:px-4 fade-in-up" style="animation-delay: 0.4s;">
            
            <!-- Floating Notification Badge (Dynamic Element) -->
            <div class="slide-in-toast absolute -right-8 top-32 z-30 hidden lg:flex w-72 flex-col rounded-lg border border-white/20 bg-[#002e01]/90 backdrop-blur-xl p-3 shadow-2xl ring-1 ring-white/10">
                <div class="flex items-start gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#F2E3BB] text-[#002e01]">
                        <iconify-icon icon="solar:bell-bing-bold-duotone"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-white">Nouveau ticket assigné</h4>
                        <p class="text-[10px] text-white/70 mt-0.5">Urgent: Problème de paiement API Stripe - Client TechFlow</p>
                        <div class="mt-2 flex gap-2">
                            <button class="rounded bg-white/10 px-2 py-1 text-[10px] font-medium hover:bg-white/20">Ignorer</button>
                            <button class="rounded bg-[#005F02] px-2 py-1 text-[10px] font-medium text-white hover:bg-[#004d02]">Voir</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Container -->
            <div class="relative rounded-xl border border-white/10 bg-[#071F0B] p-2 shadow-2xl ring-1 ring-black/50 transition-transform duration-700 hover:scale-[1.01]">
                <!-- Mac Traffic Lights -->
                <div class="mb-2 flex items-center gap-2 px-2">
                    <div class="flex gap-1.5 group">
                        <div class="h-2.5 w-2.5 rounded-full bg-[#FF5F56] group-hover:bg-[#ff5f56]/80 transition-colors"></div>
                        <div class="h-2.5 w-2.5 rounded-full bg-[#FFBD2E] group-hover:bg-[#ffbd2e]/80 transition-colors"></div>
                        <div class="h-2.5 w-2.5 rounded-full bg-[#27C93F] group-hover:bg-[#27c93f]/80 transition-colors"></div>
                    </div>
                    <div class="mx-auto flex items-center gap-2 rounded bg-white/5 px-3 py-1 text-[10px] text-white/40 border border-white/5">
                        <iconify-icon icon="solar:lock-keyhole-minimalistic-linear"></iconify-icon> app.manexo.io
                    </div>
                </div>

                <div class="flex h-[680px] w-full flex-col overflow-hidden rounded-lg bg-white text-slate-800 shadow-inner">
                    <!-- Top Bar -->
                    <div class="flex h-14 shrink-0 items-center justify-between border-b border-white/10 bg-[#002e01] px-4 text-white">
                        <div class="flex items-center gap-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded bg-gradient-to-br from-[#F2E3BB] to-[#d6c79e] text-[#002e01] shadow-sm">
                                <span class="font-serif font-bold">M</span>
                            </div>
                        </div>
                        
                        <div class="flex w-96 items-center gap-2 rounded-md bg-white/10 px-3 py-1.5 text-xs text-white/60 ring-1 ring-white/10 focus-within:ring-[#F2E3BB] focus-within:bg-white/15 transition-all">
                            <iconify-icon icon="solar:magnifer-linear" class="text-base"></iconify-icon>
                            <input type="text" placeholder="Rechercher (Cmd+K)" class="bg-transparent w-full border-none outline-none placeholder-white/40 text-white">
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <iconify-icon icon="solar:bell-linear" class="text-xl text-white/60 hover:text-white cursor-pointer transition-colors"></iconify-icon>
                                <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500 border border-[#002e01]"></span>
                            </div>
                            <div class="h-8 w-8 rounded-full border border-white/20 bg-white/10 flex items-center justify-center text-[10px] font-extrabold text-[#F2E3BB] hover:border-[#F2E3BB] cursor-pointer transition-colors">
                                JD
                            </div>
                        </div>
                    </div>

                    <!-- Interface -->
                    <div class="flex flex-1 overflow-hidden">
                        
                        <!-- Sidebar -->
                        <div class="flex w-16 flex-col items-center border-r border-white/10 bg-[#002e01] py-4 gap-6 z-10">
                            <div class="group flex h-10 w-10 cursor-pointer items-center justify-center rounded-lg bg-[#F2E3BB]/10 text-[#F2E3BB] transition-all hover:bg-[#F2E3BB] hover:text-[#002e01]">
                                <iconify-icon icon="solar:inbox-line-linear" class="text-xl"></iconify-icon>
                            </div>
                            <div class="group flex h-10 w-10 cursor-pointer items-center justify-center rounded-lg text-white/40 transition-all hover:bg-white/10 hover:text-[#F2E3BB]">
                                <iconify-icon icon="solar:checklist-minimalistic-linear" class="text-xl"></iconify-icon>
                            </div>
                            <div class="group flex h-10 w-10 cursor-pointer items-center justify-center rounded-lg text-white/40 transition-all hover:bg-white/10 hover:text-[#F2E3BB]">
                                <iconify-icon icon="solar:users-group-rounded-linear" class="text-xl"></iconify-icon>
                            </div>
                             <div class="group flex h-10 w-10 cursor-pointer items-center justify-center rounded-lg text-white/40 transition-all hover:bg-white/10 hover:text-[#F2E3BB]">
                                <iconify-icon icon="solar:graph-up-linear" class="text-xl"></iconify-icon>
                            </div>
                        </div>

                        <!-- Ticket List -->
                        <div class="flex w-72 flex-col border-r border-slate-200 bg-slate-50/50">
                            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                                <div class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-tight">
                                    Vues Tickets
                                </div>
                                <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div> <!-- Live Indicator -->
                            </div>
                            
                            <!-- Ticket Items -->
                            <div class="flex-1 overflow-y-auto px-2 py-2 space-y-2 custom-scrollbar">
                                <!-- Active Ticket -->
                                <div class="group cursor-pointer rounded-lg border border-[#005F02]/20 bg-white p-3 shadow-md ring-1 ring-[#005F02]/5 relative overflow-hidden transition-all hover:-translate-y-0.5">
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#005F02]"></div>
                                    <div class="flex justify-between mb-1 pl-2">
                                        <div class="flex items-center gap-1">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            <span class="text-[10px] text-slate-400 font-medium">En cours</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400">12m</span>
                                    </div>
                                    <h4 class="text-sm font-semibold text-slate-900 line-clamp-1 pl-2">Erreur 504 Gateway Timeout</h4>
                                    <p class="text-[11px] text-slate-500 pl-2 mt-1 line-clamp-1">Le serveur ne répond pas lors de...</p>
                                    <div class="mt-2 flex items-center gap-2 pl-2">
                                        <div class="flex items-center gap-1 text-[10px] font-medium text-[#005F02] bg-[#F2E3BB]/30 px-1.5 py-0.5 rounded">
                                            OPS-102
                                        </div>
                                            <div class="ml-auto h-5 w-5 rounded-full border border-white ring-1 ring-slate-100 bg-[#005F02] text-white flex items-center justify-center text-[9px] font-extrabold">
                                                A
                                            </div>
                                    </div>
                                </div>

                                <!-- Other Tickets -->
                                <div class="group cursor-pointer rounded-lg border border-transparent bg-white/50 p-3 hover:bg-white hover:border-slate-200 hover:shadow-sm transition-all duration-200">
                                    <div class="flex justify-between mb-1">
                                        <span class="text-[10px] text-slate-400">2h</span>
                                    </div>
                                    <h4 class="text-sm font-medium text-slate-700 group-hover:text-[#005F02] transition-colors">Problème d'authentification SSO</h4>
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
                                    <h4 class="text-sm font-medium text-slate-700 group-hover:text-[#005F02] transition-colors">Demande de licence Adobe</h4>
                                    <div class="mt-2 flex items-center gap-2">
                                        <div class="flex items-center gap-1 text-[10px] text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                                            LIC-921
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Conversation Area (Animated) -->
                        <div class="flex flex-1 flex-col bg-white">
                            <div class="border-b border-slate-100 px-6 py-4 flex justify-between items-center bg-white">
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900">Erreur 504 Gateway Timeout</h2>
                                    <div class="flex items-center gap-2 text-xs text-slate-500 mt-1">
                                        <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-medium">Haute Priorité</span>
                                        <span>•</span>
                                        <span>Client: TechFlow SAS</span>
                                    </div>
                                </div>
                                <div class="flex -space-x-2">
                                    <div class="h-8 w-8 rounded-full border-2 border-white ring-1 ring-slate-100 bg-[#F2E3BB] text-[#002e01] flex items-center justify-center text-[10px] font-extrabold" title="Client">AH</div>
                                    <div class="h-8 w-8 rounded-full border-2 border-white bg-[#005F02] text-white flex items-center justify-center text-xs font-bold ring-1 ring-slate-100" title="Agent">MO</div>
                                </div>
                            </div>

                            <div class="flex-1 overflow-y-auto p-6 bg-slate-50 custom-scrollbar">
                                <div class="space-y-6">
                                    <!-- Message Client -->
                                    <div class="flex gap-4">
                                        <div class="h-10 w-10 rounded-full bg-[#F2E3BB] text-[#002e01] flex items-center justify-center text-xs font-extrabold">AH</div>
                                        <div class="flex-1">
                                            <div class="flex items-baseline justify-between">
                                                <h3 class="text-sm font-bold text-slate-900">Allie Harmon</h3>
                                                <span class="text-xs text-slate-400">13:30</span>
                                            </div>
                                            <div class="mt-1 rounded-bl-xl rounded-r-xl bg-white p-4 shadow-sm border border-slate-100 text-sm text-slate-600">
                                                <p>Bonjour, nous rencontrons toujours des latences sur le serveur principal. Ci-joint les captures d'écran.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Message Agent -->
                                    <div class="flex gap-4 flex-row-reverse">
                                        <div class="h-10 w-10 rounded-full bg-[#005F02] flex items-center justify-center text-white font-bold text-xs">MO</div>
                                        <div class="flex-1 text-right">
                                            <div class="flex items-baseline justify-between flex-row-reverse">
                                                <h3 class="text-sm font-bold text-slate-900">Manexo Ops</h3>
                                                <span class="text-xs text-slate-400">14:02</span>
                                            </div>
                                            <div class="mt-1 rounded-br-xl rounded-l-xl bg-[#005F02] p-4 shadow-md text-sm text-white text-left inline-block">
                                                <p>Merci pour les logs. Nous avons identifié un pic de charge. Je regarde ça.</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Typing Indicator -->
                                    <div class="flex gap-4">
                                        <div class="h-10 w-10 flex items-center justify-center">
                                            <div class="flex gap-1">
                                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400 animate-bounce"></span>
                                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0.2s"></span>
                                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0.4s"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reply Box with Blinking Cursor -->
                            <div class="border-t border-slate-200 bg-white p-4">
                                <div class="rounded-lg border border-slate-300 bg-white shadow-sm ring-4 ring-[#005F02]/5">
                                    <div class="flex items-center gap-2 border-b border-slate-100 bg-slate-50/50 px-3 py-2">
                                        <iconify-icon icon="solar:text-bold-linear" class="text-slate-400 hover:text-slate-600"></iconify-icon>
                                        <iconify-icon icon="solar:link-linear" class="text-slate-400 hover:text-slate-600"></iconify-icon>
                                    </div>
                                    <div class="p-3 text-sm text-slate-600 min-h-[80px]">
                                        Je lance le redémarrage du pod k8s pour<span class="cursor-blink border-r-2 border-[#005F02]"></span>
                                    </div>
                                    <div class="flex justify-between items-center px-3 py-2">
                                        <div class="flex items-center gap-2">
                                            <button class="text-xs font-medium text-slate-500 hover:text-[#005F02] flex items-center gap-1">
                                                <iconify-icon icon="solar:magic-stick-linear"></iconify-icon> Améliorer avec IA
                                            </button>
                                        </div>
                                        <button class="bg-[#005F02] hover:bg-[#004d02] text-white px-4 py-1.5 rounded text-xs font-bold transition-colors shadow-lg shadow-[#005F02]/20">Envoyer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- NEW EXPANDED FEATURES SECTION -->
    <section class="py-24 bg-white relative">
        <div class="mx-auto max-w-7xl px-6 relative z-10">
            <div class="mx-auto max-w-2xl text-center mb-16">
                <span class="inline-block py-1 px-3 rounded-full bg-[#005F02]/5 text-xs font-bold text-[#005F02] uppercase tracking-wider mb-4 border border-[#005F02]/10">Capacités Étendues</span>
                <h2 class="font-serif text-4xl text-slate-900 font-medium">Une suite complète pour<br>l'excellence opérationnelle</h2>
                <p class="mt-4 text-slate-500 text-lg">Chaque module de Manexo est conçu pour réduire la friction et augmenter la résolution au premier contact.</p>
            </div>

            <!-- Detailed Module Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <!-- Module 1: Helpdesk -->
                <div class="group rounded-2xl border border-slate-200 bg-white p-8 transition-all duration-300 hover:-translate-y-2 hover:border-[#005F02]/30 hover:shadow-xl hover:shadow-slate-200/50 glow-hover">
                    <div class="mb-6 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-[#005F02] text-white shadow-lg shadow-[#005F02]/20 transition-transform group-hover:scale-110 group-hover:rotate-3">
                        <iconify-icon icon="solar:inbox-line-linear" class="text-3xl"></iconify-icon>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-4 group-hover:text-[#005F02] transition-colors">Gestion des Tickets</h3>
                    <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                        Un espace de travail unifié pour traiter les demandes provenant de l'email, du chat et des formulaires web.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3 text-sm text-slate-700 group-hover:translate-x-1 transition-transform">
                            <iconify-icon icon="solar:check-circle-bold" class="text-[#005F02] mt-0.5 text-base flex-shrink-0"></iconify-icon>
                            <span><strong>Détection de collision</strong> temps réel</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm text-slate-700 group-hover:translate-x-1 transition-transform delay-75">
                            <iconify-icon icon="solar:check-circle-bold" class="text-[#005F02] mt-0.5 text-base flex-shrink-0"></iconify-icon>
                            <span><strong>SLA Tracking</strong> visuel</span>
                        </li>
                    </ul>
                </div>

                <!-- Module 2: Multi-Company -->
                <div class="group rounded-2xl border border-slate-200 bg-white p-8 transition-all duration-300 hover:-translate-y-2 hover:border-[#005F02]/30 hover:shadow-xl hover:shadow-slate-200/50 glow-hover">
                    <div class="mb-6 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-[#005F02] border border-slate-200 shadow-lg transition-transform group-hover:scale-110 group-hover:-rotate-3">
                        <iconify-icon icon="solar:city-linear" class="text-3xl"></iconify-icon>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-4 group-hover:text-[#005F02] transition-colors">Architecture Multi-Entités</h3>
                    <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                        Gérez plusieurs marques ou clients depuis une seule instance sans jamais mélanger les données.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3 text-sm text-slate-700 group-hover:translate-x-1 transition-transform">
                            <iconify-icon icon="solar:check-circle-bold" class="text-[#005F02] mt-0.5 text-base flex-shrink-0"></iconify-icon>
                            <span><strong>Silos de données</strong> étanches</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm text-slate-700 group-hover:translate-x-1 transition-transform delay-75">
                            <iconify-icon icon="solar:check-circle-bold" class="text-[#005F02] mt-0.5 text-base flex-shrink-0"></iconify-icon>
                            <span><strong>Branding</strong> 100% personnalisable</span>
                        </li>
                    </ul>
                </div>

                <!-- Module 3: Automation -->
                <div class="group rounded-2xl border border-slate-200 bg-white p-8 transition-all duration-300 hover:-translate-y-2 hover:border-[#005F02]/30 hover:shadow-xl hover:shadow-slate-200/50 glow-hover">
                    <div class="mb-6 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-[#F2E3BB] text-[#005F02] shadow-lg transition-transform group-hover:scale-110 group-hover:rotate-3">
                        <iconify-icon icon="solar:bolt-linear" class="text-3xl"></iconify-icon>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-4 group-hover:text-[#005F02] transition-colors">Workflow &amp; Automatisations</h3>
                    <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                        Automatisez les tâches répétitives pour permettre à votre équipe de se concentrer sur les problèmes complexes.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3 text-sm text-slate-700 group-hover:translate-x-1 transition-transform">
                            <iconify-icon icon="solar:check-circle-bold" class="text-[#005F02] mt-0.5 text-base flex-shrink-0"></iconify-icon>
                            <span><strong>Routage intelligent</strong> par mots-clés</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm text-slate-700 group-hover:translate-x-1 transition-transform delay-75">
                            <iconify-icon icon="solar:check-circle-bold" class="text-[#005F02] mt-0.5 text-base flex-shrink-0"></iconify-icon>
                            <span><strong>Webhooks</strong> sortants</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed Functional Deep Dive with Active Visuals -->
    <section class="py-20 bg-[#FAFAFA] border-t border-slate-200 overflow-hidden">
        <div class="mx-auto max-w-7xl px-6">
            
            <!-- Feature Block 1: Form Builder -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center mb-24">
                <div class="order-2 lg:order-1 relative rounded-2xl bg-white p-2 border border-slate-200 shadow-2xl transition-transform hover:scale-[1.01] duration-500">
                    <!-- Decor elements -->
                    <div class="absolute -left-10 top-10 h-24 w-24 rounded-full bg-[#005F02] opacity-5 blur-2xl"></div>
                    
                    <!-- Fake UI Form Builder -->
                    <div class="bg-slate-50 rounded-xl p-6 border border-slate-100 relative overflow-hidden">
                        <div class="space-y-4">
                            <!-- Animated Field Selection -->
                            <div class="group relative bg-white p-4 rounded-lg border border-slate-200 shadow-sm hover:border-[#005F02] cursor-pointer transition-all hover:shadow-md">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-semibold text-slate-700">Sujet de la demande</span>
                                    <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <iconify-icon icon="solar:pen-linear" class="text-[#005F02] text-sm"></iconify-icon>
                                    </div>
                                </div>
                                <div class="h-8 w-full bg-slate-100 rounded border border-slate-200 group-hover:bg-white transition-colors"></div>
                            </div>
                            <!-- Field 2 -->
                            <div class="relative bg-white p-4 rounded-lg border-l-4 border-l-[#005F02] border-y border-r border-slate-200 shadow-sm cursor-pointer">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-semibold text-slate-700">Priorité</span>
                                    <span class="text-[10px] bg-[#005F02]/10 text-[#005F02] px-2 py-0.5 rounded font-medium">Conditionnel</span>
                                </div>
                                <div class="flex gap-2">
                                    <div class="flex-1 h-8 bg-slate-100 rounded border border-slate-200 flex items-center px-2 text-[10px] text-slate-400">Basse</div>
                                    <div class="flex-1 h-8 bg-slate-100 rounded border border-slate-200 flex items-center px-2 text-[10px] text-slate-400">Moyenne</div>
                                    <div class="flex-1 h-8 bg-[#005F02] rounded border border-[#005F02] flex items-center px-2 text-[10px] text-white shadow-md">Haute</div>
                                </div>
                                
                                <!-- Logic Connector Visualization -->
                                <div class="absolute -right-8 top-1/2 -translate-y-1/2 w-8 h-px bg-[#005F02] border-t border-dashed border-[#005F02]"></div>
                                <div class="absolute -right-36 top-1/2 -translate-y-1/2 bg-[#005F02] text-white text-[10px] px-3 py-1.5 rounded shadow-lg animate-pulse">
                                    Afficher "Urgence"
                                </div>
                            </div>
                            
                            <!-- Add Field Button -->
                            <div class="border-2 border-dashed border-slate-200 rounded-lg p-3 flex justify-center items-center text-xs text-slate-400 hover:text-[#005F02] hover:border-[#005F02] hover:bg-[#005F02]/5 transition-all cursor-pointer">
                                <iconify-icon icon="solar:add-circle-linear" class="mr-2 text-lg"></iconify-icon> Ajouter un champ conditionnel
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="order-1 lg:order-2">
                    <div class="inline-flex items-center gap-2 rounded-full bg-[#005F02]/10 px-3 py-1 text-xs font-bold text-[#005F02] mb-6 border border-[#005F02]/20">
                        <iconify-icon icon="solar:magic-stick-linear"></iconify-icon>
                        NO-CODE
                    </div>
                    <h3 class="font-serif text-3xl text-slate-900 mb-4 font-medium">Formulaires Intelligents &amp; Conditionnels</h3>
                    <p class="text-slate-600 mb-8 leading-relaxed text-lg">
                        Fini les échanges d'emails interminables. Créez des formulaires qui s'adaptent en temps réel aux réponses de vos utilisateurs.
                    </p>
                    <div class="space-y-6">
                        <div class="flex gap-4 group cursor-pointer">
                            <div class="h-10 w-10 shrink-0 rounded-full bg-white border border-slate-200 flex items-center justify-center text-[#005F02] font-bold shadow-sm group-hover:bg-[#005F02] group-hover:text-white transition-colors">1</div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Glisser-déposer intuitif</h4>
                                <p class="text-xs text-slate-500 mt-1">Aucune compétence technique requise.</p>
                            </div>
                        </div>
                        <div class="flex gap-4 group cursor-pointer">
                            <div class="h-10 w-10 shrink-0 rounded-full bg-white border border-slate-200 flex items-center justify-center text-[#005F02] font-bold shadow-sm group-hover:bg-[#005F02] group-hover:text-white transition-colors">2</div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Logique conditionnelle</h4>
                                <p class="text-xs text-slate-500 mt-1">"Si X est sélectionné, alors afficher Y".</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature Block 2: Analytics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="order-1">
                    <div class="inline-flex items-center gap-2 rounded-full bg-[#F2E3BB]/50 px-3 py-1 text-xs font-bold text-[#005F02] mb-6 border border-[#F2E3BB]">
                        <iconify-icon icon="solar:chart-square-linear"></iconify-icon>
                        REPORTING
                    </div>
                    <h3 class="font-serif text-3xl text-slate-900 mb-4 font-medium">Analysez la performance par Client</h3>
                    <p class="text-slate-600 mb-8 leading-relaxed text-lg">
                        Identifiez les goulots d'étranglement, mesurez la satisfaction (CSAT) et surveillez le respect des SLAs en temps réel.
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wide">Temps 1ère réponse</div>
                            <div class="text-3xl font-serif text-slate-900">45 min <span class="text-xs font-sans text-green-600 bg-green-50 px-2 py-0.5 rounded-full ml-2">-12%</span></div>
                        </div>
                        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wide">Score CSAT</div>
                            <div class="text-3xl font-serif text-slate-900">4.8<span class="text-xl text-slate-400">/5</span></div>
                            <div class="flex mt-2 text-[#FFBD2E] text-xs">
                                <iconify-icon icon="solar:star-bold"></iconify-icon>
                                <iconify-icon icon="solar:star-bold"></iconify-icon>
                                <iconify-icon icon="solar:star-bold"></iconify-icon>
                                <iconify-icon icon="solar:star-bold"></iconify-icon>
                                <iconify-icon icon="solar:star-bold"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="order-2 relative rounded-2xl bg-white p-2 border border-slate-200 shadow-2xl group overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-transparent to-[#005F02]/5"></div>
                    
                    <!-- Fake UI Analytics -->
                    <div class="bg-slate-50 rounded-xl p-6 border border-slate-100 min-h-[300px] flex flex-col relative z-10">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <iconify-icon icon="solar:graph-new-linear" class="text-[#005F02]"></iconify-icon> Volume de tickets
                            </h4>
                            <div class="text-[10px] font-medium text-slate-500 bg-white border border-slate-200 px-2 py-1 rounded shadow-sm">30 derniers jours</div>
                        </div>
                        
                        <!-- Bar Chart Animation -->
                        <div class="flex items-end justify-between h-40 gap-4 mt-auto px-2">
                            <div class="w-full flex flex-col items-center gap-2">
                                <div class="w-full bg-[#005F02] rounded-t-md h-[60%] opacity-80 relative grow-bar" style="animation-delay: 0.1s">
                                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity">142</div>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Client A</span>
                            </div>
                            <div class="w-full flex flex-col items-center gap-2">
                                <div class="w-full bg-[#F2E3BB] rounded-t-md h-[40%] relative grow-bar" style="animation-delay: 0.2s">
                                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity">86</div>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Client B</span>
                            </div>
                            <div class="w-full flex flex-col items-center gap-2">
                                <div class="w-full bg-[#005F02] rounded-t-md h-[85%] relative grow-bar" style="animation-delay: 0.3s">
                                     <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity">210</div>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Client C</span>
                            </div>
                            <div class="w-full flex flex-col items-center gap-2">
                                <div class="w-full bg-[#005F02] rounded-t-md h-[30%] opacity-60 relative grow-bar" style="animation-delay: 0.4s">
                                     <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity">45</div>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Client D</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>



    <!-- Technical Specs Table with hover effect -->
    <section class="py-16 bg-white border-t border-slate-200">
        <div class="mx-auto max-w-5xl px-6">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 text-center mb-12">Spécifications Techniques</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="group flex flex-col items-center text-center p-6 rounded-xl border border-transparent hover:border-slate-100 hover:bg-slate-50 transition-all duration-300">
                    <div class="h-12 w-12 rounded-full bg-[#005F02]/5 flex items-center justify-center mb-4 group-hover:bg-[#005F02] group-hover:text-white transition-colors">
                        <iconify-icon icon="solar:shield-check-linear" class="text-2xl text-[#005F02] group-hover:text-white"></iconify-icon>
                    </div>
                    <span class="text-sm font-bold text-slate-900">RGPD &amp; Hébergement</span>
                    <span class="text-xs text-slate-500 mt-1">Serveurs en France (AWS)</span>
                </div>
                <div class="group flex flex-col items-center text-center p-6 rounded-xl border border-transparent hover:border-slate-100 hover:bg-slate-50 transition-all duration-300">
                    <div class="h-12 w-12 rounded-full bg-[#005F02]/5 flex items-center justify-center mb-4 group-hover:bg-[#005F02] group-hover:text-white transition-colors">
                        <iconify-icon icon="solar:code-square-linear" class="text-2xl text-[#005F02] group-hover:text-white"></iconify-icon>
                    </div>
                    <span class="text-sm font-bold text-slate-900">API REST Complète</span>
                    <span class="text-xs text-slate-500 mt-1">Webhooks &amp; Endpoints</span>
                </div>
                <div class="group flex flex-col items-center text-center p-6 rounded-xl border border-transparent hover:border-slate-100 hover:bg-slate-50 transition-all duration-300">
                    <div class="h-12 w-12 rounded-full bg-[#005F02]/5 flex items-center justify-center mb-4 group-hover:bg-[#005F02] group-hover:text-white transition-colors">
                        <iconify-icon icon="solar:login-2-linear" class="text-2xl text-[#005F02] group-hover:text-white"></iconify-icon>
                    </div>
                    <span class="text-sm font-bold text-slate-900">SSO &amp; SAML</span>
                    <span class="text-xs text-slate-500 mt-1">Google, Microsoft, Okta</span>
                </div>
                <div class="group flex flex-col items-center text-center p-6 rounded-xl border border-transparent hover:border-slate-100 hover:bg-slate-50 transition-all duration-300">
                    <div class="h-12 w-12 rounded-full bg-[#005F02]/5 flex items-center justify-center mb-4 group-hover:bg-[#005F02] group-hover:text-white transition-colors">
                        <iconify-icon icon="solar:history-linear" class="text-2xl text-[#005F02] group-hover:text-white"></iconify-icon>
                    </div>
                    <span class="text-sm font-bold text-slate-900">Audit Logs</span>
                    <span class="text-xs text-slate-500 mt-1">Traçabilité totale</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-white py-20 border-t border-slate-100">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid grid-cols-1 gap-8 text-center sm:grid-cols-3">
                <div>
                    <div class="font-serif text-5xl text-slate-900">5.6h</div>
                    <div class="mt-2 text-xs font-medium uppercase tracking-wide text-slate-500">Économisées par jour
                    </div>
                </div>
                <div>
                    <div class="font-serif text-5xl text-slate-900">220k</div>
                    <div class="mt-2 text-xs font-medium uppercase tracking-wide text-slate-500">Tickets suivis</div>
                </div>
                <div>
                    <div class="font-serif text-5xl text-slate-900">98%</div>
                    <div class="mt-2 text-xs font-medium uppercase tracking-wide text-slate-500">Satisfaction client
                    </div>
                </div>
            </div>
        </div>
    </section>
            <!-- CTA Footer -->
    <section class="relative overflow-hidden bg-[#002e01] py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-16 lg:grid-cols-2 lg:items-center">
                <div class="max-w-lg">
                    <h2 class="font-serif text-4xl text-white">Voyez Manexo en action</h2>
                    <p class="mt-4 text-lg text-white/70">
                        Obtenez une démo de notre workflow multi-entreprises et un aperçu des nouveautés à venir.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <input type="email" placeholder="Votre email" class="w-full rounded bg-white/10 border border-white/20 py-3 pl-4 text-sm text-white placeholder-white/50 focus:border-[#F2E3BB] focus:outline-none">
                        <button class="shrink-0 rounded bg-[#F2E3BB] px-6 py-3 text-sm font-medium text-[#002e01] hover:bg-white transition-colors">
                            Réserver une démo
                        </button>
                    </div>
                    <div class="mt-6 flex items-center gap-6 text-xs text-white/40">
                        <span class="flex items-center gap-2"><iconify-icon icon="solar:check-circle-bold" class="text-[#F2E3BB]"></iconify-icon> Pas de carte requise</span>
                        <span class="flex items-center gap-2"><iconify-icon icon="solar:check-circle-bold" class="text-[#F2E3BB]"></iconify-icon> Annulation facile</span>
                    </div>
                </div>

                <!-- Angled Image visual -->
                <div class="relative lg:ml-auto">
                    <div class="relative w-[600px] -rotate-6 rounded-xl bg-white shadow-2xl overflow-hidden border border-white/10 opacity-90 transition-transform hover:rotate-0 duration-500">
                        <div class="bg-slate-50 border-b border-slate-200 p-3 flex gap-2">
                            <div class="h-2 w-2 rounded-full bg-slate-300"></div>
                            <div class="h-2 w-2 rounded-full bg-slate-300"></div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="font-serif text-xl text-slate-900">Bienvenue, John</h3>
                                <div class="h-8 w-8 rounded-full bg-[#005F02] text-white flex items-center justify-center text-xs">
                                    JD</div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-white border border-slate-100 rounded shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="h-4 w-4 rounded border border-slate-300"></div>
                                        <span class="text-sm text-slate-700">Configurer l'entreprise</span>
                                    </div>
                                    <span class="text-xs text-[#005F02] bg-[#F2E3BB]/30 px-2 py-0.5 rounded">Fait</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white border border-slate-100 rounded shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="h-4 w-4 rounded border border-slate-300"></div>
                                        <span class="text-sm text-slate-700">Inviter des agents</span>
                                    </div>
                                    <span class="text-xs text-slate-400">En attente</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white border border-slate-100 rounded shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="h-4 w-4 rounded border border-slate-300"></div>
                                        <span class="text-sm text-slate-700">Créer un formulaire</span>
                                    </div>
                                    <iconify-icon icon="solar:arrow-right-linear" class="h-4 w-4 text-slate-300"></iconify-icon>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Links -->
            <div class="mt-24 grid grid-cols-1 gap-8 border-t border-white/10 pt-12 md:grid-cols-4">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 text-white mb-4">
                        <iconify-icon icon="solar:layers-minimalistic-linear" class="text-[#F2E3BB] h-5 w-5"></iconify-icon>
                        <span class="text-lg font-medium">Manexo</span>
                    </div>
                    <p class="text-xs text-white/50">
                        800 Av. de la République<br>
                        75011 Paris, France
                    </p>
                    <div class="mt-4 flex gap-2 text-white/50">
                        <iconify-icon icon="solar:shield-check-linear" class="h-4 w-4"></iconify-icon>
                        <span class="text-[10px]">GDPR COMPLIANT</span>
                    </div>
                </div>

                <div class="col-span-1 md:col-start-3">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-white/40 mb-4">Menu</h3>
                    <ul class="space-y-3 text-xs text-white/70">
                        <li><a href="#" class="hover:text-[#F2E3BB]">Avantages</a></li>
                        <li><a href="#" class="hover:text-[#F2E3BB]">Fonctionnalités</a></li>
                        <li><a href="#" class="hover:text-[#F2E3BB]">Comment ça marche</a></li>
                    </ul>
                </div>

                <div class="col-span-1">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-white/40 mb-4">Contact</h3>
                    <ul class="space-y-3 text-xs text-white/70">
                        <li>01 23 45 67 89</li>
                        <li><a href="mailto:contact@manexo.io" class="hover:text-[#F2E3BB]">contact@manexo.io</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/5 pt-8 md:flex-row">
                <p class="text-[10px] text-white/30">© 2026 Manexo Inc. Tous droits réservés.</p>
                <div class="flex gap-6 text-[10px] text-white/50">
                    <a href="#" class="hover:text-white">Confidentialité</a>
                    <a href="#" class="hover:text-white">Conditions</a>
                    <a href="#" class="hover:text-white">Cookies</a>
                </div>
            </div>
        </div>
    </section>

</body>
</html><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/home.blade.php ENDPATH**/ ?>