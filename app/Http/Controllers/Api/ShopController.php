<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Shop;

class ShopController extends Controller
{
    /**
     * Add a new shop for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // 1. add shop
    public function addShop(Request $request) {

        $request->validate([
            'shopName' => 'required|string|max:255|unique:shops,name',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        $shop = $request->user()->shops()->create([
            'name' => $request->shopName,
            'photo' => $request->photo ? $request->file('photo')->store('shops', 'public') : null,
            'description' => $request->description ? $request->description : null,
        ]);

        return response()->json(['shop' => $shop], 201);
    }


    // 2. list shops
    public function listShops(Request $request) {

        $shops = $request->user()->shops()->get();

        return response()->json(['shops' => $shops], 200);
    }

    // 3. delete shop
    public function deleteShop($id)
    {
        /** @var Shop $shop */
        $shop = Shop::find($id) ?: abort(404, 'shop not found');

        $this->authorize('delete', $shop);

        $shop->delete();

        return response()->json(['message' => 'Shop deleted successfully'], 200);
    }

    // 4. edit shop info
    public function editShop(Request $request, $id) {
        
        /** @var Shop $shop */
        $shop = Shop::find($id) ?: abort(404, 'shop not found');

        $this->authorize('update', $shop);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255|unique:shops,name,' . $shop->id,
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'sometimes|string',
        ]);

        if ($request->hasFile('photo')) {
        $data['photo'] = $request->file('photo')->store('shops', 'public');
    }
        $shop->update($data);

        return response()->json(['shop' => $shop->fresh()], 200);
    }

}
