<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Seed sla.* settings into existing organizations
        $orgs = DB::table('organizations')->select('id', 'settings')->get();

        foreach ($orgs as $org) {
            $settings = json_decode($org->settings ?? '{}', true) ?: [];

            if (! isset($settings['sla'])) {
                $settings['sla'] = [
                    'enabled' => false,
                    'at_risk_threshold_percent' => 80,
                ];

                DB::table('organizations')
                    ->where('id', $org->id)
                    ->update(['settings' => json_encode($settings)]);
            }
        }
    }

    public function down(): void
    {
        $orgs = DB::table('organizations')->select('id', 'settings')->get();

        foreach ($orgs as $org) {
            $settings = json_decode($org->settings ?? '{}', true) ?: [];
            unset($settings['sla']);

            DB::table('organizations')
                ->where('id', $org->id)
                ->update(['settings' => json_encode($settings)]);
        }
    }
};
