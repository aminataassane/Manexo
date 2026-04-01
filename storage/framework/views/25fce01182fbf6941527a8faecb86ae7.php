
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['variant' => 'list']));

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

foreach (array_filter((['variant' => 'list']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="animate-pulse space-y-6">
    
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

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant === 'list'): ?>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 4; $i++): ?>
                <div class="rounded-xl border border-slate-100 bg-white p-4">
                    <div class="h-3 bg-slate-100 rounded w-16 mb-2"></div>
                    <div class="h-7 bg-slate-200 rounded w-12"></div>
                </div>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="rounded-2xl border border-slate-100 bg-white overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-3 flex gap-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 5; $i++): ?>
                    <div class="h-3 bg-slate-100 rounded w-20"></div>
                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 8; $i++): ?>
                <div class="border-b border-slate-50 px-5 py-4 flex items-center gap-4">
                    <div class="h-4 w-4 bg-slate-100 rounded"></div>
                    <div class="h-4 bg-slate-<?php echo e($i % 2 === 0 ? '200' : '100'); ?> rounded flex-1 max-w-xs"></div>
                    <div class="h-5 bg-slate-100 rounded-full w-20"></div>
                    <div class="h-3 bg-slate-100 rounded w-24 hidden sm:block"></div>
                    <div class="h-6 w-6 bg-slate-100 rounded-full"></div>
                </div>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

    <?php elseif($variant === 'editor'): ?>
        
        <div class="flex gap-0 rounded-2xl border border-slate-100 bg-white overflow-hidden" style="min-height: 60vh;">
            <div class="w-56 border-r border-slate-100 p-4 space-y-3 hidden lg:block">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 6; $i++): ?>
                    <div class="h-8 bg-slate-100 rounded-lg"></div>
                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="flex-1 p-8 space-y-4">
                <div class="h-6 bg-slate-200 rounded w-48 mb-6"></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 4; $i++): ?>
                    <div class="rounded-xl border border-slate-100 p-4 space-y-2">
                        <div class="h-3 bg-slate-100 rounded w-24"></div>
                        <div class="h-9 bg-slate-50 rounded-lg"></div>
                    </div>
                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="w-72 border-l border-slate-100 p-4 space-y-4 hidden lg:block">
                <div class="h-4 bg-slate-200 rounded w-32 mb-4"></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 5; $i++): ?>
                    <div class="space-y-1.5">
                        <div class="h-3 bg-slate-100 rounded w-20"></div>
                        <div class="h-8 bg-slate-50 rounded-lg"></div>
                    </div>
                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

    <?php elseif($variant === 'discussions'): ?>
        
        <div class="flex gap-0 rounded-2xl border border-slate-100 bg-white overflow-hidden" style="min-height: 60vh;">
            <div class="w-80 border-r border-slate-100 p-3 space-y-2 hidden md:block">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 8; $i++): ?>
                    <div class="flex items-center gap-3 p-3 rounded-xl">
                        <div class="h-10 w-10 bg-slate-100 rounded-full shrink-0"></div>
                        <div class="flex-1 space-y-1.5">
                            <div class="h-3 bg-slate-<?php echo e($i === 0 ? '200' : '100'); ?> rounded w-3/4"></div>
                            <div class="h-2.5 bg-slate-100 rounded w-1/2"></div>
                        </div>
                    </div>
                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="flex-1 p-8 flex items-center justify-center">
                <div class="text-center space-y-3">
                    <div class="h-16 w-16 bg-slate-100 rounded-2xl mx-auto"></div>
                    <div class="h-4 bg-slate-200 rounded w-40 mx-auto"></div>
                    <div class="h-3 bg-slate-100 rounded w-56 mx-auto"></div>
                </div>
            </div>
        </div>

    <?php elseif($variant === 'settings'): ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-3 space-y-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 8; $i++): ?>
                    <div class="h-10 bg-slate-<?php echo e($i === 0 ? '200' : '100'); ?> rounded-xl"></div>
                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="lg:col-span-9 space-y-5">
                <div class="rounded-2xl border border-slate-100 bg-white p-6 space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 5; $i++): ?>
                        <div class="space-y-1.5">
                            <div class="h-3 bg-slate-100 rounded w-24"></div>
                            <div class="h-10 bg-slate-50 rounded-lg"></div>
                        </div>
                    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/components/page-skeleton.blade.php ENDPATH**/ ?>