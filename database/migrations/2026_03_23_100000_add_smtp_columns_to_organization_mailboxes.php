<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_mailboxes', function (Blueprint $table) {
            $table->string('smtp_host', 255)->nullable()->after('imap_folder');
            $table->unsignedSmallInteger('smtp_port')->default(587)->after('smtp_host');
            $table->string('smtp_username', 255)->nullable()->after('smtp_port');
            $table->text('smtp_password')->nullable()->after('smtp_username'); // encrypted via model cast
            $table->string('smtp_encryption', 10)->default('tls')->after('smtp_password'); // tls, ssl, none
        });
    }

    public function down(): void
    {
        Schema::table('organization_mailboxes', function (Blueprint $table) {
            $table->dropColumn(['smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption']);
        });
    }
};
