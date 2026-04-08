<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('kb_articles', function (Blueprint $table) use ($driver) {
            if ($driver === 'pgsql') {
                $table->jsonb('keywords')->nullable()->after('content');
            } else {
                $table->json('keywords')->nullable()->after('content');
            }
        });

        if ($driver === 'pgsql') {
            DB::statement('CREATE INDEX kb_articles_keywords_gin ON kb_articles USING GIN (keywords)');
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS kb_articles_keywords_gin');
        }

        Schema::table('kb_articles', function (Blueprint $table) {
            $table->dropColumn('keywords');
        });
    }
};
