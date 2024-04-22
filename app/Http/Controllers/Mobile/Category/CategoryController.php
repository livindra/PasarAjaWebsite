<?php

namespace App\Http\Controllers\Mobile\Category;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\Product\ProductController;
use App\Models\ProductCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function generateTableProd($idShop)
    {
        return 'sp_' . $idShop . '_prod';
    }

    public function allCategories(ProductCategories $categories)
    {
        // ambil semua data kategori
        $allCategories = $categories->select("*")->get();

        if ($allCategories->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Data kategori tidak ditemukan'], 404);
        }

        // add path gambar
        foreach ($allCategories as $category) {
            $category->photo = asset('categories/' . $category->photo);
        }

        return response()->json(['status' => 'success', 'message' => 'Data didapatkan', 'data' => $allCategories], 200);
    }

    public function allCategoryByProduct(Request $request, ProductController $productController)
    {
        $idShop = $request->input('id_shop');

        $tableProd = $this->generateTableProd($idShop);

        if ($productController->isExistShop($idShop)['status'] === 'success') {
            $data = DB::table('0product_categories as ctg')
                ->leftJoin(DB::raw("$tableProd as prod"), 'prod.id_cp_prod', 'ctg.id_cp_prod')
                ->select(
                    [
                        'ctg.id_cp_prod',
                        'ctg.category_name',
                        'ctg.photo',
                        DB::raw("COUNT(prod.id_cp_prod) as prod_count")
                    ]
                )
                ->groupBy('ctg.id_cp_prod', 'ctg.category_name', 'ctg.photo')
                ->get();

            // Menghapus index pertama dari data
            $data->shift();

            foreach ($data as $d) {
                $d->photo = asset('categories/' . $d->photo);
            }
            return response()->json(['status' => 'success', 'message' => 'Data didapatkan', 'data' => $data], 200);
        } else {
            return response()->json(['status' => 'success', 'message' => 'Toko tidak ditemukan'], 404);
        }
    }
}
