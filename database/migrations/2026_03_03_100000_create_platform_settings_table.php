<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('platform_settings')) {
            Schema::create('platform_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key', 100)->unique();
                $table->text('value')->nullable();
                $table->string('type', 20)->default('string');
                $table->timestamps();
            });
        }

        // Seed default values if table is empty
        if (DB::table('platform_settings')->count() === 0) {
            DB::table('platform_settings')->insert([
                ['key' => 'min_password_length', 'value' => '8', 'type' => 'integer', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'session_lifetime_minutes', 'value' => '120', 'type' => 'integer', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'max_login_attempts', 'value' => '5', 'type' => 'integer', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'require_email_verification', 'value' => '1', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
