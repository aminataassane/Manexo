<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_form_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('ticket_form_templates')->cascadeOnDelete();
            $table->unsignedInteger('number')->default(1);
            $table->string('title', 160)->default('Informations');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(10);
            $table->timestamps();

            $table->unique(['template_id', 'number'], 'tfs_template_number_unique');
            $table->index(['template_id', 'sort_order'], 'tfs_template_sort_idx');
        });

        Schema::table('ticket_form_fields', function (Blueprint $table) {
            $table->foreignId('step_id')->nullable()->after('template_id')->constrained('ticket_form_steps')->nullOnDelete();
            $table->index(['step_id', 'sort_order'], 'tff_step_sort_idx');
        });

        // Backfill: every existing template gets a default step, and fields are attached to it.
        $now = now();
        $templateIds = DB::table('ticket_form_templates')->pluck('id');
        foreach ($templateIds as $templateId) {
            $stepId = DB::table('ticket_form_steps')->insertGetId([
                'template_id' => (int) $templateId,
                'number' => 1,
                'title' => 'Informations',
                'description' => null,
                'sort_order' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('ticket_form_fields')
                ->where('template_id', (int) $templateId)
                ->whereNull('step_id')
                ->update(['step_id' => (int) $stepId, 'updated_at' => $now]);
        }
    }

    public function down(): void
    {
        Schema::table('ticket_form_fields', function (Blueprint $table) {
            $table->dropIndex('tff_step_sort_idx');
            $table->dropConstrainedForeignId('step_id');
        });

        Schema::dropIfExists('ticket_form_steps');
    }
};

