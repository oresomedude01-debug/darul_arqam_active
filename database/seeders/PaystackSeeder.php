<?php

namespace Database\Seeders;

use App\Models\SchoolSetting;
use Illuminate\Database\Seeder;

class PaystackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = SchoolSetting::first();
        if ($setting) {
            $setting->update([
                'paystack_public_key' => 'pk_test_c8034fce42820644488e18b5650c3abd65a01aa1',
                'paystack_secret_key' => 'sk_test_431b577a6d634ec71ba8a6bf9fca4f791ef09ecf',
                'paystack_merchant_email' => 'merchant@school.com'
            ]);
            $this->command->info('Paystack credentials seeded successfully');
        }
    }
}
