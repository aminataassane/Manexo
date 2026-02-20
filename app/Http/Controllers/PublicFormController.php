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
    public function show(Request $request, string $slug)
    {
        $form = Form::query()
            ->published()
            ->where('is_public', true)
            ->where('slug', $slug)
            ->with(['organization', 'category', 'fields'])
            ->firstOrFail();

        $org = $form->organization;

        $categories = $form->ticket_category_id
            ? collect()
            : TicketCategory::query()
                ->where('organization_id', $org->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);

        $priorities = TicketPriority::query()
            ->where('organization_id', $org->id)
            ->where('is_active', true)
            ->orderBy('level')
            ->get(['id', 'name', 'level']);

        $defaultPriorityId = (int) ($priorities->first()?->id ?? 0);

        return view('forms.public', [
            'form' => $form,
            'organization' => $org,
            'categories' => $categories,
            'priorities' => $priorities,
            'defaultPriorityId' => $defaultPriorityId,
            'embed' => (bool) $request->boolean('embed'),
        ]);
    }

    public function submit(Request $request, string $slug): RedirectResponse
    {
        // Honeypot (anti-bot): must stay empty
        if (trim((string) $request->input('website')) !== '') {
            abort(422);
        }

        $form = Form::query()
            ->published()
            ->where('is_public', true)
            ->where('slug', $slug)
            ->with(['organization', 'category', 'fields'])
            ->firstOrFail();

        $org = $form->organization;

        $actor = Auth::user();
        $guestEmail = null;

        $baseRules = [
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'ticket_priority_id' => ['required', 'integer', 'exists:ticket_priorities,id'],
            'ticket_category_id' => ['nullable', 'integer', 'exists:ticket_categories,id'],
            'files' => ['array', 'max:5'],
            'files.*' => ['file', 'max:10240'],
            'links' => ['array', 'max:5'],
            'links.*' => ['url', 'max:2000'],
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
                $rules[] = 'boolean';
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

        // Force organization scoping
        $priorityOk = TicketPriority::query()
            ->where('id', (int) $validated['ticket_priority_id'])
            ->where('organization_id', $org->id)
            ->exists();
        if (! $priorityOk) {
            throw ValidationException::withMessages([
                'ticket_priority_id' => __("Sélection invalide pour l'entreprise."),
            ]);
        }

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

        // Build custom_fields payload
        $customFields = [];
        foreach ($formFields as $f) {
            $key = (string) $f->key;
            $val = data_get($validated, "custom.{$key}");

            if ($f->type === 'checkbox') {
                $val = (bool) $val;
            }
            if (is_string($val)) {
                $val = trim($val);
            }

            if ($val === null || $val === '') {
                continue;
            }
            $customFields[$key] = $val;
        }

        $ticket = Ticket::query()->create([
            'organization_id' => $org->id,
            'created_by' => $actor->id,
            'ticket_category_id' => $categoryId,
            'ticket_priority_id' => (int) $validated['ticket_priority_id'],
            'assigned_to' => null,
            'status' => TicketStatus::Open,
            'subject' => trim((string) $validated['subject']),
            'description' => trim((string) $validated['description']),
            'custom_fields' => $customFields ?: null,
        ]);

        // Attachments
        $attachments = ['files' => [], 'links' => []];
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        foreach (($validated['files'] ?? []) as $file) {
            $original = (string) ($file->getClientOriginalName() ?: 'file');
            $ext = (string) ($file->getClientOriginalExtension() ?: '');
            $safeBase = Str::slug(pathinfo($original, PATHINFO_FILENAME)) ?: 'file';
            $filename = $safeBase . '-' . Str::lower(Str::random(10)) . ($ext ? '.' . $ext : '');
            $path = $file->storeAs("ticket-attachments/org-{$org->id}/ticket-{$ticket->id}", $filename, 'public');

            $attachments['files'][] = [
                'disk' => 'public',
                'path' => $path,
                'name' => $original,
                'size' => method_exists($file, 'getSize') ? (int) $file->getSize() : null,
                'mime' => method_exists($file, 'getMimeType') ? (string) $file->getMimeType() : null,
                'url' => $disk->url($path),
            ];
        }

        foreach (($validated['links'] ?? []) as $url) {
            $attachments['links'][] = ['url' => (string) $url];
        }

        if (count($attachments['files']) || count($attachments['links'])) {
            $ticket->update(['attachments' => $attachments]);
        }

        // Create FormResponse
        FormResponse::create([
            'form_id' => $form->id,
            'user_id' => $actor->id,
            'form_version' => $form->current_version,
            'responses' => $customFields,
            'field_snapshot' => $form->snapshotFields(),
            'ticket_id' => $ticket->id,
            'ip_address' => $request->ip(),
        ]);

        $message = $form->public_thank_you ?: __('Merci, votre demande a bien été envoyée.');

        return redirect()
            ->route('forms.public.show', ['slug' => $slug, 'embed' => $request->boolean('embed') ? 1 : null])
            ->with('public_form_success', $message)
            ->with('public_form_ticket_id', $ticket->id)
            ->with('public_form_email', $guestEmail);
    }
}
