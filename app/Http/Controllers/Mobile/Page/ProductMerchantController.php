<?php

namespace App\Http\Controllers\Mobile\Page;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\Category\CategoryController;
use App\Http\Controllers\Mobile\Product\ProductComplainController;
use App\Http\Controllers\Mobile\Product\ProductController;
use App\Http\Controllers\Mobile\Product\ProductHistoryController;
use App\Http\Controllers\Mobile\Product\ProductReviewController;
use App\Models\ProductCategories;
use App\Models\Shops;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\returnSelf;

class ProductMerchantController extends Controller
{

    public function generateTableName($idShop)
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

    public function isExistProduct($tableName, $idProd)
    {
        $isExist = DB::table($tableName)->where('id_product', '=', $idProd)->limit(1)->exists();

        if ($isExist) {
            return ['status' => 'success', 'message' => 'ID produk sudah terdaftar'];
        } else {
            return ['status' => 'error', 'message' => 'ID produk belum terdaftar'];
        }
    }

    public function page(
        Request $request,
        CategoryController $categoryController,
        ProductController $productController,
        ProductReviewController $productReviewController,
        ProductCategories $productCategories,
    ) {

        $idShop = $request->input('id_shop');
        $filter = $request->input('id_category', 0);
        $limit = $request->input('limit', 1000);

        $isExistShop = $this->isExistShop($idShop);

        if ($isExistShop['status'] === 'success') {

            // get categories
            $categoryData = $categoryController->allCategories($productCategories)->getData();
            if ($categoryData->status === 'success') {
                $categories = $categoryData->data;
            } else {
                $categories = [];
            }

            // list product by category
            $productData = $productController->allProducts($request)->getData();
            if ($productData->status === 'success') {
                $products = $productData->data;
            } else {
                $products = [];
            }

            // highest rating
            $highestData = $productReviewController->getHighestReview($request)->getData();
            if ($highestData->status === 'success') {
                $highest = $highestData->data;
            } else {
                $highest = [];
            }

            // best selling
            $sellingData = $productController->bestSelling($request)->getData();

            if ($sellingData->status === 'success') {
                $sellings = $sellingData->data;
            } else {
                $sellings = [];
            }

            // put data
            $data = [
                'categories' => $categories,
                'products' => $products,
                'highests' => $highest,
                'sellings' => $sellings,
            ];

            return response()->json(['status' => 'success', 'message' => 'Data didapatkan', 'data' => $data], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }
    }

    public function detailProduct(
        Request $request,
        ProductController $productController,
        ProductReviewController $productReview,
        ProductComplainController $productComplain,
        ProductHistoryController $prodcutHistory,
    ) {
        $idShop = $request->input('id_shop');
        $idProd = $request->input('id_product');

        // generate table name
        $tableName = $this->generateTableName($idShop);

        // if shop not exist
        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }

        // if produk not exist
        $isExistProd = $this->isExistProduct($tableName, $idShop);
        if ($isExistProd['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => 'Produk tidak ditemukan'], 400);
        }

        // get detail product
        $detailProd = $productController
            ->detailProduct($request, $productReview, $productComplain, $prodcutHistory)
            ->getData();

        // return detail product
        if ($detailProd->status === 'success') {
            return response()->json(['status' => 'success', 'message' => 'Data detail produk didapatkan', 'data' => $detailProd->data], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Data detail produk gagal didapatkan'], 400);
        }
    }

    public function detailListReview(Request $request, ProductReviewController $review)
    {
        $idShop = $request->input('id_shop');
        $idShop = $request->input('id_product');

        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'success') {
            // get list review
            $revData = $review->getAllReview($request)->getData();

            // return list review
            if ($revData->status === 'success') {
                return response()->json(['status' => 'success', 'message' => 'Data list review didapatkan', 'data' => $revData->data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data list review gagal didapatkan'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }
    }

    public function detailListComplain(Request $request, ProductComplainController $complain)
    {
        $idShop = $request->input('id_shop');
        $idShop = $request->input('id_product');

        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'success') {
            // get list comp
            $compData = $complain->getComplains($request)->getData();

            // return list comp
            if ($compData->status === 'success') {
                return response()->json(['status' => 'success', 'message' => 'Data list complain didapatkan', 'data' => $compData->data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data list complain gagal didapatkan'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 404);
        }
    }

    public function detailListHistory(Request $request, ProductHistoryController $history)
    {
        $idShop = $request->input('id_shop');
        $idShop = $request->input('id_product');

        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'success') {
            // get list history
            $histData = $history->historyProduct($request)->getData();

            // return list history
            if ($histData->status === 'success') {
                return response()->json(['status' => 'success', 'message' => 'Data list history didapatkan', 'data' => $histData->data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data list history gagal didapatkan'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 404);
        }
    }

    public function reviewPage(Request $request, ProductReviewController $productReview)
    {
        $idShop = $request->input('id_shop');

        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'success') {
            // get data review
            $rvwData = $productReview->getAllReview($request)->getData();

            // return review data
            if ($rvwData->status === 'success') {
                return response()->json(['status' => 'success', 'message' => 'Data review didapatkan', 'data' => $rvwData->data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data review gagal didapatkan'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }
    }

    public function complainPage(Request $request, ProductComplainController $productComplain)
    {
        $idShop = $request->input('id_shop');

        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'success') {
            // get complain data
            $compData = $productComplain->getComplains($request)->getData();

            // return complain data
            if ($compData->status === 'success') {
                return response()->json(['status' => 'success', 'message' => 'Data complain didapatkan', 'data' => $compData->data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data complain gagal didapatkan'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }
    }

    public function unvailablePage(Request $request, ProductController $product)
    {
        $idShop = $request->input('id_shop');

        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'success') {
            // get data stok habis
            $unavlData = $product->unavlProducts($request)->getData();

            // return stok habis
            if ($unavlData->status === 'success') {
                return response()->json(['status' => 'success', 'message' => 'Data stok habis didapatkan', 'data' => $unavlData->data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data stok habis gagal didapatkan'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }
    }

    public function recommendedPage(Request $request, ProductController $product)
    {
        $idShop = $request->input('id_shop');

        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'success') {
            // get data produk rekomendasi
            $recomData = $product->recommendedProducts($request)->getData();

            // return produk rekomendasi
            if ($recomData->status === 'success') {
                return response()->json(['status' => 'success', 'message' => 'Data recommended didapatkan', 'data' => $recomData->data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data recommended gagal didapatkan'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }
    }

    public function hiddenPage(Request $request, ProductController $product)
    {
        $idShop = $request->input('id_shop');

        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'success') {
            // get data produk disembuyikan
            $hiddenData = $product->hiddenProducts($request)->getData();

            // return produk disembunyikan
            if ($hiddenData->status === 'success') {
                return response()->json(['status' => 'success', 'message' => 'Data disembunyikan didapatkan', 'data' => $hiddenData->data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data disembunyikan gagal didapatkan'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }
    }

    public function highestPage(Request $request, ProductReviewController $productReview)
    {
        $idShop = $request->input('id_shop');

        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'success') {
            // get highest rating
            $highestData = $productReview->getHighestReview($request)->getData();

            // return highest rating
            if ($highestData->status === 'success') {
                return response()->json(['status' => 'success', 'message' => 'Data rating didapatkan', 'data' => $highestData->data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data rating gagal didapatkan'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }
    }

    public function bestSellingPage(Request $request, ProductController $product)
    {
        $idShop = $request->input('id_shop');

        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'success') {
            // get produk terlair
            $bestData = $product->bestSelling($request)->getData();

            // return produk terlaris
            if ($bestData->status === 'success') {
                return response()->json(['status' => 'success', 'message' => 'Data terlaris didapatkan', 'data' => $bestData->data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data terlaris gagal didapatkan'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }
    }

    public function listOfCategory(Request $request, CategoryController $category, ProductController $productController)
    {
        $idShop = $request->input('id_shop');

        $isExistShop = $this->isExistShop($idShop);
        if ($isExistShop['status'] === 'success') {
            // get category
            $bestData = $category->allCategoryByProduct($request, $productController)->getData();

            // return category
            if ($bestData->status === 'success') {
                return response()->json(['status' => 'success', 'message' => 'Data category didapatkan', 'data' => $bestData->data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data category gagal didapatkan'], 400);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 400);
        }
    }
}
