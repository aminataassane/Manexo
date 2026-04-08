<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full antialiased bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=5">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(is_string($title ?? null) ? __($title) : 'Manexo'); ?></title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('assets/Logo(1).png')); ?>">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>


    <script defer src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap"></noscript>

    <style>
        /* Typography: Mona Sans uniquement */
        body, .font-serif {
            font-family: "Mona Sans", sans-serif;
            font-optical-sizing: auto;
            font-style: normal;
            font-variation-settings: "wdth" 100;
        }
        [x-cloak] { display: none !important; }
        html { overflow-x: hidden; }

        /* Animations (tickets / discussions ; pas sur le layout pour éviter 0,5s à chaque wire:navigate) */
        @keyframes subtleFade { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
        .animate-enter { animation: subtleFade 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @media (prefers-reduced-motion: reduce) {
            .animate-enter { animation: none; opacity: 1; transform: none; }
        }
        html.manexo-is-navigating .manexo-app-main-scroll { pointer-events: none; }
        html.manexo-is-navigating .manexo-content-wrap {
            opacity: 0.93;
            transform: translateY(1px);
            transition: opacity 0.06s ease-out, transform 0.06s ease-out;
        }
        @media (prefers-reduced-motion: reduce) {
            html.manexo-is-navigating .manexo-content-wrap { opacity: 1; transform: none; transition: none; }
        }

        /* Livewire navigate progress bar */
        [x-ref="progressBar"] {
            height: 3px !important;
            background-color: var(--accent) !important;
            box-shadow: 0 0 8px var(--accent), 0 0 2px var(--accent) !important;
            transition: width 0.1s ease !important;
        }

        /* Organization accent color */
        ::selection { background: var(--accent-soft); color: var(--accent); }
        /* En sidebar repliée, on neutralise les mini-tooltips (évite texte qui déborde) */
        aside .group > .absolute.left-full { display: none !important; }

        /* ─── Print: hide shell UI, full-width content ─── */
        @media print {
            body { overflow: visible !important; height: auto !important; display: block !important; }
            aside, header,
            .fixed.inset-0,                 /* mobile overlay */
            .pointer-events-none.fixed      /* bg safety layer */
            { display: none !important; }
            main {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                position: static !important;
            }
            .page-content-safe {
                overflow: visible !important;
                padding: 10px !important;
            }
        }
    </style>
</head>
<?php
    // currentOrganization is injected by EnsureOrganizationIsSelected middleware.
    $currentOrganization = $currentOrganization ?? request()->attributes->get('currentOrganization');
    $defaultAccent = env('MANEXO_DEFAULT_ACCENT', '#005F02');
    $accent = ($currentOrganization?->primary_color ?: $defaultAccent);
?>
<body
    class="manexo-fluid-root flex h-screen w-full min-h-0 overflow-hidden bg-slate-50 text-slate-900"
    data-echo-enabled="1"
    style="
        --accent: <?php echo e($accent); ?>;
        --accent-soft: color-mix(in srgb, var(--accent) 15%, white);
        --accent-soft-2: color-mix(in srgb, var(--accent) 25%, white);
        --accent-dark: color-mix(in srgb, var(--accent) 20%, black);
        --accent-ring: color-mix(in srgb, var(--accent) 20%, transparent);
        --livewire-progress-bar-color: var(--accent);
    "
    x-data="{ sidebarOpen: true, mobileOpen: false }"
    x-init="sidebarOpen = (localStorage.getItem('manexo_sidebar') !== 'false'); document.body.style.setProperty('--manexo-shell-offset', sidebarOpen ? 'var(--manexo-sidebar-expanded)' : 'var(--manexo-sidebar-collapsed)'); document.addEventListener('livewire:navigated', () => { mobileOpen = false })"
    x-effect="localStorage.setItem('manexo_sidebar', sidebarOpen); document.body.style.setProperty('--manexo-shell-offset', sidebarOpen ? 'var(--manexo-sidebar-expanded)' : 'var(--manexo-sidebar-collapsed)')"
>

    <!-- Support Session Banner -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($activeSupportSession) && $activeSupportSession): ?>
        <div
            class="fixed top-0 left-0 right-0 z-[60] bg-amber-500 text-white text-center py-2 px-4 text-sm font-medium shadow-lg"
            x-data="{
                expiresAt: new Date('<?php echo e($activeSupportSession->expires_at->toIso8601String()); ?>').getTime(),
                remaining: '',
                expired: false,
                init() {
                    this.tick();
                    setInterval(() => this.tick(), 1000);
                },
                tick() {
                    const diff = this.expiresAt - Date.now();
                    if (diff <= 0) {
                        this.expired = true;
                        this.remaining = '0:00';
                        window.location.href = '<?php echo e(route('platform-admin.support-sessions')); ?>';
                        return;
                    }
                    const mins = Math.floor(diff / 60000);
                    const secs = Math.floor((diff % 60000) / 1000);
                    this.remaining = mins + ':' + String(secs).padStart(2, '0');
                }
            }"
        >
            <div class="flex items-center justify-center gap-3">
                <iconify-icon icon="solar:headphones-round-bold" width="16"></iconify-icon>
                <span>
                    <?php echo e(__('super_admin.support.active_banner_org', ['org' => $activeSupportSession->organization?->name])); ?>

                    —
                    <span x-text="remaining"></span>
                </span>
                <a href="<?php echo e(route('platform-admin.support-sessions')); ?>" class="ml-2 rounded-lg bg-white/20 px-3 py-1 text-xs font-semibold hover:bg-white/30 transition-colors">
                    <?php echo e(__('super_admin.support.end')); ?>

                </a>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Safety: ensure main app background is never tinted by branding -->
    <div class="pointer-events-none fixed inset-0 -z-10 bg-slate-50"></div>

    <!-- Mobile Overlay -->
    <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm md:hidden" @click="mobileOpen = false"></div>

    
    <?php app("livewire")->forceAssetInjection(); ?><div x-persist="<?php echo e('manexo-sidebar'); ?>">
        <?php if (isset($component)) { $__componentOriginalf2e6689b51a6b21db82e75433b87b149 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf2e6689b51a6b21db82e75433b87b149 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf2e6689b51a6b21db82e75433b87b149)): ?>
<?php $attributes = $__attributesOriginalf2e6689b51a6b21db82e75433b87b149; ?>
<?php unset($__attributesOriginalf2e6689b51a6b21db82e75433b87b149); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf2e6689b51a6b21db82e75433b87b149)): ?>
<?php $component = $__componentOriginalf2e6689b51a6b21db82e75433b87b149; ?>
<?php unset($__componentOriginalf2e6689b51a6b21db82e75433b87b149); ?>
<?php endif; ?>
    </div>

    <?php app("livewire")->forceAssetInjection(); ?><div x-persist="<?php echo e('manexo-topbar'); ?>">
        <?php if (isset($component)) { $__componentOriginal875609232ba0794abfa6db87b7f26beb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal875609232ba0794abfa6db87b7f26beb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.topbar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.topbar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal875609232ba0794abfa6db87b7f26beb)): ?>
<?php $attributes = $__attributesOriginal875609232ba0794abfa6db87b7f26beb; ?>
<?php unset($__attributesOriginal875609232ba0794abfa6db87b7f26beb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal875609232ba0794abfa6db87b7f26beb)): ?>
<?php $component = $__componentOriginal875609232ba0794abfa6db87b7f26beb; ?>
<?php unset($__componentOriginal875609232ba0794abfa6db87b7f26beb); ?>
<?php endif; ?>
    </div>

    <!-- MAIN CONTENT: pt = hauteur du header (topbar) pour que le contenu reste sous le topbar -->
        
        <main
            class="manexo-shell-transition flex-1 flex flex-col min-w-0 min-h-0 w-full max-w-full relative z-10 transition-[padding] duration-300 ease-[cubic-bezier(0.25,1,0.5,1)] <?php echo e(isset($activeSupportSession) && $activeSupportSession ? 'pt-24 sm:pt-[6.5rem]' : 'pt-14 sm:pt-16'); ?> pl-0 md:pl-[var(--manexo-shell-offset)]"
    >
        <!-- PAGE BODY (scrollable) -->
        <div class="manexo-app-main-scroll page-content-safe manexo-shell-scroll flex-1 min-h-0 overflow-y-auto overflow-x-hidden custom-scrollbar">
            <div class="mx-auto manexo-content-wrap space-y-[var(--manexo-space-section)]">
                <?php echo e($slot); ?>

            </div>
        </div>
    </main>

    
    <?php if (isset($component)) { $__componentOriginal31151edcc3b903d3e72bd726f72ee28d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31151edcc3b903d3e72bd726f72ee28d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.toast','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.toast'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31151edcc3b903d3e72bd726f72ee28d)): ?>
<?php $attributes = $__attributesOriginal31151edcc3b903d3e72bd726f72ee28d; ?>
<?php unset($__attributesOriginal31151edcc3b903d3e72bd726f72ee28d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31151edcc3b903d3e72bd726f72ee28d)): ?>
<?php $component = $__componentOriginal31151edcc3b903d3e72bd726f72ee28d; ?>
<?php unset($__componentOriginal31151edcc3b903d3e72bd726f72ee28d); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal0dac00f20cc43a67eaab8b64d32d96f8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0dac00f20cc43a67eaab8b64d32d96f8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.confirm-dialog','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.confirm-dialog'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0dac00f20cc43a67eaab8b64d32d96f8)): ?>
<?php $attributes = $__attributesOriginal0dac00f20cc43a67eaab8b64d32d96f8; ?>
<?php unset($__attributesOriginal0dac00f20cc43a67eaab8b64d32d96f8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0dac00f20cc43a67eaab8b64d32d96f8)): ?>
<?php $component = $__componentOriginal0dac00f20cc43a67eaab8b64d32d96f8; ?>
<?php unset($__componentOriginal0dac00f20cc43a67eaab8b64d32d96f8); ?>
<?php endif; ?>

    
    <script>
        if(!window.Echo){var _c={listen:function(){return _c},stopListening:function(){return _c},notification:function(){return _c},listenForWhisper:function(){return _c},subscribed:function(){return _c},error:function(){return _c}};window.Echo={private:function(){return _c},channel:function(){return _c},encryptedPrivate:function(){return _c},join:function(){return _c},leave:function(){},leaveChannel:function(){},leaveAllChannels:function(){},socketId:function(){return null},connector:{pusher:{connection:{state:"stub"}}}}}
    </script>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <script>
        document.addEventListener('livewire:init', function() {
            // Bridge Livewire dispatch('toast') → Alpine window event
            Livewire.on('toast', function(params) {
                var p = Array.isArray(params) ? params[0] : params;
                window.dispatchEvent(new CustomEvent('toast', { detail: p }));
            });
            // Couleur d’accent org : le layout n’est pas re-rendu par Livewire après « Enregistrer »
            Livewire.on('manexo-accent', function(params) {
                var p = Array.isArray(params) ? params[0] : params;
                var accent = p && p.accent ? p.accent : null;
                if (!accent) {
                    return;
                }
                document.body.style.setProperty('--accent', accent);
                document.body.style.setProperty('--accent-soft', 'color-mix(in srgb, ' + accent + ' 15%, white)');
                document.body.style.setProperty('--accent-soft-2', 'color-mix(in srgb, ' + accent + ' 25%, white)');
                document.body.style.setProperty('--accent-dark', 'color-mix(in srgb, ' + accent + ' 20%, black)');
                document.body.style.setProperty('--accent-ring', 'color-mix(in srgb, ' + accent + ' 20%, transparent)');
            });
        });
    </script>
</body>
</html>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\layouts\manexo-app.blade.php ENDPATH**/ ?>