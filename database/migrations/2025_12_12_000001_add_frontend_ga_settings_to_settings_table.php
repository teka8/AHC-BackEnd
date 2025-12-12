<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert frontend Google Analytics settings
        $settings = [
            [
                'option_name' => 'frontend_ga_measurement_id',
                'option_value' => '',
                'autoload' => true,
            ],
            [
                'option_name' => 'frontend_ga_property_id',
                'option_value' => '',
                'autoload' => true,
            ],
            [
                'option_name' => 'frontend_ga_enabled',
                'option_value' => '0',
                'autoload' => true,
            ],
            [
                'option_name' => 'frontend_ga_anonymize_ip',
                'option_value' => '1',
                'autoload' => true,
            ],
            [
                'option_name' => 'frontend_ga_cookie_consent_required',
                'option_value' => '1',
                'autoload' => true,
            ],
            [
                'option_name' => 'frontend_ga_service_account_path',
                'option_value' => '',
                'autoload' => true,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['option_name' => $setting['option_name']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $settingKeys = [
            'frontend_ga_measurement_id',
            'frontend_ga_property_id',
            'frontend_ga_enabled',
            'frontend_ga_anonymize_ip',
            'frontend_ga_cookie_consent_required',
            'frontend_ga_service_account_path',
        ];

        DB::table('settings')->whereIn('option_name', $settingKeys)->delete();
    }
};
