<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;

class PaymentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Payment::create(['payment_method' => 'コンビニ払い']);
        Payment::create(['payment_method' => 'カード支払い']);
    }
}
