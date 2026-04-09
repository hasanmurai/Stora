<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{User, Shop, Product};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function assignRole(Request $request, $id){

        $request->validate([
        'role' => 'required|in:user,admin,owner'
        ]);

        $targetUser = User::findOrFail($id);
        $currentUser = Auth::user();

        if ($request->role === 'owner' && !$currentUser->isOwner()) {
            return response()->json(['message' => 'Only the current Owner can appoint a new Owner.'], 403);
        }

        if ($targetUser->isOwner() && !$currentUser->isOwner()) {
            return response()->json(['message' => 'Admins cannot modify the Owner account.'], 403);
        }

        if ($currentUser->id === $targetUser->id) {
            return response()->json(['message' => 'You cannot change your own role.'], 403);
        }

        $targetUser->role = $request->role;
        $targetUser->save();

        return response()->json(['message' => "Role updated to {$request->role}"]);

    }

    public function listAllUsers(){

        $users = User::withCount(['shops'])->get();

        return response()->json([
            'total_users' => $users->count(),
            'users' => $users
        ], 200);
    }

    public function stats(){

        $stats = [
            'total_users' => User::count(),
            'total_shops' => Shop::count(),
            'total_products' => Product::count(),
        ];

        $growth = [
            'new_users_today' => User::where('created_at', '>=', now()->startOfDay())->count(),
            'active_shops' => Shop::has('products')->count(),
            'empty_shops' => Shop::has('products', '=', 0)->count(),
        ];

        $recent = [
            'latest_shops' => Shop::with('user:id,name')
                                ->latest()
                                ->take(5)
                                ->get(),
            'latest_users' => User::latest()
                                ->take(5)
                                ->get(['id', 'name', 'email', 'role', 'created_at']),
        ];

        return response()->json([
            'stats' => $stats,
            'growth' => $growth,
            'recent' => $recent,
        ], 200);
    }

    public function search(Request $request)
    {
        // 1. Grab the search term from the URL (e.g., ?q=electronics)
        $query = $request->query('q');

        if (blank($query)) {
        return response()->json([
            'message' => 'Search term is empty.',
            'users' => [],
            'shops' => [],
            'products' => []
        ]);
    }
        // 2. Search the Users table for matching names or emails
        $users = User::where('name', 'LIKE', "%$query%")
                    ->orWhere('email', 'LIKE', "%$query%")
                    ->get();

        // 3. Search the Shops table for matching shop names
        $shops = Shop::where('name', 'LIKE', "%$query%")
                    ->get();

        // 4. Combine them into one response
        return response()->json([
            'users' => $users,
            'shops' => $shops
        ]);
    }
}