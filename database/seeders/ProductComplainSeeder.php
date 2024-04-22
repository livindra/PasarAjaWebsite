<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductComplainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sp_1_comp')->insert(
            [
                [
                    'id_user' => 1,
                    'id_shop' => 1,
                    'id_trx' => 1,
                    'id_product' => 1,
                    'reason' => 'makanannya ga enak',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_user' => 1,
                    'id_shop' => 1,
                    'id_trx' => 1,
                    'id_product' => 2,
                    'reason' => 'makanannya udah basi',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],

            ]
        );
    }
}
