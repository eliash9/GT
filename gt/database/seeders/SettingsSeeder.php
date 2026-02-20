<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Infrastructure\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'GT App', 'type' => 'string', 'group' => 'general', 'label' => 'Application Name'],
            ['key' => 'app_logo', 'value' => null, 'type' => 'image', 'group' => 'general', 'label' => 'Application Logo'],
            ['key' => 'app_favicon', 'value' => null, 'type' => 'image', 'group' => 'general', 'label' => 'Application Favicon'],
            ['key' => 'app_description', 'value' => 'Starter Pack App', 'type' => 'string', 'group' => 'general', 'label' => 'Application Description'],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'general', 'label' => 'Maintenance Mode'],
            ['key' => 'items_per_page', 'value' => '15', 'type' => 'integer', 'group' => 'general', 'label' => 'Items Per Page'],
            ['key' => 'admin_email', 'value' => 'admin@example.com', 'type' => 'string', 'group' => 'email', 'label' => 'Admin Email'],
            ['key' => 'mail_from_name', 'value' => 'GT App', 'type' => 'string', 'group' => 'email', 'label' => 'Mail From Name'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
