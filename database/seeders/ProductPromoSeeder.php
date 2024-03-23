<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductPromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sp_1_promo')->insert(
            [
                [
                    'id_shop' => 1,
                    'id_product' => 1,
                    'default_price' => 10000,
                    'promo_price' => 9000,
                    'start_date' => '2019-10-20',
                    'end_date' => '2019-10-29',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],

            ]
        );
    }
}
