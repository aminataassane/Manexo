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
        if (Schema::hasColumn('organization_invitations', 'token')) {
            return;
        }

        Schema::table('organization_invitations', function (Blueprint $table) {
            $table->string('token', 64)->nullable()->after('role');
        });

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

    public function down(): void
    {
        Schema::table('organization_invitations', function (Blueprint $table) {
            $table->dropUnique(['token']);
            $table->dropColumn('token');
        });
    }
};
