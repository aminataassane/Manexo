<?php
    $att = $att ?? [];
    $variant = $variant ?? 'theirs'; // theirs | mine | note
    $name = is_array($att) ? ($att['name'] ?? 'Fichier') : 'Fichier';
    $url = '#';

    $resolveTicketPublicId = function (int $numericId) {
        static $cache = [];
        if (!isset($cache[$numericId])) {
            $cache[$numericId] = \App\Models\Ticket::where('id', $numericId)->value('public_id') ?? $numericId;
        }
        return $cache[$numericId];
    };

    if (is_array($att)) {
        if (!empty($att['path'])) {
            $path = $att['path'];
            if (str_starts_with($path, 'ticket-messages/')) {
                $parts = explode('/', $path, 3);
                if (count($parts) >= 3) {
                    $ticketPublicId = $resolveTicketPublicId((int) $parts[1]);
                    $url = route('tickets.discussion.file', ['ticket' => $ticketPublicId, 'filename' => $parts[2]]);
                } else {
                    $url = asset('storage/' . $path);
                }
            } elseif (str_starts_with($path, 'ticket-attachments/')) {
                $parts = explode('/', $path);
                $filename = $parts[count($parts) - 1] ?? basename($path);
                $ticketId = null;
                foreach ($parts as $seg) {
                    if (preg_match('/^ticket-(\d+)$/', $seg, $m)) {
                        $ticketId = (int) $m[1];
                        break;
                    }
                }
                if ($ticketId) {
                    $ticketPublicId = $resolveTicketPublicId($ticketId);
                    $url = route('tickets.attachment', ['ticket' => $ticketPublicId, 'filename' => $filename]);
                } else {
                    $url = asset('storage/' . $path);
                }
            } else {
                $url = asset('storage/' . $path);
            }
        } elseif (!empty($att['url'])) {
            $url = $att['url'];
        }
    }

    $downloadUrl = $url !== '#' ? $url . (str_contains($url, '?') ? '&' : '?') . 'download=1' : '#';
    $size = null;
    if (is_array($att) && !empty($att['size'])) {
        $bytes = (int) $att['size'];
        $size = $bytes >= 1024 * 1024 ? number_format($bytes / 1024 / 1024, 1) . ' MB' : number_format($bytes / 1024, 1) . ' KB';
    }
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'], true);
    $isPdf = $ext === 'pdf';
    $icon = match(true) {
        $isImage => 'solar:gallery-linear',
        $isPdf   => 'solar:file-text-linear',
        in_array($ext, ['doc', 'docx']) => 'solar:document-text-linear',
        in_array($ext, ['xls', 'xlsx', 'csv']) => 'solar:chart-square-linear',
        in_array($ext, ['zip', 'rar', '7z']) => 'solar:archive-linear',
        in_array($ext, ['txt']) => 'solar:notes-linear',
        default  => 'solar:file-text-linear',
    };
    $extLabel = strtoupper($ext);

    $fileRowClass = match ($variant) {
        'note' => 'bg-amber-50/60 hover:bg-amber-50/85',
        'mine' => 'bg-[color-mix(in_srgb,var(--accent-soft)_70%,white)] hover:bg-[color-mix(in_srgb,var(--accent-soft)_95%,white)]',
        default => 'bg-slate-50/95 hover:bg-slate-100/95',
    };
    $iconWrapClass = match ($variant) {
        'note' => 'bg-white/75 text-amber-800/75 ring-amber-200/45 shadow-[0_1px_2px_rgba(180,83,9,0.06)]',
        'mine' => 'bg-white text-[var(--accent)] ring-[color:color-mix(in_srgb,var(--accent)_20%,#e2e8f0)] shadow-[0_1px_2px_rgba(15,23,42,0.04)]',
        default => 'bg-slate-100/90 text-slate-600 ring-slate-200/70 shadow-[0_1px_2px_rgba(15,23,42,0.03)]',
    };
    $metaClass = match ($variant) {
        'note' => 'text-amber-900/50',
        'mine' => 'text-slate-600',
        default => 'text-slate-500',
    };
    $nameClass = match ($variant) {
        'note' => 'text-amber-950',
        'mine' => 'text-slate-800',
        default => 'text-slate-800',
    };
    $actionPillClass = match ($variant) {
        'note' => 'bg-white/85 ring-amber-200/45',
        'mine' => 'bg-white ring-[color:color-mix(in_srgb,var(--accent)_16%,#e2e8f0)]',
        default => 'bg-white ring-slate-200/60',
    };
    $actionBtnClass = match ($variant) {
        'mine' => 'flex h-8 w-8 items-center justify-center rounded-full text-[var(--accent)] transition-colors hover:bg-[var(--accent-soft)]',
        'note' => 'flex h-8 w-8 items-center justify-center rounded-full text-amber-900/65 transition-colors hover:bg-amber-100/90 hover:text-amber-950',
        default => 'flex h-8 w-8 items-center justify-center rounded-full text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900',
    };
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isImage && $url !== '#'): ?>
    
    <div class="att-image-tile group/img relative cursor-pointer overflow-hidden rounded-xl <?php echo e($variant === 'mine' ? 'border border-[color:color-mix(in_srgb,var(--accent)_18%,#e2e8f0)] bg-[color-mix(in_srgb,var(--accent-soft)_45%,white)]' : ($variant === 'note' ? 'border border-amber-200/50 bg-amber-50/40' : 'border border-slate-200/55 bg-slate-50')); ?>"
         @click="$dispatch('open-attachment-preview', {
            url: '<?php echo e($url); ?>',
            name: '<?php echo e(e($name)); ?>',
            type: 'image',
            gallery: typeof __gallery !== 'undefined' ? __gallery : [],
            index: typeof __galleryIndex !== 'undefined' ? __galleryIndex : 0
         })">
        <img src="<?php echo e($url); ?>" alt="<?php echo e($name); ?>"
             class="block w-full h-full object-cover transition-transform duration-200 group-hover/img:scale-105"
             loading="lazy">
        
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent
                    opacity-0 group-hover/img:opacity-100 transition-opacity duration-200
                    flex flex-col items-start justify-end p-2.5">
            <span class="text-[11px] font-medium text-white truncate max-w-full drop-shadow"><?php echo e($name); ?></span>
        </div>
        
        <div class="absolute top-1.5 right-1.5 flex gap-1 opacity-0 group-hover/img:opacity-100 transition-opacity duration-200">
            <a href="<?php echo e($downloadUrl); ?>"
               @click.stop
               title="Télécharger"
               class="w-7 h-7 rounded-lg bg-white/85 hover:bg-white text-slate-600 flex items-center justify-center shadow-sm backdrop-blur-sm transition-colors">
                <iconify-icon icon="solar:download-minimalistic-linear" width="13"></iconify-icon>
            </a>
        </div>
    </div>
<?php else: ?>
    
    <div class="attachment-file-row flex max-w-full cursor-default items-center gap-2.5 rounded-2xl px-2.5 py-2 transition-colors duration-200 sm:gap-3 sm:px-3 sm:py-2.5 <?php echo e($fileRowClass); ?> group/file">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl ring-1 <?php echo e($iconWrapClass); ?>">
            <iconify-icon icon="<?php echo e($icon); ?>" width="18" class="opacity-90"></iconify-icon>
        </div>
        <div class="min-w-0 flex-1 py-0.5">
            <p class="truncate text-[13px] font-medium leading-snug tracking-tight <?php echo e($nameClass); ?>"><?php echo e($name); ?></p>
            <p class="mt-0.5 text-[11px] font-normal tabular-nums <?php echo e($metaClass); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($extLabel): ?><span class="uppercase tracking-wide"><?php echo e($extLabel); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($size): ?><span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($extLabel): ?><span class="mx-1 opacity-40">·</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php echo e($size); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </p>
        </div>
        <div class="flex shrink-0 items-center gap-0.5 self-center rounded-full p-0.5 shadow-sm ring-1 backdrop-blur-[2px] transition-opacity duration-200 opacity-80 group-hover/file:opacity-100 <?php echo e($actionPillClass); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPdf): ?>
                <button type="button"
                        @click="$dispatch('open-attachment-preview', { url: '<?php echo e($url); ?>', name: '<?php echo e(e($name)); ?>', type: 'pdf' })"
                        title="<?php echo e(__('Aperçu')); ?>"
                        class="<?php echo e($actionBtnClass); ?>">
                    <iconify-icon icon="solar:eye-linear" width="16"></iconify-icon>
                </button>
            <?php elseif(!$isImage): ?>
                <a href="<?php echo e($url); ?>" target="_blank" rel="noopener"
                   title="<?php echo e(__('Ouvrir')); ?>"
                   class="<?php echo e($actionBtnClass); ?>">
                    <iconify-icon icon="solar:square-top-down-linear" width="16"></iconify-icon>
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <a href="<?php echo e($downloadUrl); ?>"
               title="<?php echo e(__('Télécharger')); ?>"
               class="<?php echo e($actionBtnClass); ?>">
                <iconify-icon icon="solar:download-minimalistic-linear" width="16"></iconify-icon>
            </a>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\tickets\partials\attachment-link.blade.php ENDPATH**/ ?>