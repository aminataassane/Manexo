<?php

namespace App\Livewire\UserForms;

use App\Enums\FormStatus;
use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.manexo-app')]
#[Title('Remplir le formulaire')]
class FillTeam extends Component
{
    use WithFileUploads;

    public Form $form;
    public array $answers = [];
    public array $fileUploads = [];

    public function mount(Form $form): void
    {
        $userId = Auth::id();
        $orgId = (int) session('current_organization_id');

        abort_if(! $userId || ! $orgId, 403);
        abort_if($form->organization_id !== $orgId, 403);
        abort_if($form->status !== FormStatus::Published, 404);
        abort_if($form->target_user_id !== null, 403, 'This form is not a team form.');

        $this->form = $form;
        $this->form->loadMissing('fields');

        foreach ($this->form->fields as $field) {
            if ($field->type === 'section') {
                continue;
            }
            $this->answers[$field->key] = $field->type === 'checkbox'
                ? (is_array($field->options) && count($field->options) > 0 ? [] : false)
                : '';
        }
    }

    public function submit(): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);

        $this->form->loadMissing('fields');
        $fields = $this->form->fields->where('type', '!=', 'section');

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
                    $fieldRules[] = 'max:' . count($f->options);
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
            'form_id' => $this->form->id,
            'user_id' => $user->id,
            'assignment_id' => null,
            'form_version' => $this->form->current_version,
            'responses' => $responses,
            'field_snapshot' => $this->form->snapshotFields(),
            'ip_address' => request()->ip(),
            'respondent_name' => $user->name,
            'respondent_email' => $user->email,
            'submitted_from' => 'internal_team',
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

        session()->flash('form_success', __('pages.forms.response_saved'));
        $this->redirectRoute('forms.index');
    }

    public function render()
    {
        return view('livewire.user-forms.fill', [
            'form' => $this->form,
            'assignment' => null,
        ]);
    }
}
