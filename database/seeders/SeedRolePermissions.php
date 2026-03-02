<?php

namespace Database\Seeders;

use App\Helpers\PermissionSeeder;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class SeedRolePermissions extends Seeder
{
    public function run(): void
    {
        Organization::query()
            ->select('id')
            ->chunkById(100, function ($orgs) {
                foreach ($orgs as $org) {
                    PermissionSeeder::seedForOrganization((int) $org->id);
                }
            });
    }
}
