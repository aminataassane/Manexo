@php
    /** @var \App\Models\Form $form */
    /** @var \App\Models\Organization $organization */
    $embedMode = (bool) ($embed ?? false);
    $pageTitle = $form->public_title ?: $form->name ?: __('Formulaire');
    $pageDesc = $form->public_description ?: null;
    $fields = $form->fields->sortBy('sort_order');
@endphp

<x-manexo-public-layout :organization="$organization" :title="$pageTitle">
    <div class="{{ $embedMode ? 'p-4 sm:p-6' : 'py-10 px-4 sm:px-6 lg:px-8' }}">
        <div class="mx-auto w-full {{ $embedMode ? 'max-w-3xl' : 'max-w-4xl' }}">
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-6 sm:px-8 border-b border-slate-200 bg-gradient-to-br from-white to-slate-50">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="inline-flex items-center gap-2 text-[11px] uppercase tracking-wider font-bold text-slate-500">
                                <span class="h-2 w-2 rounded-full" style="background: var(--accent);"></span>
                                {{ $organization->name ?? 'Manexo' }}
                            </div>
                            <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                                {{ $pageTitle }}
                            </h1>
                            @if($pageDesc)
                                <p class="mt-2 text-sm text-slate-600 max-w-2xl">
                                    {{ $pageDesc }}
                                </p>
                            @endif
                        </div>
                        <div class="hidden sm:flex h-12 w-12 rounded-2xl items-center justify-center"
                             style="background: var(--accent-soft); color: var(--accent);">
                            <iconify-icon icon="solar:ticket-linear" width="22"></iconify-icon>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6 sm:px-8">
                    @if(session('public_form_success'))
                        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 text-emerald-700">
                                    <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-emerald-900">{{ __('Envoyé !') }}</div>
                                    <div class="mt-1 text-sm text-emerald-800">
                                        {{ session('public_form_success') }}
                                        @if(session('public_form_ticket_id'))
                                            <span class="font-semibold">#{{ session('public_form_ticket_id') }}</span>
                                        @endif
                                    </div>
                                    @if(session('public_form_email'))
                                        <div class="mt-1 text-xs text-emerald-700">
                                            {{ __('Email :') }} <span class="font-semibold">{{ session('public_form_email') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('forms.public.submit', ['slug' => $form->slug, 'embed' => $embedMode ? 1 : null]) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- Honeypot --}}
                        <input type="text" name="website" value="" class="hidden" tabindex="-1" autocomplete="off" />

                        @guest
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-slate-700">{{ __('Votre nom') }}</label>
                                    <input type="text" name="guest_name" value="{{ old('guest_name') }}"
                                           class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                           placeholder="{{ __('Ex: Aminata') }}" required>
                                    @error('guest_name')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-slate-700">{{ __('Votre email') }}</label>
                                    <input type="email" name="guest_email" value="{{ old('guest_email') }}"
                                           class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                           placeholder="email@exemple.com" required>
                                    @error('guest_email')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        @endguest

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @if(!$form->ticket_category_id)
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-slate-700">{{ __('Catégorie') }}</label>
                                    <select name="ticket_category_id"
                                            class="appearance-none block w-full rounded-xl border-slate-200 bg-white py-2.5 pl-3 pr-10 text-sm text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] transition-all duration-200 ease-in-out cursor-pointer hover:border-slate-300">
                                        <option value="">{{ __('Choisir…') }}</option>
                                        @foreach($categories as $c)
                                            <option value="{{ $c->id }}" @selected((string) old('ticket_category_id') === (string) $c->id)>{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('ticket_category_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            @else
                                <input type="hidden" name="ticket_category_id" value="{{ (int) $form->ticket_category_id }}">
                            @endif

                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-700">{{ __('Priorité') }}</label>
                                <select name="ticket_priority_id"
                                        class="appearance-none block w-full rounded-xl border-slate-200 bg-white py-2.5 pl-3 pr-10 text-sm text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] transition-all duration-200 ease-in-out cursor-pointer hover:border-slate-300">
                                    @foreach($priorities as $p)
                                        @php $selected = (string) (old('ticket_priority_id') ?: $defaultPriorityId) === (string) $p->id; @endphp
                                        <option value="{{ $p->id }}" @selected($selected)>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                @error('ticket_priority_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">{{ __('Sujet') }}</label>
                            <input type="text" name="subject" value="{{ old('subject') }}"
                                   class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                   placeholder="{{ __('Ex: Problème de connexion') }}" required>
                            @error('subject')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">{{ __('Description') }}</label>
                            <textarea name="description" rows="5"
                                      class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                      placeholder="{{ __('Décrivez votre besoin…') }}" required>{{ old('description') }}</textarea>
                            @error('description')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        @if($fields->where('type', '!=', 'section')->count())
                            <div class="pt-2 border-t border-slate-200">
                                <div class="space-y-5">
                                    @foreach($fields as $f)
                                        @php
                                            $type = (string) $f->type;
                                            $config = is_array($f->configuration) ? $f->configuration : [];
                                            $placeholder = $config['placeholder'] ?? '';
                                            $helpText = $config['help_text'] ?? '';
                                            $options = $config['options'] ?? $f->options ?? [];
                                            $name = "custom[{$f->key}]";
                                            $oldVal = old("custom.{$f->key}");
                                            $isFull = in_array($type, ['textarea', 'section', 'radio', 'file'], true);
                                        @endphp

                                        @if($type === 'section')
                                            <div class="pt-4 pb-1 border-t border-slate-200 first:border-t-0 first:pt-0">
                                                <div class="text-sm font-extrabold text-slate-900">{{ $f->label }}</div>
                                                @if($helpText)
                                                    <p class="mt-1 text-xs text-slate-600">{{ $helpText }}</p>
                                                @endif
                                            </div>
                                        @else
                                            <div class="space-y-1.5">
                                                <label class="text-xs font-semibold text-slate-700">
                                                    {{ $f->label }}
                                                    @if($f->required)
                                                        <span class="text-red-600">*</span>
                                                    @endif
                                                </label>

                                                @if($type === 'textarea')
                                                    <textarea name="{{ $name }}" rows="4"
                                                              class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                                              placeholder="{{ $placeholder }}"
                                                              @required($f->required)>{{ $oldVal }}</textarea>
                                                @elseif($type === 'select')
                                                    <select name="{{ $name }}"
                                                            class="appearance-none block w-full rounded-xl border-slate-200 bg-white py-2.5 pl-3 pr-10 text-sm text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] transition-all duration-200 ease-in-out cursor-pointer hover:border-slate-300"
                                                            @required($f->required)>
                                                        <option value="">{{ $placeholder ?: __('Choisir…') }}</option>
                                                        @foreach((array) $options as $opt)
                                                            <option value="{{ $opt }}" @selected((string) $oldVal === (string) $opt)>{{ $opt }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($type === 'radio')
                                                    <div class="space-y-2">
                                                        @foreach((array) $options as $opt)
                                                            <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2.5 hover:border-[var(--accent)] cursor-pointer transition-colors">
                                                                <input type="radio" name="{{ $name }}" value="{{ $opt }}"
                                                                       class="text-[color:var(--accent)] focus:ring-[color:var(--accent)]/30"
                                                                       @checked((string) $oldVal === (string) $opt)>
                                                                <span class="text-sm text-slate-700">{{ $opt }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @elseif($type === 'checkbox')
                                                    <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5">
                                                        <input type="hidden" name="{{ $name }}" value="0">
                                                        <input type="checkbox" name="{{ $name }}" value="1"
                                                               class="h-4 w-4 rounded border-slate-300 text-[color:var(--accent)] focus:ring-[color:var(--accent)]/30"
                                                               @checked((bool) $oldVal)>
                                                        <span class="text-sm text-slate-700">{{ __('Oui') }}</span>
                                                    </div>
                                                @elseif($type === 'file')
                                                    <input type="file" name="{{ $name }}"
                                                           class="block w-full text-sm file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:bg-slate-900 file:text-white hover:file:opacity-90 transition"
                                                           @if(!empty($config['accept'])) accept="{{ $config['accept'] }}" @endif>
                                                @else
                                                    @php
                                                        $inputType = in_array($type, ['email', 'date', 'datetime', 'number'], true)
                                                            ? ($type === 'datetime' ? 'datetime-local' : $type)
                                                            : 'text';
                                                    @endphp
                                                    <input type="{{ $inputType }}" name="{{ $name }}" value="{{ $oldVal }}"
                                                           class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                                           placeholder="{{ $placeholder }}"
                                                           @required($f->required)>
                                                @endif

                                                @if($helpText)
                                                    <p class="text-[11px] text-slate-500">{{ $helpText }}</p>
                                                @endif

                                                @error("custom.{$f->key}")<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="pt-2 border-t border-slate-200">
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">
                                {{ __('Pièces jointes (optionnel)') }}
                            </div>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-slate-700">{{ __('Fichiers (max 5)') }}</label>
                                    <input type="file" name="files[]" multiple
                                           class="block w-full text-sm file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:bg-slate-900 file:text-white hover:file:opacity-90 transition"
                                    >
                                    @error('files')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                                    @error('files.*')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-slate-700">{{ __('Liens (optionnel)') }}</label>
                                    <input type="url" name="links[]" value="{{ old('links.0') }}"
                                           class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                           placeholder="https://…">
                                    <input type="url" name="links[]" value="{{ old('links.1') }}"
                                           class="mt-2 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                           placeholder="https://…">
                                    @error('links')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                                    @error('links.*')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 flex items-center justify-end gap-3">
                            <button type="submit"
                                    class="h-11 px-5 rounded-2xl text-white text-sm font-extrabold shadow-sm inline-flex items-center gap-2 bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)] transition">
                                <iconify-icon icon="solar:plain-2-linear" width="18"></iconify-icon>
                                {{ __('Envoyer') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if(!$embedMode)
                <div class="mt-6 text-center text-xs text-slate-500">
                    {{ __('Propulsé par Manexo') }}
                </div>
            @endif
        </div>
    </div>
</x-manexo-public-layout>
