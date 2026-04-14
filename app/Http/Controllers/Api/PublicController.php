<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Product, Shop};
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        $products = Product::with('shop')
            // check if user is active
            ->whereHas('shop', function($q) {
                $q->active();
            })

            ->where(function($q) use ($query) {
                // search by product name or description
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  // search by shop name
                  ->orWhereHas('shop', function($sq) use ($query) {
                      $sq->where('name', 'LIKE', "%{$query}%");
                  });
            })
            ->paginate(12); 

        return response()->json($products);
    }

    public function showShop($slug)
    {
        $shop = Shop::active()
            ->with('products') 
            ->where('slug', $slug)
            ->first() ?: abort(404, 'Shop not found');

        return response()->json($shop);
    }

    public function showProduct($slug)
    {
        $product = Product::where('slug', $slug)
            ->whereHas('shop', function ($user) {
                $user->active();
            })
            ->with('shop')
            ->first() ?: abort(404, 'Product not found');

        return response()->json($product);
    }
}