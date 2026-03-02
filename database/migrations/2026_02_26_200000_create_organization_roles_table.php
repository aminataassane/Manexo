<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name', 80);
            $table->string('slug', 40);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['organization_id', 'slug']);
        });

        // Seed default roles for every existing organization
        $orgIds = DB::table('organizations')->pluck('id');

        $defaults = [
            ['name' => 'Propriétaire', 'slug' => 'owner'],
            ['name' => 'Administrateur', 'slug' => 'admin'],
            ['name' => 'Agent', 'slug' => 'agent'],
            ['name' => 'Membre', 'slug' => 'member'],
        ];

        $now = now();
        $rows = [];

        foreach ($orgIds as $orgId) {
            foreach ($defaults as $d) {
                $rows[] = [
                    'organization_id' => $orgId,
                    'name' => $d['name'],
                    'slug' => $d['slug'],
                    'is_default' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($rows)) {
            DB::table('organization_roles')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_roles');
    }
};
