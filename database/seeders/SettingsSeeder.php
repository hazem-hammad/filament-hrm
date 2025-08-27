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
                'name' => 'Brand Name',
                'key' => 'brand_name',
                'value' => 'Congora',
                'type' => 'text'
            ],
            [
                'group' => 'Force Update',
                'name' => 'iOS Minimum Version',
                'key' => 'ios_min_version',
                'value' => '1.0.0',
                'type' => 'text'
            ],
            [
                'group' => 'Force Update',
                'name' => 'Android Minimum Version',
                'key' => 'android_min_version',
                'value' => '1.0.0',
                'type' => 'text'
            ],
            [
                'group' => 'Maintenance',
                'name' => 'Maintenance Mode',
                'key' => 'maintenance_mode',
                'value' => 0,
                'is_configurable_by_admin' => true,
                'type' => 'boolean',
            ],
            [
                'group' => 'Login Options',
                'name' => 'Login by Apple',
                'key' => 'login_by_apple',
                'value' => 0,
                'type' => 'boolean',
            ],
            [
                'group' => 'Login Options',
                'name' => 'Login by Google',
                'key' => 'login_by_google',
                'value' => 0,
                'type' => 'boolean',
            ],
            [
                'group' => 'Color Identity',
                'name' => 'Primary Color',
                'key' => 'primary_color',
                'value' => '#23B53D',
                'type' => 'color',
            ],
            [
                'group' => 'Color Identity',
                'name' => 'Secondary Color',
                'key' => 'secondary_color',
                'value' => '#000000',
                'type' => 'color',
            ],

            // logos: light and dark
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
            [
                'group' => 'Logos',
                'key' => 'logo_icon_light',
                'name' => 'Logo Icon Light',
                'value' => null,
                'is_configurable_by_admin' => true,
                'media_collection_name' => 'logo_icon_light',
                'type' => 'file',
            ],
            [
                'group' => 'Logos',
                'name' => 'Logo Icon Dark',
                'key' => 'logo_icon_dark',
                'value' => null,
                'is_configurable_by_admin' => true,
                'media_collection_name' => 'logo_icon_dark',
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
