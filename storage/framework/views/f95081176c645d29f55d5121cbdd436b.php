<div class="builder-full-bleed h-builder flex flex-col min-w-0 bg-white overflow-hidden" x-data="{ mobileDetailOpen: false }">
    <!-- HEADER -->
    <div class="shrink-0 px-4 sm:px-5 lg:px-6 py-3 bg-white border-b border-slate-200/60">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <a href="<?php echo e(route('admin.forms')); ?>" wire:navigate.hover class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors shrink-0">
                    <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                </a>
                <div class="min-w-0">
                    <h1 class="text-sm font-semibold text-slate-900 truncate"><?php echo e($form->name); ?></h1>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        <?php echo e(trans_choice('forms_builder.response_count', $this->responses->total(), ['count' => $this->responses->total()])); ?>

                    </p>
                </div>
            </div>
            <button type="button" wire:click="exportCsv"
                    class="h-8 px-3 rounded-lg border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-50 transition inline-flex items-center gap-1.5 shrink-0 disabled:opacity-50"
                    wire:loading.attr="disabled" wire:target="exportCsv">
                <span wire:loading.remove wire:target="exportCsv">
                    <iconify-icon icon="solar:document-text-linear" width="15"></iconify-icon>
                </span>
                <span wire:loading wire:target="exportCsv" class="animate-spin">
                    <iconify-icon icon="solar:refresh-linear" width="15"></iconify-icon>
                </span>
                <span wire:loading.remove wire:target="exportCsv"><?php echo e(__('forms_builder.export_csv')); ?></span>
                <span wire:loading wire:target="exportCsv" class="text-slate-400">Génération...</span>
            </button>
        </div>

        <?php
            $formResponseSourceOptions = [
                ['value' => '', 'label' => __('forms_builder.all_sources')],
                ['value' => 'public', 'label' => __('forms_builder.source_public')],
                ['value' => 'internal_assignment', 'label' => __('forms_builder.source_internal_assignment')],
                ['value' => 'internal_team', 'label' => __('forms_builder.source_internal_team')],
                ['value' => 'internal_team_slug', 'label' => __('forms_builder.source_internal_team_slug')],
            ];
            $formResponseSourceLabel = collect($formResponseSourceOptions)->firstWhere('value', (string) ($filterSource ?? ''))['label'] ?? '';
        ?>
        <!-- FILTERS -->
        <div class="mt-2.5 flex flex-wrap items-center gap-2">
            <div class="relative flex-1 min-w-[160px] max-w-xs">
                <iconify-icon icon="solar:magnifer-linear" width="14" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400"></iconify-icon>
                <input type="text" wire:model.live.debounce.300ms="filterSearch"
                       placeholder="<?php echo e(__('forms_builder.filter_search')); ?>"
                       class="input-builder w-full text-[11px] py-2 pl-8 pr-3">
            </div>
            <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['options' => $formResponseSourceOptions,'label' => $formResponseSourceLabel,'selectedValue' => $filterSource ?? '','wire:model.live' => 'filterSource']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($formResponseSourceOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($formResponseSourceLabel),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filterSource ?? ''),'wire:model.live' => 'filterSource']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36)): ?>
<?php $attributes = $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36; ?>
<?php unset($__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36)): ?>
<?php $component = $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36; ?>
<?php unset($__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36); ?>
<?php endif; ?>
            <div class="flex items-center gap-1.5">
                <label class="text-[10px] font-medium text-slate-400 shrink-0"><?php echo e(__('forms_builder.filter_date_from')); ?></label>
                <input type="date" wire:model.live="filterDateFrom"
                       class="input-builder text-[11px] py-1.5 px-2">
            </div>
            <div class="flex items-center gap-1.5">
                <label class="text-[10px] font-medium text-slate-400 shrink-0"><?php echo e(__('forms_builder.filter_date_to')); ?></label>
                <input type="date" wire:model.live="filterDateTo"
                       class="input-builder text-[11px] py-1.5 px-2">
            </div>
        </div>
    </div>

    <!-- SPLIT VIEW -->
    <div class="flex-1 flex overflow-hidden">
        <!-- LEFT: Response list -->
        <div class="w-full lg:w-80 xl:w-96 shrink-0 border-r border-slate-200/60 bg-white overflow-y-auto custom-scrollbar">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->responses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $response): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <?php
                    $isSelected = $selectedResponseId === $response->id;
                    $sourceBadge = match($response->submitted_from) {
                        'public' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => __('forms_builder.source_public')],
                        'internal_assignment' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'label' => __('forms_builder.source_internal_assignment')],
                        'internal_team' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500', 'label' => __('forms_builder.source_internal_team')],
                        'internal_team_slug' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500', 'label' => __('forms_builder.source_internal_team_slug')],
                        default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500', 'label' => __('forms_builder.source_internal')],
                    };
                ?>
                <button
                    type="button"
                    wire:click="selectResponse(<?php echo e($response->id); ?>)"
                    @click="if (window.innerWidth < 1024) mobileDetailOpen = true"
                    class="w-full text-left px-4 py-3 border-b border-slate-100/80 transition-all <?php echo e($isSelected ? 'bg-slate-50 border-l-2 border-l-[var(--accent)]' : 'hover:bg-slate-50/50 border-l-2 border-l-transparent'); ?>"
                >
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-slate-900 truncate">
                                <?php echo e($response->respondent_name ?? $response->user?->name ?? __('Anonyme')); ?>

                            </p>
                            <p class="text-[10px] text-slate-500 mt-0.5 truncate">
                                <?php echo e($response->respondent_email ?? $response->user?->email ?? ''); ?>

                            </p>
                        </div>
                        <span class="shrink-0 px-1.5 py-0.5 rounded-md text-[9px] font-medium <?php echo e($sourceBadge['bg']); ?> <?php echo e($sourceBadge['text']); ?>">
                            <?php echo e($sourceBadge['label']); ?>

                        </span>
                    </div>
                    <div class="flex items-center gap-1.5 mt-1.5 text-[10px] text-slate-400">
                        <span><?php echo e($response->created_at->format('d/m/Y H:i')); ?></span>
                        <span class="text-slate-300">&middot;</span>
                        <span>v<?php echo e($response->form_version); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($response->ticket_id): ?>
                            <span class="text-slate-300">&middot;</span>
                            <span class="text-[var(--accent)]">#<?php echo e($response->ticket_id); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="px-6 py-16 text-center">
                    <iconify-icon icon="solar:inbox-linear" width="32" class="text-slate-300 mb-3"></iconify-icon>
                    <p class="text-xs font-medium text-slate-600"><?php echo e(__('forms_builder.no_responses')); ?></p>
                    <p class="text-[11px] text-slate-400 mt-1"><?php echo e(__('forms_builder.no_responses_hint')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->responses->hasPages()): ?>
                <div class="px-3 py-2.5 border-t border-slate-100">
                    <?php echo e($this->responses->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <!-- RIGHT: Detail panel (desktop) -->
        <div class="hidden lg:flex flex-1 flex-col overflow-y-auto custom-scrollbar bg-slate-50/30">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->selectedResponse): ?>
                <?php echo $__env->make('livewire.admin.partials.form-response-detail', ['response' => $this->selectedResponse], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php else: ?>
                <div class="flex-1 flex flex-col items-center justify-center text-center px-8">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mb-3">
                        <iconify-icon icon="solar:document-text-linear" class="text-slate-400" width="24"></iconify-icon>
                    </div>
                    <p class="text-xs font-medium text-slate-600"><?php echo e(__('forms_builder.select_response')); ?></p>
                    <p class="text-[11px] text-slate-400 mt-1"><?php echo e(__('forms_builder.select_response_hint')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- MOBILE: Detail drawer -->
    <div class="lg:hidden fixed inset-0 z-40" x-show="mobileDetailOpen && <?php echo \Illuminate\Support\Js::from($selectedResponseId)->toHtml() ?>" x-cloak style="display:none;">
        <div class="absolute inset-0 bg-black/20 backdrop-blur-[2px]" @click="mobileDetailOpen = false"></div>
        <div class="absolute right-0 top-0 bottom-0 w-full max-w-[24rem] bg-white shadow-xl border-l border-slate-200/60 flex flex-col"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            <div class="h-12 px-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                <h3 class="text-xs font-semibold text-slate-900"><?php echo e(__('forms_builder.response_detail')); ?></h3>
                <button type="button" @click="mobileDetailOpen = false" class="p-1.5 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition">
                    <iconify-icon icon="solar:close-circle-linear" width="16"></iconify-icon>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->selectedResponse): ?>
                    <?php echo $__env->make('livewire.admin.partials.form-response-detail', ['response' => $this->selectedResponse], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\admin\form-responses.blade.php ENDPATH**/ ?>