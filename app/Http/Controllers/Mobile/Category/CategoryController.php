<?php

namespace App\Http\Controllers\Mobile\Category;

use App\Http\Controllers\Controller;
use App\Models\ProductCategories;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function allCategories(ProductCategories $categories){
        // ambil semua data kategori
        $allCategories = $categories->select("*")->get();
    
        if($allCategories->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Data kategori tidak ditemukan'], 404);
        }
    
        // add path gambar
        foreach ($allCategories as $category) {
            if (app()->environment('local')) {
                $category->photo = public_path('categories/') . $category->photo;
            }else{
                $category->photo = public_path(base_path('../public_html/public/categories/')) . $category->photo;
            }
        }
    
        return response()->json(['status' => 'success', 'message' => 'Data didapatkan', 'data' => $allCategories], 200);
    }
    
}
