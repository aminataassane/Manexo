<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Tables that receive a public_id column, with their ULID prefix.
     */
    private array $tables = [
        'tickets' => 'TCK',
        'forms' => 'FRM',
        'form_assignments' => 'ASG',
        'form_responses' => 'RSP',
        'organizations' => 'ORG',
    ];

    public function up(): void
    {
        // Phase 1: add nullable column
        foreach ($this->tables as $table => $prefix) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('public_id', 40)->nullable()->after('id');
            });
        }

        // Phase 2: backfill existing rows
        foreach ($this->tables as $table => $prefix) {
            $rows = DB::table($table)->whereNull('public_id')->select('id')->get();
            foreach ($rows as $row) {
                DB::table($table)->where('id', $row->id)->update([
                    'public_id' => $prefix . '-' . Str::ulid()->toBase32(),
                ]);
            }
        }

        // Phase 3: NOT NULL + unique index
        foreach ($this->tables as $table => $prefix) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                $t->string('public_id', 40)->nullable(false)->unique()->change();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table => $prefix) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('public_id');
            });
        }
    }
};
