<?php

namespace Database\Seeders;

use App\Models\Shops;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shops::create([
            'id_user' => '1',
            'phone_number' => '6289891212',
            'shop_name' => 'Toko Admin',
            'description' => 'abcdefghijklmnopqrstuvwxyz',
            'benchmark' => 'Dekat Kantor Pasar',
            'photo' => 'photo.jpg',
        ]);
    }
}
