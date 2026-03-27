<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('source', 20)->default('platform')->after('status');
        });

        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->string('email_message_id', 512)->nullable()->after('meta');
            $table->index('email_message_id');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('source');
        });

        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->dropIndex(['email_message_id']);
            $table->dropColumn('email_message_id');
        });
    }
};
