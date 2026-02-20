<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ticket_form_templates')) {
            return;
        }

        $templates = DB::table('ticket_form_templates')->get();

        foreach ($templates as $tpl) {
            $formId = DB::table('forms')->insertGetId([
                'organization_id' => $tpl->organization_id,
                'name' => $tpl->name,
                'slug' => $tpl->public_slug ?? null,
                'description' => null,
                'status' => ($tpl->is_active ?? false) ? 'published' : 'draft',
                'is_public' => $tpl->is_public ?? false,
                'public_title' => $tpl->public_title ?? null,
                'public_description' => $tpl->public_description ?? null,
                'public_thank_you' => $tpl->public_thank_you ?? null,
                'ticket_category_id' => $tpl->ticket_category_id ?? null,
                'target_user_id' => $tpl->target_user_id ?? null,
                'current_version' => 1,
                'created_at' => $tpl->created_at,
                'updated_at' => $tpl->updated_at,
            ]);

            // Migrate steps as section-type fields
            $steps = DB::table('ticket_form_steps')
                ->where('template_id', $tpl->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            $sortOrder = 0;

            foreach ($steps as $step) {
                // Insert a section divider field for each step
                $sortOrder += 10;
                DB::table('form_fields')->insert([
                    'form_id' => $formId,
                    'type' => 'section',
                    'label' => $step->title,
                    'key' => 'section_' . $step->id,
                    'required' => false,
                    'sort_order' => $sortOrder,
                    'configuration' => json_encode([
                        'description' => $step->description,
                    ]),
                    'form_version' => 1,
                    'created_at' => $step->created_at ?? now(),
                    'updated_at' => $step->updated_at ?? now(),
                ]);

                // Migrate fields from this step
                $fields = DB::table('ticket_form_fields')
                    ->where('template_id', $tpl->id)
                    ->where('step_id', $step->id)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();

                foreach ($fields as $field) {
                    $sortOrder += 10;
                    $config = [];
                    if ($field->placeholder ?? null) {
                        $config['placeholder'] = $field->placeholder;
                    }
                    if ($field->help_text ?? null) {
                        $config['help_text'] = $field->help_text;
                    }
                    $options = json_decode($field->options ?? 'null', true);
                    if (is_array($options) && count($options)) {
                        $config['options'] = $options;
                    }

                    DB::table('form_fields')->insert([
                        'form_id' => $formId,
                        'type' => $field->type,
                        'label' => $field->label,
                        'key' => $field->key,
                        'required' => $field->required ?? false,
                        'sort_order' => $sortOrder,
                        'configuration' => json_encode($config ?: (object) []),
                        'form_version' => 1,
                        'created_at' => $field->created_at ?? now(),
                        'updated_at' => $field->updated_at ?? now(),
                    ]);
                }
            }

            // Also migrate orphan fields (no step_id)
            $orphanFields = DB::table('ticket_form_fields')
                ->where('template_id', $tpl->id)
                ->whereNull('step_id')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            foreach ($orphanFields as $field) {
                $sortOrder += 10;
                $config = [];
                if ($field->placeholder ?? null) {
                    $config['placeholder'] = $field->placeholder;
                }
                if ($field->help_text ?? null) {
                    $config['help_text'] = $field->help_text;
                }
                $options = json_decode($field->options ?? 'null', true);
                if (is_array($options) && count($options)) {
                    $config['options'] = $options;
                }

                DB::table('form_fields')->insert([
                    'form_id' => $formId,
                    'type' => $field->type,
                    'label' => $field->label,
                    'key' => $field->key,
                    'required' => $field->required ?? false,
                    'sort_order' => $sortOrder,
                    'configuration' => json_encode($config ?: (object) []),
                    'form_version' => 1,
                    'created_at' => $field->created_at ?? now(),
                    'updated_at' => $field->updated_at ?? now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Data migration - no rollback needed
    }
};
