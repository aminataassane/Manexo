@php
    $att = $att ?? [];
    $variant = $variant ?? 'theirs'; // theirs | mine | note
    $name = is_array($att) ? ($att['name'] ?? 'Fichier') : 'Fichier';
    $url = '#';
    if (is_array($att)) {
        if (!empty($att['path'])) {
            $path = $att['path'];
            if (str_starts_with($path, 'ticket-messages/')) {
                $parts = explode('/', $path, 3);
                $url = count($parts) >= 3 ? route('tickets.discussion.file', ['ticket' => $parts[1], 'filename' => $parts[2]]) : asset('storage/' . $path);
            }             elseif (str_starts_with($path, 'ticket-attachments/')) {
                $parts = explode('/', $path);
                $filename = $parts[count($parts) - 1] ?? basename($path);
                $ticketId = null;
                foreach ($parts as $seg) {
                    if (preg_match('/^ticket-(\d+)$/', $seg, $m)) {
                        $ticketId = (int) $m[1];
                        break;
                    }
                }
                $url = $ticketId ? route('tickets.attachment', ['ticket' => $ticketId, 'filename' => $filename]) : asset('storage/' . $path);
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
    {{-- Aperçu image : pas de boîte blanche, juste l'image + nom en dessous --}}
    <div class="inline-block max-w-[220px]">
        <a href="{{ $url }}" target="_blank" rel="noopener" class="block rounded-lg overflow-hidden transition-opacity hover:opacity-95">
            <img src="{{ $url }}" alt="{{ $name }}" class="block w-full h-auto max-h-40 object-contain object-center" loading="lazy">
        </a>
        <a href="{{ $url }}" target="_blank" rel="noopener" class="mt-1 flex items-center gap-1.5 text-[10px] text-[#6B7280] hover:text-[#111827] transition-colors">
            <span class="truncate font-medium">{{ $name }}</span>
            <iconify-icon icon="solar:download-linear" class="shrink-0 opacity-70" width="11"></iconify-icon>
        </a>
    </div>
@else
    {{-- Fichier non-image : fond très transparent et neutre --}}
    <a href="{{ $url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 w-full min-w-0 rounded-lg border border-[#E5E7EB]/80 bg-white/30 hover:bg-white/50 p-2 transition-colors max-w-[200px] text-[#374151]">
        <div class="w-7 h-7 rounded-md flex items-center justify-center shrink-0 bg-white/50 text-[#6B7280]">
            <iconify-icon icon="{{ $icon }}" width="14"></iconify-icon>
        </div>
        <div class="min-w-0 flex-1 text-left">
            <p class="text-[11px] font-medium truncate leading-tight">{{ $name }}</p>
            @if($size)
                <p class="text-[9px] text-[#6B7280]">{{ $size }}</p>
            @endif
        </div>
        <iconify-icon icon="solar:download-linear" class="shrink-0 opacity-70 text-[#6B7280]" width="12"></iconify-icon>
    </a>
@endif
