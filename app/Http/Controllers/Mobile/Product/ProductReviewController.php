<?php

namespace App\Http\Controllers\Mobile\Product;

use App\Http\Controllers\Controller;
use App\Models\Shops;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\select;

class ProductReviewController extends Controller
{

    public function generateTableReview($idShop)
    {
        return 'sp_' . $idShop . '_rvw';
    }

    public function generateTableProd($idShop)
    {
        return 'sp_' . $idShop . '_prod';
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

    /// get rating and user review
    public function getReviews(Request $request)
    {
        $idShop = $request->input('id_shop');
        $idProd = $request->input('id_product');
        $limit = $request->input('limit', 0);

        // generate table product and review
        $tableRvw = $this->generateTableReview($idShop);
        $tableProd = $this->generateTableProd($idShop);

        // cek apakah toko ada atau tidak didalam database
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $isExistShop['message']], 400);
        }

        // menghitung rating dari produk
        $productAverageRating = DB::table($tableRvw)
            ->select(DB::raw('ROUND(AVG(star), 1) as average_rating'))
            ->where('id_product', $idProd)
            ->first();

        // mendapatkan total review
        $totalReviews = DB::table($tableRvw)
            ->where('id_product', $idProd)
            ->count();

        // mendapatkan data rating produk
        $averageRating = $productAverageRating->average_rating;

        // jika tidak ada review, set rating rata-rata menjadi 0
        if ($averageRating === null) {
            $averageRating = 0;
        }

        $reviews = DB::table(DB::raw("$tableRvw as rvw"))
            ->join(DB::raw("$tableProd as prod"), 'prod.id_product', 'rvw.id_product')
            ->join('0users as us', 'us.id_user', 'rvw.id_user')
            ->select('rvw.*', 'prod.product_name', 'us.full_name', 'us.email', 'us.photo')
            ->where('rvw.id_product', $idProd)
            ->orderByDesc('rvw.id_review')
            ->when($limit !== 0, function ($query) use ($limit) {
                $query->limit($limit);
            })
            ->get();

        foreach ($reviews as $rvw) {
            $rvw->photo = asset('users/' . $rvw->photo);
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

    /// get all user review
    public function getAllReview(Request $request)
    {
        $idShop = $request->input('id_shop');
        $idProduct = $request->input('id_product', 0);
        $star = $request->input('star', 0);
        $limit = $request->input('limit', 0);

        // generate table product and review
        $tableRvw = $this->generateTableReview($idShop);
        $tableProd = $this->generateTableProd($idShop);

        // get all data review
        $getData = DB::table(DB::raw("$tableRvw as rvw"))
            ->join(DB::raw("$tableProd as prod"), 'prod.id_product', 'rvw.id_product')
            ->join('0users as us', 'us.id_user', 'rvw.id_user')
            ->orderByDesc('rvw.id_review')
            ->when($idProduct !== 0, function ($query) use ($idProduct) {
                $query->where('prod.id_product', $idProduct);
            })
            ->when($limit !== 0, function ($query) use ($limit) {
                $query->limit($limit);
            })
            ->when($star > 0 && $star <= 5, function ($query) use ($star) {
                $query->where('rvw.star', $star);
            })
            ->select('rvw.*', 'prod.product_name', 'us.full_name', 'us.email', 'us.photo')
            ->get();


        foreach ($getData as $prod) {
            $prod->photo = asset('users/' . $prod->photo);
        }

        return response()->json(['status' => 'success', 'message' => 'Data berhasil didapatkan', 'data' => $getData], 200);
    }

    public function getHighestReview(Request $request)
    {

        $idShop = $request->input('id_shop');
        $idCategory = $request->input('id_category', 0);
        $limit = $request->input('limit', 0);

        // generate table product and review
        $tableRvw = $this->generateTableReview($idShop);
        $tableProd = $this->generateTableProd($idShop);

        $products = DB::table(DB::raw("$tableProd as prod"))
            ->select(
                'prod.id_product',
                'prod.product_name',
                'prod.id_cp_prod',
                'prod.photo',
                DB::raw("AVG(rvw.star) as rating"),
                DB::raw("COUNT(rvw.id_review) as reviewer")
            )
            ->leftJoin(DB::raw("$tableRvw as rvw"), 'prod.id_product', 'rvw.id_product')
            ->groupBy('prod.id_product', 'prod.product_name', 'prod.id_cp_prod', 'prod.photo')
            ->orderByRaw('AVG(rvw.star) DESC')
            ->when($idCategory !== 0, function ($query) use ($idCategory) {
                $query->where('prod.id_cp_prod', $idCategory);
            })
            ->when($limit !== 0, function ($query) use ($limit) {
                $query->limit($limit);
            })
            ->get();

        foreach ($products as $prod) {
            $prod->rating = doubleval($prod->rating);
            $prod->photo = asset('prods/' . $prod->photo);
        }
        return response()->json(['status' => 'success', 'message' => 'Data berhasil didapatkan', 'data' => $products], 200);
    }
}
