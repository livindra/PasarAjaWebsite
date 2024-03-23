<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sp_1_prod')->insert(
            [
                [
                    'id_shop' => 1,
                    'id_cp_prod' => 24,
                    'product_name' => 'pecel lele',
                    'description' => 'Masakan-Masakan Indonesia Tercinta',
                    'selling_unit' => 1,
                    'unit' => 'Bungkus',
                    'price' => 10000,
                    'total_sold' => 0,
                    'photo' => 'product.png',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_shop' => 1,
                    'id_cp_prod' => 24,
                    'product_name' => 'ayam bakar',
                    'description' => 'Masakan-Masakan Indonesia Tercinta',
                    'selling_unit' => 1,
                    'unit' => 'Bungkus',
                    'price' => 20000,
                    'total_sold' => 0,
                    'photo' => 'product.png',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_shop' => 1,
                    'id_cp_prod' => 24,
                    'product_name' => 'ayam geprek',
                    'description' => 'Masakan-Masakan Indonesia Tercinta',
                    'selling_unit' => 1,
                    'unit' => 'Bungkus',
                    'price' => 22000,
                    'total_sold' => 0,
                    'photo' => 'product.png',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_shop' => 1,
                    'id_cp_prod' => 24,
                    'product_name' => 'indomie goreng',
                    'description' => 'Masakan-Masakan Indonesia Tercinta',
                    'selling_unit' => 1,
                    'unit' => 'Bungkus',
                    'price' => 12000,
                    'total_sold' => 0,
                    'photo' => 'product.png',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_shop' => 1,
                    'id_cp_prod' => 24,
                    'product_name' => 'pop mie',
                    'description' => 'Masakan-Masakan Indonesia Tercinta',
                    'selling_unit' => 1,
                    'unit' => 'Bungkus',
                    'price' => 13000,
                    'total_sold' => 0,
                    'photo' => 'product.png',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
            ]
        );
    }
}
