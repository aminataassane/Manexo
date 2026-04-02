<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>404 – <?php echo e(__('Page introuvable')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Mona Sans", sans-serif;
            font-optical-sizing: auto;
            font-style: normal;
            font-variation-settings: "wdth" 100;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 flex flex-col items-center justify-center px-4 py-8" style="--accent: <?php echo e(request()->attributes->get('currentOrganization')?->primary_color ?? env('MANEXO_DEFAULT_ACCENT', '#005F02')); ?>;">
    <div class="w-full max-w-md text-center">
        
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl border border-blue-200 bg-blue-50 text-blue-600 shadow-sm">
            <iconify-icon icon="solar:map-arrow-square-bold-duotone" width="44"></iconify-icon>
        </div>

        
        <h1 class="mt-6 text-4xl font-bold tracking-tight text-slate-900">
            <span class="text-slate-400">404</span>
            <span class="mx-2 text-slate-300">|</span>
            <span><?php echo e(__('Page introuvable')); ?></span>
        </h1>
        <p class="mt-3 text-sm leading-relaxed text-slate-600">
            <?php echo e(__('La page que vous recherchez n\'existe pas ou a été déplacée.')); ?>

        </p>

        
        <?php
            $backUrl = url()->previous() !== url()->current() ? url()->previous() : (auth()->check() ? route('tickets.index') : route('home'));
        ?>
        <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
            <a href="<?php echo e($backUrl); ?>"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50">
                <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                <?php echo e(__('Retour')); ?>

            </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('tickets.index')); ?>"
                   class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-opacity hover:opacity-90"
                   style="background: var(--accent);">
                    <iconify-icon icon="solar:inbox-linear" width="18"></iconify-icon>
                    <?php echo e(__('Mes tickets')); ?>

                </a>
            <?php else: ?>
                <a href="<?php echo e(route('home')); ?>"
                   class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-opacity hover:opacity-90"
                   style="background: var(--accent);">
                    <iconify-icon icon="solar:home-2-linear" width="18"></iconify-icon>
                    <?php echo e(__('Accueil')); ?>

                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\errors\404.blade.php ENDPATH**/ ?>