<div class="w-full max-w-full min-w-0 mx-auto">

    
    <div class="page-header">
        <div class="min-w-0">
            <h1 class="page-title"><?php echo e(__('Base de connaissances')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('Trouvez des réponses à vos questions avant de créer un ticket.')); ?></p>
        </div>
    </div>

    
    <div class="mb-8 sm:mb-10 max-w-xl">
        <div class="relative">
            <iconify-icon icon="solar:magnifer-linear" width="18" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></iconify-icon>
            <input
                type="search"
                wire:model.live.debounce.600ms="search"
                wire:loading.attr="disabled"
                wire:target="search"
                placeholder="<?php echo e(__('Rechercher un article...')); ?>"
                autocomplete="off"
                class="w-full rounded-2xl border border-slate-200 bg-white py-3.5 pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--accent)]/15 transition-all"
            />
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search): ?>
                <button type="button" wire:click="$set('search', '')" wire:loading.attr="disabled" wire:target="search" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors" aria-label="<?php echo e(__('Effacer')); ?>">
                    <iconify-icon icon="solar:close-circle-bold" width="18"></iconify-icon>
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($viewingArticle): ?>
        
        <div class="mb-6">
            <button wire:click="closeArticle" wire:loading.attr="disabled" wire:target="closeArticle" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
                <iconify-icon icon="solar:arrow-left-linear" width="16"></iconify-icon>
                <?php echo e(__('Retour aux articles')); ?>

            </button>
        </div>

        <article class="max-w-3xl">
            <div class="flex items-center gap-3 text-xs text-slate-500 mb-5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($viewingArticle->category): ?>
                    <span class="inline-flex items-center gap-1.5 font-medium text-slate-700">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($viewingArticle->category->icon): ?>
                            <iconify-icon icon="<?php echo e($viewingArticle->category->icon); ?>" width="13" class="text-[var(--accent)]"></iconify-icon>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php echo e($viewingArticle->category->name); ?>

                    </span>
                    <span class="text-slate-300">&middot;</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($viewingArticle->published_at): ?>
                    <span><?php echo e($viewingArticle->published_at->translatedFormat('d M Y')); ?></span>
                    <span class="text-slate-300">&middot;</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span><?php echo e($viewingArticle->view_count); ?> <?php echo e(__('vues')); ?></span>
            </div>

            <h1 class="text-2xl font-bold text-slate-900 leading-tight"><?php echo e($viewingArticle->title); ?></h1>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($viewingArticle->creator): ?>
                <p class="mt-3 text-sm text-slate-500"><?php echo e(__('Par')); ?> <span class="font-medium text-slate-700"><?php echo e($viewingArticle->creator->name); ?></span></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="mt-8 prose prose-slate prose-sm max-w-none
                [&_h2]:text-lg [&_h2]:font-bold [&_h2]:text-slate-900 [&_h2]:mt-8 [&_h2]:mb-3
                [&_h3]:text-base [&_h3]:font-semibold [&_h3]:text-slate-800 [&_h3]:mt-6 [&_h3]:mb-2
                [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:my-3
                [&_ol]:list-decimal [&_ol]:pl-6 [&_ol]:my-3
                [&_li]:my-1
                [&_blockquote]:border-l-2 [&_blockquote]:border-slate-200 [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:text-slate-500 [&_blockquote]:my-4
                [&_hr]:border-slate-100 [&_hr]:my-8
                [&_p]:my-3 [&_p]:leading-relaxed
                [&_a]:text-[var(--accent)] [&_a]:underline [&_a]:underline-offset-2
                [&_strong]:font-semibold
                [&_code]:text-sm [&_code]:bg-slate-50 [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:rounded"
            ><?php echo $viewingArticle->safeHtml(); ?></div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($viewingArticle->keywords)): ?>
                <div class="mt-10 pt-6 border-t border-slate-100 flex items-center gap-2 flex-wrap">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $viewingArticle->keywords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <span class="text-xs text-slate-500 bg-slate-50 border border-slate-100 rounded-lg px-2.5 py-1"><?php echo e($kw); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </article>

    <?php else: ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

            
            <div class="lg:col-span-3">
                <div class="lg:sticky lg:top-24 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider"><?php echo e(__('Catégories')); ?></h3>
                    </div>
                    <div class="p-2">
                        <button
                            wire:click="filterByCategory(null)"
                            wire:loading.attr="disabled"
                            wire:target="filterByCategory"
                            class="w-full flex items-center justify-between gap-2 rounded-xl px-3.5 py-2.5 text-sm transition-all <?php echo e(!$categoryId ? 'bg-[var(--accent-soft)] text-slate-900 font-semibold' : 'text-slate-600 hover:bg-slate-50'); ?>"
                        >
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:layers-bold-duotone" width="17" class="<?php echo e(!$categoryId ? 'text-[var(--accent)]' : 'text-slate-400'); ?>"></iconify-icon>
                                <?php echo e(__('Toutes')); ?>

                            </span>
                            <span class="text-xs font-medium tabular-nums <?php echo e(!$categoryId ? 'text-[var(--accent)]' : 'text-slate-400'); ?>"><?php echo e($articles->count()); ?></span>
                        </button>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <button
                                wire:click="filterByCategory(<?php echo e($cat->id); ?>)"
                                wire:loading.attr="disabled"
                                wire:target="filterByCategory"
                                class="w-full flex items-center justify-between gap-2 rounded-xl px-3.5 py-2.5 text-sm transition-all <?php echo e($categoryId === $cat->id ? 'bg-[var(--accent-soft)] text-slate-900 font-semibold' : 'text-slate-600 hover:bg-slate-50'); ?>"
                            >
                                <span class="flex items-center gap-2.5 min-w-0">
                                    <iconify-icon icon="<?php echo e($cat->icon ?: 'solar:folder-bold-duotone'); ?>" width="17" class="<?php echo e($categoryId === $cat->id ? 'text-[var(--accent)]' : 'text-slate-400'); ?> shrink-0"></iconify-icon>
                                    <span class="truncate"><?php echo e($cat->name); ?></span>
                                </span>
                                <span class="text-xs font-medium tabular-nums <?php echo e($categoryId === $cat->id ? 'text-[var(--accent)]' : 'text-slate-400'); ?>"><?php echo e($cat->articles_count); ?></span>
                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="lg:col-span-9">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($articles->isEmpty()): ?>
                    <div class="rounded-2xl border border-slate-200 bg-white py-20 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-50 text-slate-400 mb-5">
                            <iconify-icon icon="solar:book-2-linear" width="26"></iconify-icon>
                        </div>
                        <p class="text-sm font-semibold text-slate-700"><?php echo e(__('Aucun article trouvé')); ?></p>
                        <p class="text-xs text-slate-500 mt-1.5 max-w-xs mx-auto">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search): ?>
                                <?php echo e(__('Essayez de modifier votre recherche.')); ?>

                            <?php else: ?>
                                <?php echo e(__('Les articles apparaîtront ici une fois publiés.')); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <button
                                wire:click="viewArticle(<?php echo e($article->id); ?>)"
                                wire:loading.attr="disabled"
                                wire:target="viewArticle"
                                class="group flex flex-col rounded-2xl border border-slate-200 bg-white text-left transition-all duration-200 hover:border-slate-300 hover:shadow-md"
                            >
                                
                                <div class="flex-1 p-5 sm:p-6">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($article->category): ?>
                                        <div class="mb-3">
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($article->category->icon): ?>
                                                    <iconify-icon icon="<?php echo e($article->category->icon); ?>" width="13" class="text-slate-400"></iconify-icon>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php echo e($article->category->name); ?>

                                            </span>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <h4 class="text-[15px] font-bold text-slate-900 leading-snug line-clamp-2 group-hover:text-[var(--accent)] transition-colors"><?php echo e($article->title); ?></h4>

                                    <p class="mt-2.5 text-[13px] text-slate-500 leading-relaxed line-clamp-3"><?php echo e($article->excerpt(120)); ?></p>
                                </div>

                                
                                <div class="px-5 sm:px-6 py-3.5 border-t border-slate-100 flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 text-[11px] text-slate-400 min-w-0">
                                        <span><?php echo e($article->view_count); ?> <?php echo e(__('vues')); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($article->keywords)): ?>
                                            <span class="truncate hidden sm:inline"><?php echo e(implode(' · ', array_slice($article->keywords, 0, 2))); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <iconify-icon icon="solar:arrow-right-linear" width="15" class="text-slate-300 group-hover:text-[var(--accent)] shrink-0 transition-colors"></iconify-icon>
                                </div>
                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\knowledge-base\index.blade.php ENDPATH**/ ?>