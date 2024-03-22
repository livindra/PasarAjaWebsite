<?php

namespace App\Http\Controllers\Mobile\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Shops;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductReviewController extends Controller
{

    public function generateTableName($idShop)
    {
        return 'sp_' . $idShop . '_rvw';
    }

    public function isExistShop($idShop)
    {
        $isExist = Shops::where('id_shop', '=', $idShop)->limit(1)->exists();

        if ($isExist) {
            return ['status' => 'success', 'message' => 'Toko terdaftar'];
        } else {
            return ['status' => 'error', 'message' => 'Toko tidak terdaftar'];
        }
    }

    public function getReviews(Request $request)
    {
        $idShop = $request->input('id_shop');
        $idProd = $request->input('id_product');

        // generate table name
        $tableName = $this->generateTableName($idShop);

        // cek apakah toko ada atau tidak didalam database
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistShop['message']], 400);
        }

        // menghitung rating dari produk
        $productAverageRating = DB::table($tableName)
            ->select(DB::raw('ROUND(AVG(star), 1) as average_rating'))
            ->where('id_product', $idProd)
            ->first();

        // mendapatkan total review
        $totalReviews = DB::table($tableName)
            ->where('id_product', $idProd)
            ->count();

        // mendapatkan data rating produk
        $averageRating = $productAverageRating->average_rating;

        // jika tidak ada review, set rating rata-rata menjadi 0
        if ($averageRating === null) {
            $averageRating = 0;
        }

        $reviews = DB::table($tableName)
            ->select()
            ->where('id_product', $idProd)
            ->orderByDesc('id_review')
            ->get();

        foreach ($reviews as $rvw) {
            // mendapatkan data nama dan email
            $userData = User::select(['full_name', 'email'])
                ->where('id_user', $rvw->id_user)
                ->limit(1)->first();

            // menyimpan data nama dan email
            $rvw->full_name = $userData->full_name;
            $rvw->email = $userData->email;
        }

        $ratingData = [
            'rating' => $averageRating,
            'total_review' => $totalReviews,
            'reviewers' => $reviews,
        ];

        return response()->json(['status' => 'success', 'message' => 'data didapatkan', 'data' => $ratingData], 200);
    }

    public function addReview(Request $request)
    {
        //
    }
}
