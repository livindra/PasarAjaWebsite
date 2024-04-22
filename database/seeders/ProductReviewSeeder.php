<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sp_1_rvw')->insert(
            [
                [
                    'id_user' => 1,
                    'id_trx' => 1,
                    'id_product' => 1,
                    'star' => '4',
                    'comment' => 'sangat enak',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_user' => 1,
                    'id_trx' => 1,
                    'id_product' => 2,
                    'star' => '2',
                    'comment' => 'kurang enak',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_user' => 1,
                    'id_trx' => 1,
                    'id_product' => 2,
                    'star' => '5',
                    'comment' => 'enak banget',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_user' => 1,
                    'id_trx' => 1,
                    'id_product' => 1,
                    'star' => '5',
                    'comment' => 'b aja',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],

            ]
        );
    }
}
