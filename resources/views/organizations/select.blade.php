<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50 antialiased selection:bg-[#F2E3BB] selection:text-[#005F02]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélection d'entreprise - MANEXO</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }

        /* Subtle Entrance */
        .fade-in { animation: fadeIn 0.6s ease-out forwards; opacity: 0; transform: translateY(10px); }
        .fade-in-delay-1 { animation-delay: 0.1s; }
        .fade-in-delay-2 { animation-delay: 0.2s; }
        @keyframes fadeIn { to { opacity: 1; transform: translateY(0); } }

        /* Custom Scrollbar for list if needed */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }

        /* Accent helpers (dynamic via CSS variable --accent) */
        .accent-text { color: var(--accent); }
        .accent-bg { background-color: var(--accent); }

        /* Radio swatch selection */
        .color-radio:checked + label {
            box-shadow: 0 0 0 2px var(--accent), 0 0 0 4px white;
            transform: scale(1.1);
        }
    </style>
</head>

<body class="overflow-hidden flex flex-col items-center justify-center bg-slate-50 w-screen h-screen relative">
    <!-- Ambient Glows (Consistent with previous pages) -->
    <div class="-ml-20 -mt-20 bg-[#bbf7d0] opacity-20 w-96 h-96 rounded-full absolute top-0 left-0 blur-3xl pointer-events-none"></div>
    <div class="-mr-20 -mb-20 bg-[#F2E3BB] opacity-30 w-96 h-96 rounded-full absolute right-0 bottom-0 blur-3xl pointer-events-none"></div>
    <div class="opacity-40 z-0 absolute top-0 right-0 bottom-0 left-0 pointer-events-none"></div>

    <livewire:organizations.selector />

    @livewireScripts
</body>
</html>

