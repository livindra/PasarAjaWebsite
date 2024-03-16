<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shops;
use App\Models\User;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    
    // CREATE
    public function store(Request $request)
    {
        $shop = new Shops();
        $shop->id_user = $request->input('id_user');
        // $shop->id_cp_shop = $request->input('id_cp_shop');
        $shop->shop_name = $request->input('shop_name');
        $shop->description = $request->input('description');
        $shop->benchmark = $request->input('benchmark');
        $shop->operational = $request->input('operational');
        $shop->photo = $request->input('photo');
        $shop->save();

        return response()->json(['status'=>'success', 'message' => 'Shop created successfully', 'data'=>$shop], 201);
    }

    // UPDATE
    public function update(Request $request)
    {
        $id = $request->input('id_shop');
        $shop = Shops::findOrFail($id);
        $shop->update($request->all());

        return response()->json(['status'=>'success', 'message' => 'Shop updated successfully'], 200);
    }

    // DELETE
    public function destroy(Request $request)
    {   $idshop = $request->input('id_shop');
        $shop = Shops::findOrFail($idshop);
        $shop->delete();

        return response()->json(['status'=>'success', 'message' => 'Shop deleted successfully'], 200);
    }

    // DELETE
    public function showUserData()
{
    $users = User::all();

    return response()->json(['status'=>'success', 'message' => 'Show all data', 'users' => $users], 200);
}
}
