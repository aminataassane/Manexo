<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_form_fields', function (Blueprint $table) {
            $table->string('placeholder', 255)->nullable()->after('label');
            $table->text('help_text')->nullable()->after('placeholder');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_form_fields', function (Blueprint $table) {
            $table->dropColumn(['placeholder', 'help_text']);
        });
    }
};

