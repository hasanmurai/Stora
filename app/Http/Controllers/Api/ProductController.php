<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // 1. add product
    public function addProduct(Request $request, $shopId) {

        $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'required|image|max:2048',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            
        ]);

        $shop = $request->user()->shops()->find($shopId);

        if (!$shop) {
            return response()->json(['message' => 'Shop not found or unauthorized'], 404);
        }

        $product = $shop->products()->create([
            'name' => $request->name,
            'photo' => $request->file('photo')->store('products', 'public'),
            'description' => $request->description ? $request->description : null,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return response()->json(['product' => $product], 201);
    }
    
    // 2. edit product info
    public function editProduct(Request $request, $productId) {

        $product = $request->user()->products()->find($productId);
        
        // $product = Product::where('id', $productId)
        //     ->whereHas('shop', function ($query) {
        //         $query->where('user_id', Auth::id());
        //     })->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found or unauthorized'], 404);
        }

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'photo'       => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'description' => 'sometimes|string',
            'price'       => 'sometimes|numeric|min:0',
            'stock'       => 'sometimes|integer|min:0',
        ]);

        if ($request->hasFile('photo')) {
            
            Storage::disk('public')->delete($product->photo);
            $data['photo'] = $request->file('photo')->store('products', 'public');
        }

        $product->update($data);

        return response()->json([
            'message' => 'Product updated!','product' => $product->fresh()], 200);
        }


    // 3. delete product
    public function deleteProduct(Request $request, $productId) {
        $product = $request->user()->products()->find($productId);

        if (!$product) {
            return response()->json(['message' => 'Product not found or unauthorized'], 404);
        }

        Storage::disk('public')->delete($product->photo);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }

    // 4. list products of a shop
    public function listProducts($shopId) {

        $shop = Shop::with('products')->find($shopId);

        if (!$shop) {
            return response()->json(['message' => 'Shop not found'], 404);
        }

        return response()->json(['shop' => $shop], 200);
    }
}
