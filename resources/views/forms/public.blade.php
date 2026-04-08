@php
    /** @var \App\Models\Form $form */
    /** @var \App\Models\Organization $organization */
    $embedMode = (bool) ($embed ?? false);
    $pageTitle = $form->public_title ?: $form->name ?: __('Formulaire');
    $pageDesc  = $form->public_description ?: null;

    $allFields   = $form->fields->sortBy('sort_order');
    $hasSections = $allFields->where('type', 'section')->count() > 0;

    $steps = [];
    if ($hasSections) {
        $steps[] = ['id' => 'general', 'title' => __('Informations'), 'type' => 'general', 'fields' => []];

        $currentFields = [];
        $currentTitle  = __('Détails');

        foreach ($allFields as $f) {
            if ($f->type === 'section') {
                if (!empty($currentFields)) {
                    $steps[] = [
                        'id'     => 'section-' . \Illuminate\Support\Str::slug($currentTitle) . '-' . rand(100, 999),
                        'title'  => $currentTitle,
                        'type'   => 'custom',
                        'fields' => $currentFields,
                    ];
                }
                $currentTitle  = $f->label;
                $currentFields = [];
            } else {
                $currentFields[] = $f;
            }
        }
        if (!empty($currentFields) || ($currentTitle !== __('Détails') && $currentTitle !== '')) {
            $steps[] = [
                'id'     => 'section-' . \Illuminate\Support\Str::slug($currentTitle) . '-' . rand(100, 999),
                'title'  => $currentTitle,
                'type'   => 'custom',
                'fields' => $currentFields,
            ];
        }
    }
@endphp

<x-manexo-public-layout :organization="$organization" :title="$pageTitle" :embed="$embedMode">

    {{-- Alpine component --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('publicForm', (totalSteps) => ({
                step: 1,
                totalSteps: totalSteps,
                submitting: false,

                clearErrors(container) {
                    container.querySelectorAll('.mnx-field-error').forEach(e => e.remove());
                    container.querySelectorAll('.mnx-invalid').forEach(e => {
                        e.classList.remove('mnx-invalid');
                        e.style.removeProperty('border-color');
                    });
                },

                validate(container) {
                    this.clearErrors(container);
                    let valid = true;
                    const checked = new Set();

                    container.querySelectorAll('input[required], select[required], textarea[required]').forEach(input => {
                        if (input.type === 'hidden') return;
                        if (input.offsetParent === null && input.type !== 'radio') return;

                        if (input.type === 'radio') {
                            if (checked.has(input.name)) return;
                            checked.add(input.name);
                            const sel = 'input[name="' + input.name + '"]:checked';
                            if (!container.querySelector(sel)) {
                                valid = false;
                                const wrap = input.closest('.grid') || input.closest('[class*="space-y"]');
                                if (wrap) this.addError(wrap, @json(__('Veuillez sélectionner une option')));
                            }
                            return;
                        }

                        if (input.type === 'checkbox' && input.name.endsWith('[]')) {
                            if (checked.has(input.name)) return;
                            checked.add(input.name);
                            const sel = 'input[name="' + input.name + '"]:checked';
                            if (!container.querySelector(sel)) {
                                valid = false;
                                const wrap = input.closest('.grid') || input.closest('[class*="space-y"]');
                                if (wrap) this.addError(wrap, @json(__('Veuillez sélectionner au moins une option')));
                            }
                            return;
                        }

                        const val = input.value ? input.value.trim() : '';

                        if (!val) {
                            valid = false;
                            const labelEl = input.closest('[class*="space-y"]')?.querySelector('label');
                            const name = labelEl ? labelEl.textContent.replace(/\*/g, '').trim() : @json(__('Ce champ'));
                            this.markInvalid(input, name + ' ' + @json(__('est obligatoire')));
                            return;
                        }

                        if (input.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                            valid = false;
                            this.markInvalid(input, @json(__('Adresse email invalide')));
                        }
                    });

                    if (!valid) {
                        const first = container.querySelector('.mnx-invalid, .mnx-field-error');
                        if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }

                    return valid;
                },

                markInvalid(input, message) {
                    input.classList.add('mnx-invalid');
                    input.style.borderColor = '#fca5a5';
                    const wrap = input.closest('[class*="space-y"]') || input.parentElement;
                    this.addError(wrap, message);
                },

                addError(wrap, message) {
                    if (!wrap || wrap.querySelector('.mnx-field-error')) return;
                    const p = document.createElement('p');
                    p.className = 'mnx-field-error';
                    p.textContent = message;
                    wrap.appendChild(p);
                },

                clearFieldError(e) {
                    const input = e.target;
                    if (!input.closest || input.type === 'hidden') return;
                    input.classList.remove('mnx-invalid');
                    input.style.removeProperty('border-color');
                    const wrap = input.closest('[class*="space-y"]') || input.parentElement;
                    if (wrap) wrap.querySelectorAll('.mnx-field-error').forEach(el => el.remove());
                },

                next() {
                    const container = document.getElementById('step-' + this.step);
                    if (container && !this.validate(container)) return;
                    this.step++;
                    this.$el.querySelector('.mnx-form-body')?.scrollTo({ top: 0, behavior: 'smooth' });
                },

                prev() {
                    this.step--;
                    this.$el.querySelector('.mnx-form-body')?.scrollTo({ top: 0, behavior: 'smooth' });
                },

                handleSubmit(e) {
                    const body = this.$el.querySelector('.mnx-form-body');
                    if (body && !this.validate(body)) {
                        e.preventDefault();
                        return;
                    }
                    this.submitting = true;
                }
            }));
        });
    </script>

    <div class="flex items-start justify-center {{ $embedMode ? 'p-0 min-h-0' : 'min-h-[calc(100vh-7rem)] py-8 sm:py-12 px-4 sm:px-6' }}">
        <div class="w-full {{ $embedMode ? 'max-w-full' : 'max-w-2xl' }} mnx-animate-in">

            {{-- ==================== SUCCESS STATE ==================== --}}
            @if(session('public_form_success'))
                <div class="mnx-card rounded-2xl border border-white/60 shadow-xl {{ $embedMode ? 'p-8' : 'p-10 sm:p-14' }} mnx-animate-in"
                     x-data="{ show: false, copied: false }" x-init="setTimeout(() => show = true, 100)">

                    {{-- Animated check --}}
                    <div class="text-center">
                        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full mb-6 transition-all duration-700"
                             style="background: var(--accent-soft); color: var(--accent);"
                             :class="show ? 'scale-100 opacity-100' : 'scale-50 opacity-0'">
                            <iconify-icon icon="solar:check-circle-bold-duotone" width="48"></iconify-icon>
                        </div>

                        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight transition-all duration-500 delay-200"
                            :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
                            {{ __('public_form.success_title') }}
                        </h2>

                        <p class="mt-3 text-slate-500 text-[15px] max-w-md mx-auto leading-relaxed transition-all duration-500 delay-300"
                           :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
                            {{ session('public_form_success') }}
                        </p>
                    </div>

                    {{-- Récapitulatif --}}
                    <div class="mt-8 rounded-xl border border-slate-100 bg-slate-50/50 divide-y divide-slate-100 transition-all duration-500 delay-[350ms]"
                         :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">

                        {{-- Référence ticket --}}
                        @if(session('public_form_ticket_ref'))
                            <div class="flex items-center justify-between px-5 py-3.5">
                                <div class="flex items-center gap-2.5 text-sm text-slate-600">
                                    <iconify-icon icon="solar:ticket-bold" width="17" style="color: var(--accent);"></iconify-icon>
                                    {{ __('public_form.ticket_reference') }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-slate-900">{{ session('public_form_ticket_ref') }}</span>
                                    <button type="button"
                                            @click="navigator.clipboard.writeText('{{ session('public_form_ticket_ref') }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors"
                                            :title="copied ? '{{ __('public_form.copied') }}' : '{{ __('public_form.copy') }}'">
                                        <iconify-icon :icon="copied ? 'solar:check-read-linear' : 'solar:copy-linear'" width="14"></iconify-icon>
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- Référence réponse --}}
                        @if(session('public_form_response_ref'))
                            <div class="flex items-center justify-between px-5 py-3.5">
                                <div class="flex items-center gap-2.5 text-sm text-slate-600">
                                    <iconify-icon icon="solar:document-text-bold" width="17" class="text-slate-400"></iconify-icon>
                                    {{ __('public_form.response_reference') }}
                                </div>
                                <span class="text-sm font-semibold text-slate-700">{{ session('public_form_response_ref') }}</span>
                            </div>
                        @endif

                        {{-- Email de confirmation --}}
                        @if(session('public_form_email'))
                            <div class="flex items-center justify-between px-5 py-3.5">
                                <div class="flex items-center gap-2.5 text-sm text-slate-600">
                                    <iconify-icon icon="solar:letter-bold" width="17" class="text-slate-400"></iconify-icon>
                                    {{ __('public_form.confirmation_email') }}
                                </div>
                                <span class="text-sm font-semibold text-slate-700">{{ session('public_form_email') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Suivi --}}
                    <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50/50 px-5 py-4 transition-all duration-500 delay-[400ms]"
                         :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
                        <div class="flex items-start gap-3">
                            <iconify-icon icon="solar:info-circle-bold" width="18" class="text-blue-500 mt-0.5 shrink-0"></iconify-icon>
                            <div class="text-[13px] text-blue-800 leading-relaxed">
                                @if(session('public_form_ticket_ref'))
                                    {{ __('public_form.followup_with_ticket', ['ref' => session('public_form_ticket_ref'), 'org' => session('public_form_org_name', '')]) }}
                                @else
                                    {{ __('public_form.followup_no_ticket', ['org' => session('public_form_org_name', '')]) }}
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-8 text-center transition-all duration-500 delay-500"
                         :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
                        <a href="{{ request()->url() }}"
                           class="mnx-btn-primary inline-flex items-center gap-2 h-12 px-7 rounded-xl text-sm font-bold text-white"
                           style="background: var(--accent);">
                            <iconify-icon icon="solar:restart-linear" width="16"></iconify-icon>
                            {{ __('public_form.submit_another') }}
                        </a>
                    </div>
                </div>

            {{-- ==================== FORM ==================== --}}
            @else
                <div class="mnx-card rounded-2xl border border-white/60 shadow-xl overflow-hidden"
                     x-data="publicForm({{ $hasSections ? count($steps) : 1 }})">

                    {{-- Hero header --}}
                    <div class="{{ $embedMode ? 'px-5 pt-6 pb-5' : 'px-6 pt-8 pb-7 sm:px-10 sm:pt-10 sm:pb-8' }}">
                        @if(!$embedMode)
                            <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest mb-5" style="color: var(--accent);">
                                <span class="h-1.5 w-1.5 rounded-full" style="background: var(--accent);"></span>
                                {{ $organization->name ?? 'Manexo' }}
                            </div>
                        @endif

                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 leading-tight">{{ $pageTitle }}</h1>

                        @if($pageDesc)
                            <p class="mt-3 text-[14px] text-slate-500 leading-relaxed max-w-lg">{{ $pageDesc }}</p>
                        @endif

                        {{-- Progress bar (stepper) --}}
                        @if($hasSections)
                            <div class="mt-7 space-y-2.5">
                                <div class="flex items-center justify-between text-[11px] font-bold text-slate-400">
                                    <span x-text="'{{ __('Étape') }} ' + step + ' / {{ count($steps) }}'"></span>
                                    <span x-text="Math.round(step / totalSteps * 100) + '%'"></span>
                                </div>
                                <div class="h-1.5 w-full rounded-full bg-slate-100 overflow-hidden">
                                    <div class="mnx-progress-fill h-full rounded-full"
                                         style="background: var(--accent);"
                                         :style="'width:' + (step / totalSteps * 100) + '%'"></div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="h-px bg-slate-100"></div>

                    {{-- Form --}}
                    <form method="POST"
                          action="{{ route('forms.public.submit', ['slug' => $form->slug, 'embed' => $embedMode ? 1 : null]) }}"
                          enctype="multipart/form-data"
                          novalidate
                          @submit="handleSubmit($event)"
                          @input="clearFieldError($event)"
                          @change="clearFieldError($event)">
                        @csrf
                        <input type="text" name="website" id="website_honeypot" value="" class="hidden" tabindex="-1" autocomplete="off" />

                        <div class="mnx-form-body {{ $embedMode ? 'px-5 py-5' : 'px-6 py-7 sm:px-10 sm:py-9' }}">
                            @if($hasSections)
                                {{-- STEPPER --}}
                                @foreach($steps as $index => $s)
                                    @php $stepNum = $index + 1; @endphp
                                    <div id="step-{{ $stepNum }}"
                                         x-show="step === {{ $stepNum }}"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 translate-x-4"
                                         x-transition:enter-end="opacity-100 translate-x-0"
                                         style="display:none;">
                                        <h3 class="text-[13px] font-bold uppercase tracking-wider text-slate-400 mb-6">{{ $s['title'] }}</h3>

                                        @if($s['type'] === 'general')
                                            <div class="space-y-5">
                                                @include('forms.partials.general-fields', ['form' => $form, 'categories' => $categories])
                                            </div>
                                        @elseif($s['type'] === 'custom')
                                            <div class="grid grid-cols-6 gap-x-4 gap-y-5">
                                                @foreach($s['fields'] as $f)
                                                    @include('forms.partials.custom-field', ['f' => $f])
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                {{-- SINGLE PAGE --}}
                                <div class="space-y-5">
                                    @include('forms.partials.general-fields', ['form' => $form, 'categories' => $categories])
                                </div>

                                @if($allFields->count())
                                    <div class="mt-9 pt-7 border-t border-slate-100 grid grid-cols-6 gap-x-4 gap-y-5">
                                        @foreach($allFields as $f)
                                            @if($f->type === 'section')
                                                <div class="col-span-6 {{ !$loop->first ? 'mt-3 pt-6 border-t border-slate-100' : '' }}">
                                                    <h3 class="text-[13px] font-bold uppercase tracking-wider text-slate-400">
                                                        {{ $f->label }}
                                                    </h3>
                                                    @if(!empty($f->configuration['help_text']))
                                                        <p class="text-[13px] text-slate-400 mt-1 leading-relaxed">{{ $f->configuration['help_text'] }}</p>
                                                    @endif
                                                </div>
                                            @else
                                                @include('forms.partials.custom-field', ['f' => $f])
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="border-t border-slate-100 {{ $embedMode ? 'px-5 py-4' : 'px-6 py-5 sm:px-10 sm:py-6' }} flex items-center gap-3"
                             :class="step > 1 ? 'justify-between' : 'justify-end'">

                            @if($hasSections)
                                <button type="button"
                                        x-show="step > 1" x-cloak
                                        @click="prev()"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition">
                                    <iconify-icon icon="solar:arrow-left-linear" width="16"></iconify-icon>
                                    {{ __('Retour') }}
                                </button>
                                <div>
                                    <button type="button"
                                            x-show="step < totalSteps"
                                            @click="next()"
                                            class="mnx-btn-primary inline-flex items-center gap-2 h-12 px-7 rounded-xl text-sm font-bold text-white"
                                            style="background: var(--accent);">
                                        {{ __('Continuer') }}
                                        <iconify-icon icon="solar:arrow-right-linear" width="16"></iconify-icon>
                                    </button>
                                    <button type="submit"
                                            x-show="step === totalSteps" x-cloak
                                            :disabled="submitting"
                                            class="mnx-btn-primary inline-flex items-center gap-2 h-12 px-8 rounded-xl text-sm font-bold text-white disabled:opacity-60"
                                            style="background: var(--accent);">
                                        <template x-if="!submitting">
                                            <span class="inline-flex items-center gap-2">
                                                {{ __('Envoyer') }}
                                                <iconify-icon icon="solar:plain-2-bold" width="16"></iconify-icon>
                                            </span>
                                        </template>
                                        <template x-if="submitting">
                                            <span class="inline-flex items-center gap-2">
                                                <iconify-icon icon="svg-spinners:ring-resize" width="16"></iconify-icon>
                                                {{ __('Envoi…') }}
                                            </span>
                                        </template>
                                    </button>
                                </div>
                            @else
                                <button type="submit"
                                        :disabled="submitting"
                                        class="mnx-btn-primary inline-flex items-center gap-2 h-12 px-8 rounded-xl text-sm font-bold text-white disabled:opacity-60"
                                        style="background: var(--accent);">
                                    <template x-if="!submitting">
                                        <span class="inline-flex items-center gap-2">
                                            {{ __('Envoyer') }}
                                            <iconify-icon icon="solar:plain-2-bold" width="16"></iconify-icon>
                                        </span>
                                    </template>
                                    <template x-if="submitting">
                                        <span class="inline-flex items-center gap-2">
                                            <iconify-icon icon="svg-spinners:ring-resize" width="16"></iconify-icon>
                                            {{ __('Envoi…') }}
                                        </span>
                                    </template>
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-manexo-public-layout>
