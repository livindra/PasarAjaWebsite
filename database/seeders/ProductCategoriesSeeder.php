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
            ["category_code" => 100, "category_name" => "Barang Bekas"],
            ["category_code" => 101, "category_name" => "Barang Antik"],
            ["category_code" => 102, "category_name" => "Peralatan Memancing"],
            ["category_code" => 103, "category_name" => "Perlengkapan Bayi dan Anak"],
            ["category_code" => 104, "category_name" => "Kerajinan Lokal"],
            ["category_code" => 105, "category_name" => "Bunga"],
            ["category_code" => 106, "category_name" => "Tanaman"],
            ["category_code" => 107, "category_name" => "Hewan Peliharaan"],
            ["category_code" => 108, "category_name" => "Elektronik"],
            ["category_code" => 109, "category_name" => "Bahan Bangunan"],
            ["category_code" => 110, "category_name" => "Peralatan Medis"],
            ["category_code" => 111, "category_name" => "Pakaian"],
            ["category_code" => 112, "category_name" => "Sepatu"],
            ["category_code" => 113, "category_name" => "Perhiasan"],
            ["category_code" => 114, "category_name" => "Alat Tulis"],
            ["category_code" => 115, "category_name" => "Tas"],
            ["category_code" => 116, "category_name" => "Perlengkapan Sekolah"],
            ["category_code" => 117, "category_name" => "Daging"],
            ["category_code" => 118, "category_name" => "Sayuran"],
            ["category_code" => 119, "category_name" => "Ikan"],
            ["category_code" => 120, "category_name" => "Bumbu Dapur"],
            ["category_code" => 121, "category_name" => "Buku"],
            ["category_code" => 122, "category_name" => "Kue dan Roti"],
            ["category_code" => 123, "category_name" => "Makanan"],
            ["category_code" => 124, "category_name" => "Minuman"],
            ["category_code" => 125, "category_name" => "Perlengkapan Rumah Tangga"],
            ["category_code" => 126, "category_name" => "Kerajinan Tangan"],
            ["category_code" => 127, "category_name" => "Perabotan"],
            ["category_code" => 128, "category_name" => "Souvenir"],
            ["category_code" => 129, "category_name" => "Perawatan Kecantikan"],
            ["category_code" => 130, "category_name" => "Obat dan Farmasi"],
            ["category_code" => 131, "category_name" => "Peralatan Olahraga"]
        ]);
    }
}
