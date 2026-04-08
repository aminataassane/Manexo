
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'initials' => '?',
    'name' => '',
    'email' => null,
    'mentionTag' => null,
    'badges' => [],
    'openLabel' => null,
    'popoverId' => null,
    /** Cible ~44px au toucher sur mobile ; plus compact sur sm+ */
    'sizeClass' => 'min-h-11 min-w-11 h-11 w-11 sm:h-9 sm:w-9 sm:min-h-0 sm:min-w-0',
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
    'initials' => '?',
    'name' => '',
    'email' => null,
    'mentionTag' => null,
    'badges' => [],
    'openLabel' => null,
    'popoverId' => null,
    /** Cible ~44px au toucher sur mobile ; plus compact sur sm+ */
    'sizeClass' => 'min-h-11 min-w-11 h-11 w-11 sm:h-9 sm:w-9 sm:min-h-0 sm:min-w-0',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $badges = is_array($badges ?? null) ? $badges : [];
    $openLabel = $openLabel ?? __('pages.discussions.user_card_open');
    $pid = $popoverId ?? 'muc-' . bin2hex(random_bytes(8));
?>

<div class="relative isolate shrink-0" data-manexo-user-card-root>
    <button
        type="button"
        popovertarget="<?php echo e($pid); ?>"
        class="list-none cursor-pointer touch-manipulation select-none <?php echo e($sizeClass); ?> rounded-full border border-slate-100 bg-slate-100 text-slate-700 inline-flex items-center justify-center text-[10px] sm:text-[10px] font-semibold shrink-0 hover:ring-2 hover:ring-[var(--accent)]/35 hover:bg-slate-50 active:scale-[0.98] transition-all"
        title="<?php echo e($openLabel); ?>"
        aria-label="<?php echo e($openLabel); ?>"
        aria-haspopup="dialog"
    ><?php echo e($initials); ?></button>
    <div
        id="<?php echo e($pid); ?>"
        popover="manual"
        data-manexo-user-card
        role="dialog"
        aria-label="<?php echo e($openLabel); ?>"
        class="pointer-events-auto m-0 w-[min(24rem,calc(100vw-2rem))] max-w-[calc(100vw-2rem)] rounded-2xl border border-slate-200/90 bg-white p-4 text-left shadow-2xl shadow-slate-900/15 ring-1 ring-slate-900/[0.06]"
        style="padding-left: max(1rem, env(safe-area-inset-left, 0px)); padding-right: max(1rem, env(safe-area-inset-right, 0px));"
        onclick="event.stopPropagation()"
    >
        <div class="text-[15px] font-semibold leading-snug text-slate-900 [overflow-wrap:anywhere] break-words"><?php echo e($name); ?></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($email): ?>
            <div class="mt-2 text-xs leading-relaxed text-slate-500 [overflow-wrap:anywhere] break-all"><?php echo e($email); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mentionTag): ?>
            <div class="mt-2 text-sm font-medium text-slate-600 [overflow-wrap:anywhere] break-all">
                <span class="text-slate-400">@</span><?php echo e($mentionTag); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($badges) > 0): ?>
            <div class="mt-3 flex flex-wrap gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $badges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <span class="inline-flex max-w-full items-center rounded-full bg-[var(--accent-soft)] px-2.5 py-1 text-[10px] font-bold leading-tight text-[var(--accent)] ring-1 ring-[var(--accent)]/15 sm:text-[11px] [overflow-wrap:anywhere]">
                        <?php echo e($badge); ?>

                    </span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/components/manexo/user-avatar-popover.blade.php ENDPATH**/ ?>