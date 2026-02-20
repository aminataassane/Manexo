<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop in dependency order: fields -> steps -> templates
        Schema::dropIfExists('ticket_form_fields');
        Schema::dropIfExists('ticket_form_steps');
        Schema::dropIfExists('ticket_form_templates');
    }

    public function down(): void
    {
        // Cannot restore dropped tables; re-run original migrations if rollback is needed.
    }
};
