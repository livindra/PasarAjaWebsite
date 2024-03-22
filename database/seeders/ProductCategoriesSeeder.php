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
            ["category_name" => "Barang Bekas"],
            ["category_name" => "Barang Antik"],
            ["category_name" => "Peralatan Memancing"],
            ["category_name" => "Perlengkapan Bayi dan Anak"],
            ["category_name" => "Kerajinan Lokal"],
            ["category_name" => "Bunga"],
            ["category_name" => "Tanaman"],
            ["category_name" => "Hewan Peliharaan"],
            ["category_name" => "Elektronik"],
            ["category_name" => "Bahan Bangunan"],
            ["category_name" => "Peralatan Medis"],
            ["category_name" => "Pakaian"],
            ["category_name" => "Sepatu"],
            ["category_name" => "Perhiasan"],
            ["category_name" => "Alat Tulis"],
            ["category_name" => "Tas"],
            ["category_name" => "Perlengkapan Sekolah"],
            ["category_name" => "Daging"],
            ["category_name" => "Sayuran"],
            ["category_name" => "Ikan"],
            ["category_name" => "Bumbu Dapur"],
            ["category_name" => "Buku"],
            ["category_name" => "Kue dan Roti"],
            ["category_name" => "Makanan"],
            ["category_name" => "Minuman"],
            ["category_name" => "Perlengkapan Rumah Tangga"],
            ["category_name" => "Kerajinan Tangan"],
            ["category_name" => "Perabotan"],
            ["category_name" => "Souvenir"],
            ["category_name" => "Perawatan Kecantikan"],
            ["category_name" => "Obat dan Farmasi"],
            ["category_name" => "Peralatan Olahraga"]
        ]);
    }
}
