<?php

namespace Database\Seeders;

use App\Models\ShopCategories;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShopCategories::insert([
            ["category_name" => "Shop Category 1"],
            ["category_name" => "Shop Category 2"],
        ]);
    }
}
