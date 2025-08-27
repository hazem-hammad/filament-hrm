<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'group' => 'Identity',
                'name' => 'Company Name',
                'key' => 'company_name',
                'value' => 'HRM',
                'type' => 'text'
            ],
            [
                'group' => 'careers',
                'name' => 'Primary Color',
                'key' => 'primary_color',
                'value' => '#23B53D',
                'type' => 'color',
            ],
            [
                'group' => 'Logos',
                'name' => 'Logo Light',
                'key' => 'logo_light',
                'value' => null,
                'is_configurable_by_admin' => true,
                'media_collection_name' => 'logo_light',
                'type' => 'file',
            ],
            [
                'group' => 'Logos',
                'name' => 'Logo Dark',
                'key' => 'logo_dark',
                'value' => null,
                'is_configurable_by_admin' => true,
                'media_collection_name' => 'logo_dark',
                'type' => 'file',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
