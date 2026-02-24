<?php

namespace App\Http\Controllers;

use App\Enums\FormStatus;
use App\Enums\OrganizationRole;
use App\Enums\TicketStatus;
use App\Models\Form;
use App\Models\FormResponse;
use App\Models\OrganizationMembership;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PublicFormController extends Controller
{
    private function findPublicForm(string $slug): Form
    {
        $slug = trim($slug, '/');
        if ($slug === '') {
            abort(404);
        }

        $form = Form::query()
            ->where('is_public', true)
            ->where('status', FormStatus::Published)
            ->whereNotNull('slug')
            ->where('slug', $slug)
            ->with(['organization', 'category', 'fields'])
            ->first();

        if (! $form) {
            abort(404);
        }

        return $form;
    }

    public function show(Request $request, string $slug)
    {
        $form = $this->findPublicForm($slug);
        $org = $form->organization;

        $categories = $form->ticket_category_id
            ? collect()
            : TicketCategory::query()
                ->where('organization_id', $org->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);

        return view('forms.public', [
            'form' => $form,
            'organization' => $org,
            'categories' => $categories,
            'embed' => (bool) $request->boolean('embed'),
        ]);
    }

    public function submit(Request $request, string $slug): RedirectResponse
    {
        // Honeypot (anti-bot): must stay empty
        if (trim((string) $request->input('website')) !== '') {
            abort(422);
        }

        $form = $this->findPublicForm($slug);
        $org = $form->organization;

        $actor = Auth::user();
        $guestEmail = null;
        $guestName = null;

        $baseRules = [
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'ticket_category_id' => ['nullable', 'integer', 'exists:ticket_categories,id'],
        ];

        if (! $actor) {
            $baseRules['guest_name'] = ['required', 'string', 'max:120'];
            $baseRules['guest_email'] = ['required', 'email', 'max:255'];
        }

        // Dynamic rules from form fields
        $dynamicRules = [];
        $formFields = $form->fields->where('type', '!=', 'section');
        foreach ($formFields as $f) {
            $path = "custom.{$f->key}";
            $rules = [$f->required ? 'required' : 'nullable'];
            $type = (string) $f->type;

            if ($type === 'email') {
                $rules[] = 'email';
                $rules[] = 'max:255';
            } elseif ($type === 'number') {
                $rules[] = 'numeric';
            } elseif (in_array($type, ['date', 'datetime'], true)) {
                $rules[] = 'date';
            } elseif ($type === 'checkbox') {
                $cbOptions = is_array($f->configuration['options'] ?? null) ? $f->configuration['options'] : [];
                if (count($cbOptions) > 0) {
                    $rules = [$f->required ? 'required' : 'nullable', 'array'];
                    $dynamicRules["{$path}.*"] = ['string', Rule::in($cbOptions)];
                } else {
                    $rules[] = 'boolean';
                }
            } elseif ($type === 'textarea') {
                $rules[] = 'string';
                $rules[] = 'max:5000';
            } elseif (in_array($type, ['select', 'radio'], true)) {
                $rules[] = 'string';
                $rules[] = 'max:120';
                if (is_array($f->options) && count($f->options)) {
                    $rules[] = Rule::in($f->options);
                }
            } elseif ($type === 'file') {
                $rules = ['nullable', 'file', 'max:10240'];
            } else {
                $rules[] = 'string';
                $rules[] = 'max:255';
            }

            $dynamicRules[$path] = $rules;
        }

        $validated = $request->validate(array_merge($baseRules, $dynamicRules));

        $shouldCreateTicket = (bool) $form->creates_ticket;

        $categoryId = null;
        $categoryName = null;
        $defaultPriority = null;

        if ($shouldCreateTicket) {
            $defaultPriority = TicketPriority::query()
                ->where('organization_id', $org->id)
                ->where('is_active', true)
                ->orderBy('level')
                ->first();

            $categoryId = $form->ticket_category_id ? (int) $form->ticket_category_id : (int) ($validated['ticket_category_id'] ?? 0);
            if ($categoryId <= 0) {
                throw ValidationException::withMessages([
                    'ticket_category_id' => __('Veuillez choisir une catégorie.'),
                ]);
            }
            $categoryOk = TicketCategory::query()
                ->where('id', $categoryId)
                ->where('organization_id', $org->id)
                ->exists();
            if (! $categoryOk) {
                throw ValidationException::withMessages([
                    'ticket_category_id' => __("Sélection invalide pour l'entreprise."),
                ]);
            }
            $categoryName = TicketCategory::find($categoryId)?->name;
        }

        if (! $actor) {
            $guestEmail = Str::lower(trim((string) $validated['guest_email']));
            $guestName = trim((string) $validated['guest_name']);

            $existing = User::query()->where('email', $guestEmail)->first();

            if ($existing) {
                $actor = $existing;
                if (! $actor->name && $guestName) {
                    $actor->forceFill(['name' => $guestName])->save();
                }
            } else {
                $actor = User::query()->create([
                    'name' => $guestName,
                    'email' => $guestEmail,
                    'password' => Str::random(32),
                ]);
            }

            OrganizationMembership::query()->firstOrCreate([
                'organization_id' => $org->id,
                'user_id' => $actor->id,
            ], [
                'role' => OrganizationRole::Member->value,
            ]);
        }

        // Build custom_fields payload (excluding file fields for now)
        $customFields = [];
        $fileFields = [];
        foreach ($formFields as $f) {
            $key = (string) $f->key;
            $val = data_get($validated, "custom.{$key}");

            if ($f->type === 'file') {
                if ($val instanceof \Illuminate\Http\UploadedFile) {
                    $fileFields[$key] = $val;
                }
                continue;
            }

            if ($f->type === 'checkbox') {
                $cbOptions = is_array($f->configuration['options'] ?? null) ? $f->configuration['options'] : [];
                if (count($cbOptions) > 0) {
                    if (! is_array($val) || empty($val)) {
                        continue;
                    }
                } else {
                    $val = (bool) $val;
                }
            }
            if (is_string($val)) {
                $val = trim($val);
            }

            if ($val === null || $val === '') {
                continue;
            }
            $customFields[$key] = $val;
        }

        $ticket = null;

        if ($shouldCreateTicket) {
            $ticket = Ticket::query()->create([
                'organization_id' => $org->id,
                'created_by' => $actor->id,
                'ticket_category_id' => $categoryId,
                'ticket_priority_id' => $defaultPriority?->id,
                'assigned_to' => null,
                'status' => TicketStatus::Open,
                'subject' => trim((string) $validated['subject']),
                'description' => trim((string) $validated['description']),
                'custom_fields' => $customFields ?: null,
            ]);
        }

        // Create FormResponse (need ID for file storage)
        $formResponse = FormResponse::create([
            'form_id' => $form->id,
            'user_id' => $actor->id,
            'form_version' => $form->current_version,
            'responses' => $customFields,
            'field_snapshot' => $form->snapshotFields(),
            'ticket_id' => $ticket?->id,
            'ip_address' => $request->ip(),
            'respondent_name' => $guestName ?? $actor->name,
            'respondent_email' => $guestEmail ?? $actor->email,
            'base_fields' => [
                'subject' => trim((string) $validated['subject']),
                'description' => trim((string) $validated['description']),
                'category_name' => $categoryName,
                'ticket_category_id' => $categoryId,
                'guest_name' => $guestName,
                'guest_email' => $guestEmail,
            ],
            'submitted_from' => 'public',
            'public_form_slug' => $slug,
        ]);

        // Store uploaded files and update responses JSON
        if (! empty($fileFields)) {
            $responses = $formResponse->responses ?? [];
            foreach ($fileFields as $key => $uploadedFile) {
                $path = FormResponse::storeUploadedFile($uploadedFile, $org->id, $formResponse->id, $key);
                $responses[$key] = [
                    'type' => 'file',
                    'path' => $path,
                    'original_name' => $uploadedFile->getClientOriginalName(),
                    'size' => $uploadedFile->getSize(),
                ];
            }
            $formResponse->update(['responses' => $responses]);
        }

        $message = $form->public_thank_you ?: __('Merci, votre demande a bien été envoyée.');

        $redirect = redirect()
            ->route('forms.public.show', ['slug' => $slug, 'embed' => $request->boolean('embed') ? 1 : null])
            ->with('public_form_success', $message)
            ->with('public_form_email', $guestEmail);

        if ($ticket) {
            $redirect->with('public_form_ticket_id', $ticket->id);
        }

        return $redirect;
    }

    /**
     * Serve a file attached to a form response.
     */
    public function serveFile(Request $request, FormResponse $response, string $fieldKey)
    {
        $user = Auth::user();
        abort_if(! $user, 403);

        $orgId = (int) session('current_organization_id');
        $form = $response->form;
        abort_if(! $form || (int) $form->organization_id !== $orgId, 403);

        $responses = $response->responses ?? [];
        $fileData = $responses[$fieldKey] ?? null;

        if (! is_array($fileData) || ($fileData['type'] ?? '') !== 'file' || empty($fileData['path'])) {
            abort(404);
        }

        $storage = Storage::disk('public');
        if (! $storage->exists($fileData['path'])) {
            abort(404);
        }

        return $storage->response($fileData['path'], $fileData['original_name'] ?? basename($fileData['path']), [
            'Content-Type' => $storage->mimeType($fileData['path']),
        ]);
    }
}
