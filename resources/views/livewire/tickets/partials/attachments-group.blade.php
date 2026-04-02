{{--
    Renders grouped attachments: images grid first, then files stack.
    Expects: $attachments (array), $variant (theirs|mine|note)
--}}
@php
    $attachments = $attachments ?? [];
    $variant = $variant ?? 'theirs';

    $images = [];
    $files = [];
    $imageExts = ['png', 'jpg', 'jpeg', 'gif', 'webp'];

    foreach ($attachments as $att) {
        $n = is_array($att) ? ($att['name'] ?? '') : '';
        $ext = strtolower(pathinfo($n, PATHINFO_EXTENSION));
        if (in_array($ext, $imageExts, true)) {
            $images[] = $att;
        } else {
            $files[] = $att;
        }
    }
    $imageCount = count($images);

    // Build gallery data for lightbox navigation (JSON array of {url, name})
    $galleryData = [];
    $resolveUrl = function ($att) {
        // Simplified — the attachment-link partial does the real resolution.
        // Here we just need the URLs for the gallery JS array.
        $url = '#';
        $resolveTicketPublicId = function (int $numericId) {
            static $cache = [];
            if (!isset($cache[$numericId])) {
                $cache[$numericId] = \App\Models\Ticket::where('id', $numericId)->value('public_id') ?? $numericId;
            }
            return $cache[$numericId];
        };
        if (!empty($att['path'])) {
            $path = $att['path'];
            if (str_starts_with($path, 'ticket-messages/')) {
                $parts = explode('/', $path, 3);
                if (count($parts) >= 3) {
                    $url = route('tickets.discussion.file', ['ticket' => $resolveTicketPublicId((int) $parts[1]), 'filename' => $parts[2]]);
                }
            } elseif (str_starts_with($path, 'ticket-attachments/')) {
                $parts = explode('/', $path);
                $filename = $parts[count($parts) - 1] ?? basename($path);
                $ticketId = null;
                foreach ($parts as $seg) {
                    if (preg_match('/^ticket-(\d+)$/', $seg, $m)) { $ticketId = (int) $m[1]; break; }
                }
                if ($ticketId) {
                    $url = route('tickets.attachment', ['ticket' => $resolveTicketPublicId($ticketId), 'filename' => $filename]);
                }
            }
            if ($url === '#') $url = asset('storage/' . $path);
        } elseif (!empty($att['url'])) {
            $url = $att['url'];
        }
        return $url;
    };

    foreach ($images as $img) {
        $galleryData[] = [
            'url' => $resolveUrl($img),
            'name' => $img['name'] ?? 'Image',
        ];
    }
    $galleryJson = json_encode($galleryData, JSON_HEX_APOS | JSON_HEX_QUOT);

    // Grid classes based on image count
    $gridClass = match(true) {
        $imageCount === 1 => 'grid-cols-1 max-w-[280px]',
        $imageCount === 2 => 'grid-cols-2 max-w-[340px]',
        $imageCount === 3 => 'grid-cols-2 max-w-[340px]',
        default           => 'grid-cols-2 max-w-[340px]',
    };
    $maxVisibleImages = 4;
    $extraImages = max(0, $imageCount - $maxVisibleImages);
@endphp

@if($imageCount > 0 || count($files) > 0)
    <div class="mt-2 border-t border-dashed pt-2.5 {{ $variant === 'note' ? 'border-amber-200/35' : ($variant === 'mine' ? 'border-[color:color-mix(in_srgb,var(--accent)_34%,#e2e8f0)]' : 'border-slate-200/40') }}">
        {{-- ═══ IMAGES GRID ═══ --}}
        @if($imageCount > 0)
            <div class="grid {{ $gridClass }} gap-1.5 mb-{{ count($files) > 0 ? '2.5' : '0' }}"
                 x-data="{ __gallery: {{ $galleryJson }} }">
                @foreach($images as $idx => $att)
                    @if($idx < $maxVisibleImages)
                        <div class="{{ $imageCount === 1 ? 'aspect-auto max-h-[220px]' : ($imageCount === 3 && $idx === 0 ? 'row-span-2 aspect-[3/4]' : 'aspect-square') }} relative overflow-hidden rounded-xl"
                             x-data="{ __galleryIndex: {{ $idx }} }">
                            @if($idx === $maxVisibleImages - 1 && $extraImages > 0)
                                {{-- Last visible tile with "+N" overlay --}}
                                @include('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => $variant])
                                <div class="absolute inset-0 bg-slate-900/50 flex items-center justify-center pointer-events-none rounded-xl">
                                    <span class="text-white text-xl font-bold drop-shadow">+{{ $extraImages }}</span>
                                </div>
                            @else
                                @include('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => $variant])
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- ═══ FILES STACK ═══ --}}
        @if(count($files) > 0)
            <div class="space-y-2">
                @foreach($files as $att)
                    @include('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => $variant])
                @endforeach
            </div>
        @endif
    </div>
@endif
