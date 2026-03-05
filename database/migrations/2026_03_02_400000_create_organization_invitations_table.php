<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('organization_invitations')) {
            Schema::create('organization_invitations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->string('email', 255);
                $table->string('role', 50);
                $table->string('token', 64)->unique();
                $table->foreignId('invited_by')->constrained('users')->cascadeOnDelete();
                $table->string('status', 20)->default('pending');
                $table->timestamp('accepted_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamp('expires_at');
                $table->timestamps();

                $table->index('email');
                $table->index('expires_at');
                $table->unique(['organization_id', 'email', 'status'], 'org_email_pending_unique');
            });

            return;
        }

        // Table exists (e.g. created by an older schema): add missing columns
        Schema::table('organization_invitations', function (Blueprint $table) {
            if (! Schema::hasColumn('organization_invitations', 'token')) {
                $table->string('token', 64)->nullable()->after('role');
            }
            if (! Schema::hasColumn('organization_invitations', 'status')) {
                $table->string('status', 20)->default('pending')->after('invited_by');
            }
            if (! Schema::hasColumn('organization_invitations', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('status');
            }
            if (! Schema::hasColumn('organization_invitations', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('accepted_at');
            }
        });

        // Backfill token for existing rows, then make it non-null and unique
        if (Schema::hasColumn('organization_invitations', 'token')) {
            $rows = DB::table('organization_invitations')->whereNull('token')->get();
            foreach ($rows as $row) {
                DB::table('organization_invitations')->where('id', $row->id)->update([
                    'token' => Str::random(64),
                ]);
            }
            DB::statement('ALTER TABLE organization_invitations ALTER COLUMN token SET NOT NULL');
            Schema::table('organization_invitations', function (Blueprint $table) {
                $table->unique('token');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_invitations');
    }
};
