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
            $category->photo = asset('categories/' . $category->photo);
        }
    
        return response()->json(['status' => 'success', 'message' => 'Data didapatkan', 'data' => $allCategories], 200);
    }
    
}
