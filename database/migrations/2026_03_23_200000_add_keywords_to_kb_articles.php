<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kb_articles', function (Blueprint $table) {
            $table->jsonb('keywords')->nullable()->after('content');
        });

        DB::statement('CREATE INDEX kb_articles_keywords_gin ON kb_articles USING GIN (keywords)');
    }

    public function down(): void
    {
        Schema::table('kb_articles', function (Blueprint $table) {
            $table->dropIndex('kb_articles_keywords_gin');
            $table->dropColumn('keywords');
        });
    }
};
