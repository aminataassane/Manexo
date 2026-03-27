<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'organization' => null,
    'title' => null,
    'embed' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'organization' => null,
    'title' => null,
    'embed' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title ?? 'Manexo'); ?></title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('assets/Logo(1).png')); ?>">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,100..900&display=swap" rel="stylesheet">

    <?php
        /** @var \App\Models\Organization|null $organization */
        $org = $organization ?? null;
        $defaultAccent = env('MANEXO_DEFAULT_ACCENT', '#005F02');
        $accent = ($org?->primary_color ?: $defaultAccent);
        $orgName = $org?->name ?? 'Manexo';
        $orgLogo = $org?->logo_path ? asset('storage/' . ltrim($org->logo_path, '/')) : null;
        $isEmbed = (bool) ($embed ?? false);
    ?>

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: "Inter", system-ui, -apple-system, sans-serif;
            font-optical-sizing: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        [x-cloak] { display: none !important; }

        /* Glassmorphism card */
        .mnx-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
        }
        @supports not (backdrop-filter: blur(1px)) {
            .mnx-card { background: #fff; }
        }

        /* Input focus glow */
        .mnx-input {
            transition: all 0.15s ease;
        }
        .mnx-input:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 3px var(--accent-ring);
            outline: none;
        }

        /* Smooth fade-in */
        @keyframes mnx-fade-up {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .mnx-animate-in {
            animation: mnx-fade-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        .mnx-animate-in-delay {
            animation: mnx-fade-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both;
        }

        /* Progress bar fill */
        .mnx-progress-fill {
            transition: width 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Button hover lift */
        .mnx-btn-primary {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .mnx-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px -4px var(--accent-ring);
        }
        .mnx-btn-primary:active {
            transform: translateY(0);
        }

        /* Radio/Checkbox card hover */
        .mnx-option-card {
            transition: all 0.15s ease;
        }
        .mnx-option-card:hover {
            border-color: var(--accent-soft-2);
            background: var(--accent-soft);
        }
        .mnx-option-card.selected {
            border-color: var(--accent);
            background: var(--accent-soft);
        }

        /* Custom radio */
        .mnx-radio {
            appearance: none;
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #cbd5e1;
            border-radius: 50%;
            transition: all 0.15s ease;
            cursor: pointer;
            position: relative;
            flex-shrink: 0;
        }
        .mnx-radio:checked {
            border-color: var(--accent);
            border-width: 6px;
        }
        .mnx-radio:focus {
            box-shadow: 0 0 0 3px var(--accent-ring);
            outline: none;
        }

        /* Custom checkbox */
        .mnx-checkbox {
            appearance: none;
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #cbd5e1;
            border-radius: 6px;
            transition: all 0.15s ease;
            cursor: pointer;
            position: relative;
            flex-shrink: 0;
        }
        .mnx-checkbox:checked {
            border-color: var(--accent);
            background: var(--accent);
        }
        .mnx-checkbox:checked::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 2px;
            width: 6px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        .mnx-checkbox:focus {
            box-shadow: 0 0 0 3px var(--accent-ring);
            outline: none;
        }

        /* Field validation errors */
        .mnx-invalid {
            border-color: #fca5a5 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.08) !important;
        }
        .mnx-field-error {
            font-size: 12px;
            line-height: 1.4;
            font-weight: 500;
            color: #ef4444;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .mnx-field-error::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #ef4444;
            flex-shrink: 0;
        }

        /* Background pattern */
        .mnx-bg-pattern {
            background-image:
                radial-gradient(circle at 20% 50%, var(--accent-soft) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, var(--accent-soft) 0%, transparent 50%);
        }
    </style>
</head>
<body
    class="min-h-screen text-slate-800"
    style="
        --accent: <?php echo e($accent); ?>;
        --accent-soft: color-mix(in srgb, var(--accent) 8%, white);
        --accent-soft-2: color-mix(in srgb, var(--accent) 18%, white);
        --accent-dark: color-mix(in srgb, var(--accent) 20%, black);
        --accent-ring: color-mix(in srgb, var(--accent) 15%, transparent);
    "
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isEmbed): ?>
        
        <div class="fixed inset-0 bg-slate-50 mnx-bg-pattern -z-10"></div>

        
        <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-xl border-b border-slate-100">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orgLogo): ?>
                        <img src="<?php echo e($orgLogo); ?>" alt="<?php echo e($orgName); ?>" class="h-7 w-auto object-contain">
                    <?php else: ?>
                        <div class="h-8 w-8 rounded-lg flex items-center justify-center text-white text-xs font-bold" style="background: var(--accent);">
                            <?php echo e(mb_substr($orgName, 0, 1)); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span class="text-sm font-semibold text-slate-800"><?php echo e($orgName); ?></span>
                </div>
                <div class="flex items-center gap-2 text-slate-400">
                    <iconify-icon icon="solar:shield-check-bold" width="14" class="text-emerald-500"></iconify-icon>
                    <span class="text-[11px] font-medium text-slate-400"><?php echo e(__('Formulaire sécurisé')); ?></span>
                </div>
            </div>
        </header>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo e($slot); ?>


    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isEmbed): ?>
        
        <footer class="py-8 text-center">
            <span class="text-[11px] font-medium text-slate-300 tracking-wide">
                <?php echo e(__('Propulsé par')); ?>

                <span class="font-semibold">Manexo</span>
            </span>
        </footer>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</body>
</html>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/components/manexo-public-layout.blade.php ENDPATH**/ ?>