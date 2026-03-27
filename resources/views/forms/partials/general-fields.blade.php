<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div class="space-y-2">
        <label for="guest_name" class="text-[13px] font-semibold text-slate-700">{{ __('Votre nom') }} <span class="text-red-400">*</span></label>
        <input type="text" name="guest_name" id="guest_name" value="{{ old('guest_name', auth()->user()?->name ?? '') }}"
               class="mnx-input block w-full rounded-xl border border-slate-200 bg-white py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0 transition"
               placeholder="{{ __('Votre nom complet') }}" required>
        @error('guest_name')<p class="text-[12px] text-red-500 mt-1 flex items-center gap-1"><iconify-icon icon="solar:danger-circle-bold" width="13"></iconify-icon> {{ $message }}</p>@enderror
    </div>
    <div class="space-y-2">
        <label for="guest_email" class="text-[13px] font-semibold text-slate-700">{{ __('Votre email') }} <span class="text-red-400">*</span></label>
        <input type="email" name="guest_email" id="guest_email" value="{{ old('guest_email', auth()->user()?->email ?? '') }}"
               class="mnx-input block w-full rounded-xl border border-slate-200 bg-white py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0 transition"
               placeholder="email@exemple.com" required>
        @error('guest_email')<p class="text-[12px] text-red-500 mt-1 flex items-center gap-1"><iconify-icon icon="solar:danger-circle-bold" width="13"></iconify-icon> {{ $message }}</p>@enderror
    </div>
</div>

@if($form->creates_ticket)
    @if(!$form->ticket_category_id)
        <div class="space-y-2">
            <label for="ticket_category_id" class="text-[13px] font-semibold text-slate-700">{{ __('Catégorie') }}</label>
            <div class="relative">
                <select name="ticket_category_id" id="ticket_category_id"
                        class="mnx-input appearance-none block w-full rounded-xl border border-slate-200 bg-white py-3 pl-4 pr-10 text-sm text-slate-900 focus:ring-0 transition cursor-pointer">
                    <option value="">{{ __('Sélectionner…') }}</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected((string) old('ticket_category_id') === (string) $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <iconify-icon icon="solar:alt-arrow-down-linear" width="16"></iconify-icon>
                </div>
            </div>
            @error('ticket_category_id')<p class="text-[12px] text-red-500 mt-1 flex items-center gap-1"><iconify-icon icon="solar:danger-circle-bold" width="13"></iconify-icon> {{ $message }}</p>@enderror
        </div>
    @else
        <input type="hidden" name="ticket_category_id" id="ticket_category_id_fixed" value="{{ (int) $form->ticket_category_id }}">
    @endif
@endif

<div class="space-y-2">
    <label for="subject" class="text-[13px] font-semibold text-slate-700">{{ __('Sujet') }} <span class="text-red-400">*</span></label>
    <input type="text" name="subject" id="subject" value="{{ old('subject') }}"
           class="mnx-input block w-full rounded-xl border border-slate-200 bg-white py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0 transition"
           placeholder="{{ __('Résumez votre demande') }}" required>
    @error('subject')<p class="text-[12px] text-red-500 mt-1 flex items-center gap-1"><iconify-icon icon="solar:danger-circle-bold" width="13"></iconify-icon> {{ $message }}</p>@enderror
</div>

<div class="space-y-2">
    <label for="description" class="text-[13px] font-semibold text-slate-700">{{ __('Description') }} <span class="text-red-400">*</span></label>
    <textarea name="description" id="description" rows="4"
              class="mnx-input block w-full rounded-xl border border-slate-200 bg-white py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0 transition resize-none"
              placeholder="{{ __('Décrivez votre besoin en détail…') }}" required>{{ old('description') }}</textarea>
    @error('description')<p class="text-[12px] text-red-500 mt-1 flex items-center gap-1"><iconify-icon icon="solar:danger-circle-bold" width="13"></iconify-icon> {{ $message }}</p>@enderror
</div>
