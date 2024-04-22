<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sp_1_trx_dtl')->insert([
            [
                'id_detail' => 1,
                'id_trx' => 1,
                'id_product' => 1,
                'quantity' => 1,
                'promo_price' => 2000,
                'notes' => 'yang masih baru'
            ],
            [
                'id_detail' => 2,
                'id_trx' => 1,
                'id_product' => 2,
                'quantity' => 4,
                'promo_price' => 0,
                'notes' => ''
            ],
            [
                'id_detail' => 3,
                'id_trx' => 1,
                'id_product' => 3,
                'quantity' => 2,
                'promo_price' => 4000,
                'notes' => 'ga ada'
            ],
        ]);
    }
}
