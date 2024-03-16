<?php

namespace Database\Seeders;

use App\Models\ProductCategories;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductCategories::insert([
            ["category_name" => "Kategori Produk 1"],
            ["category_name" => "Kategori Produk 2"],
        ]);
    }
}
