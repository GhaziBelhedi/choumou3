<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('flat_shipping_price', '7');
        Setting::set('free_shipping_threshold', '100');
        Setting::set('site_phone', '+216 XX XXX XXX');
        Setting::set('site_email', 'contact@choumou3.tn');
    }
}
