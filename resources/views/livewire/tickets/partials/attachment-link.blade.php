@php
    $att = $att ?? [];
    $variant = $variant ?? 'theirs'; // theirs | mine | note
    $name = is_array($att) ? ($att['name'] ?? 'Fichier') : 'Fichier';
    $url = '#';
    // Resolve ticket public_id from numeric ID in storage path
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
    $size = null;
    if (is_array($att) && !empty($att['size'])) {
        $bytes = (int) $att['size'];
        $size = $bytes >= 1024 * 1024 ? number_format($bytes / 1024 / 1024, 1) . ' MB' : number_format($bytes / 1024, 1) . ' KB';
    }
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'], true);
    $icon = $isImage ? 'solar:gallery-linear' : 'solar:file-text-linear';
    $showImagePreview = $isImage && $url !== '#';
@endphp

@if($showImagePreview)
    <div class="inline-block max-w-[220px]">
        <a href="{{ $url }}" target="_blank" rel="noopener" class="block rounded-xl overflow-hidden border border-slate-200/80 shadow-sm transition-shadow hover:shadow-md">
            <img src="{{ $url }}" alt="{{ $name }}" class="block w-full h-auto max-h-40 object-contain object-center" loading="lazy">
        </a>
        <a href="{{ $url }}" target="_blank" rel="noopener" class="mt-1.5 flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-800 transition-colors">
            <span class="truncate font-medium">{{ $name }}</span>
            <iconify-icon icon="solar:download-linear" class="shrink-0 opacity-70" width="12"></iconify-icon>
        </a>
    </div>
@else
    <a href="{{ $url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2.5 min-w-0 rounded-xl border border-slate-200/80 bg-slate-50/50 hover:bg-slate-50 p-2.5 transition-colors max-w-[220px] text-slate-700 shadow-sm">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 bg-white text-slate-500 border border-slate-200/60">
            <iconify-icon icon="{{ $icon }}" width="16"></iconify-icon>
        </div>
        <div class="min-w-0 flex-1 text-left">
            <p class="text-xs font-medium truncate leading-tight">{{ $name }}</p>
            @if($size)
                <p class="text-[10px] text-slate-500 mt-0.5">{{ $size }}</p>
            @endif
        </div>
        <iconify-icon icon="solar:download-linear" class="shrink-0 text-slate-400" width="14"></iconify-icon>
    </a>
@endif
