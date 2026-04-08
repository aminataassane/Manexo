<?php

namespace App\Livewire\UserForms;

use App\Enums\FormAssignmentStatus;
use App\Events\UserNotificationReceived;
use App\Helpers\CacheHelper;
use App\Models\Form;
use App\Models\FormAssignment;
use App\Models\FormResponse;
use App\Models\OrganizationMembership;
use App\Models\User;
use App\Notifications\FormResponseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.manexo-app')]
#[Title('Remplir le formulaire')]
class Fill extends Component
{
    use WithFileUploads;

    public FormAssignment $assignment;

    /** Champs initialisés dans mount (pas de wire:init — un seul rendu utile). */
    public bool $formReady = false;

    public array $answers = [];

    public array $fileUploads = [];

    public function mount(FormAssignment $assignment): void
    {
        $userId = Auth::id();
        $orgId = (int) session('current_organization_id');
        abort_if(! $userId || ! $orgId, 403);

        $functionIds = Cache::remember("org_member_functions:{$orgId}:{$userId}", CacheHelper::TTL, function () use ($orgId, $userId) {
            return OrganizationMembership::query()
                ->where('organization_id', $orgId)
                ->where('user_id', (int) $userId)
                ->whereNotNull('organization_function_id')
                ->pluck('organization_function_id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
        });

        // Verify user has access to this assignment
        $hasAccess = FormAssignment::query()
            ->whereKey($assignment->id)
            ->where('organization_id', $orgId)
            ->where(function ($q) use ($userId, $functionIds) {
                $q->where('user_id', (int) $userId);
                if ($functionIds !== []) {
                    $q->orWhere(function ($sub) use ($functionIds) {
                        $sub->whereNull('user_id')
                            ->whereIn('organization_function_id', $functionIds);
                    });
                }
            })
            ->exists();

        abort_if(! $hasAccess, 403);
        abort_if($assignment->status === FormAssignmentStatus::Submitted, 403, 'Ce formulaire a déjà été soumis.');
        abort_if($assignment->isExpired(), 403, __('pages.forms.form_expired'));

        $this->assignment = $assignment;
        $this->assignment->loadMissing('form.fields');

        foreach ($this->assignment->form->fields as $field) {
            if ($field->type === 'section') {
                continue;
            }
            $this->answers[$field->key] = $field->type === 'checkbox'
                ? (is_array($field->options) && count($field->options) > 0 ? [] : false)
                : '';
        }

        $this->formReady = true;
    }

    public function loadFormFields(): void
    {
        // No-op: fields loaded in mount().
    }

    public function submit(): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);

        if (! $this->formReady) {
            $this->loadFormFields();
        }

        $this->assignment->refresh();
        abort_if($this->assignment->isExpired(), 403, __('pages.forms.form_expired'));

        $this->assignment->loadMissing('form.fields');
        $form = $this->assignment->form;
        $fields = $form->fields->where('type', '!=', 'section');

        // Build validation rules
        $rules = [];
        foreach ($fields as $f) {
            $path = "answers.{$f->key}";
            $fieldRules = [$f->required ? 'required' : 'nullable'];
            $type = (string) $f->type;

            if ($type === 'email') {
                $fieldRules[] = 'email';
                $fieldRules[] = 'max:255';
            } elseif ($type === 'number') {
                $fieldRules[] = 'numeric';
            } elseif (in_array($type, ['date', 'datetime'], true)) {
                $fieldRules[] = 'date';
            } elseif ($type === 'checkbox') {
                if (is_array($f->options) && count($f->options) > 0) {
                    $fieldRules[] = 'array';
                    $fieldRules[] = 'max:'.count($f->options);
                    if ($f->required) {
                        $fieldRules[] = 'min:1';
                    }
                    $rules["answers.{$f->key}.*"] = ['string', Rule::in($f->options)];
                } else {
                    $fieldRules[] = 'boolean';
                }
            } elseif ($type === 'textarea') {
                $fieldRules[] = 'string';
                $fieldRules[] = 'max:5000';
            } elseif (in_array($type, ['select', 'radio'], true)) {
                $fieldRules[] = 'string';
                $fieldRules[] = 'max:120';
                if (is_array($f->options) && count($f->options)) {
                    $fieldRules[] = Rule::in($f->options);
                }
            } elseif ($type === 'file') {
                $fieldRules = ['nullable'];
                $rules["fileUploads.{$f->key}"] = ['nullable', 'file', 'max:10240'];
            } else {
                $fieldRules[] = 'string';
                $fieldRules[] = 'max:255';
            }

            $rules[$path] = $fieldRules;
        }

        $this->validate($rules);

        // Build clean responses (excluding file fields)
        $responses = [];
        $fileFields = [];
        foreach ($fields as $f) {
            $key = (string) $f->key;

            if ($f->type === 'file') {
                $uploaded = $this->fileUploads[$key] ?? null;
                if ($uploaded instanceof \Illuminate\Http\UploadedFile) {
                    $fileFields[$key] = $uploaded;
                }

                continue;
            }

            $val = $this->answers[$key] ?? null;
            if ($f->type === 'checkbox') {
                if (is_array($f->options) && count($f->options) > 0) {
                    $val = is_array($val) ? array_values(array_filter($val)) : [];
                } else {
                    $val = (bool) $val;
                }
            }
            if (is_string($val)) {
                $val = trim($val);
            }
            if ($val === null || $val === '' || (is_array($val) && count($val) === 0)) {
                continue;
            }
            $responses[$key] = $val;
        }

        $orgId = (int) session('current_organization_id');

        $response = FormResponse::create([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'assignment_id' => $this->assignment->id,
            'form_version' => $this->assignment->form_version,
            'responses' => $responses,
            'field_snapshot' => $form->snapshotFields(),
            'ip_address' => request()->ip(),
            'respondent_name' => $user->name,
            'respondent_email' => $user->email,
            'submitted_from' => 'internal_assignment',
        ]);

        // Store uploaded files
        if (! empty($fileFields)) {
            $updatedResponses = $response->responses ?? [];
            foreach ($fileFields as $key => $uploadedFile) {
                $path = FormResponse::storeUploadedFile($uploadedFile, $orgId, $response->id, $key);
                $updatedResponses[$key] = [
                    'type' => 'file',
                    'path' => $path,
                    'original_name' => $uploadedFile->getClientOriginalName(),
                    'size' => $uploadedFile->getSize(),
                ];
            }
            $response->update(['responses' => $updatedResponses]);
        }

        $this->assignment->update([
            'status' => FormAssignmentStatus::Submitted,
            'submitted_at' => now(),
        ]);

        // Notify form creator (assigned_by)
        $creator = User::find($this->assignment->assigned_by);
        if ($creator && (int) $creator->id !== (int) $user->id) {
            $creator->notify(new FormResponseNotification(
                formId: $form->id,
                formName: $form->name,
                responseId: $response->id,
                responderId: $user->id,
                responderName: $user->name,
                source: 'assignment',
                formPublicId: $form->public_id,
            ));
            event(new UserNotificationReceived(userId: $creator->id, notificationType: 'form_response'));
        }

        CacheHelper::invalidateUserFormsCache($orgId, (int) $user->id);

        session()->flash('form_success', __('pages.forms.response_saved'));
        $this->redirectRoute('forms.index');
    }

    public function render()
    {
        return view('livewire.user-forms.fill');
    }
}
