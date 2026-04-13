<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
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
}