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
    public function addShop(Request $request)
    {
        $request->validate([
            'shopName' => 'required|string|max:255|unique:shops,name',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        $shop = $request->user()->shops()->create([
            'name' => $request->shopName,
            'slug' => $this->generateSlug($request->shopName),
            'photo' => $request->photo ? $request->file('photo')->store('shops', 'public') : null,
            'description' => $request->description ? $request->description : null,
        ]);

        return response()->json(['shop' => $shop], 201);
    }

    /**
     * Generate a slug from the given shop name.
     *
     * @param string $shopName
     * @return string
     */
    private function generateSlug($shopName)
    {
        return Str::slug($shopName);
    }

    public function listShops(Request $request)
    {
        $shops = $request->user()->shops()->get();

        return response()->json(['shops' => $shops], 200);
    }

    public function deleteShop(Request $request, $id)
    {
        $shop = $request->user()->shops()->find($id);

        if (!$shop) {
            return response()->json(['message' => 'Shop not found'], 404);
        }

        $shop->delete();

        return response()->json(['message' => 'Shop deleted successfully'], 200);
    }

    public function updateShop(Request $request, $id)
    {

        $shop = Shop::where('user_id', $request->user()->id)->find($id);

        if (!$shop) {
            return response()->json(['message' => 'Shop not found'], 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255|unique:shops,name,' . $shop->id,
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'sometimes|string',
        ]);

        if ($request->filled('name') && $request->name !== $shop->name) {
            $data['slug'] = $this->generateSlug($request->name);
        }

        $shop->update($data);

        return response()->json(['shop' => $shop->fresh()], 200);
    }

    // public function getShop(Request $request, $id)
    // {
    //     $shop = $request->user()->shops()->find($id);

    //     if (!$shop) {
    //         return response()->json(['message' => 'Shop not found'], 404);
    //     }

    //     return response()->json(['shop' => $shop], 200);
    // }

}
