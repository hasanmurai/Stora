<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{User, Shop, Product};
use Illuminate\Http\Request;

class AdminController extends Controller
{

    // only owner allowed to assign role to others
    public function assignRole(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);

        $this->authorize('changeRole', $targetUser);

        $data = $request->validate([
            'role' => 'required|in:user,admin,owner',
        ]);

        $targetUser->role = $data['role'];
        $targetUser->save();

        return response()->json(['message' => "User role updated to {$targetUser->role}"]);
    }

    public function listAllUsers()
    {
        $users = User::withCount(['shops'])->get();

        return response()->json([
            'total_users' => $users->count(),
            'users' => $users
        ], 200);
    }

    public function stats()
    {
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
        $query = $request->query('q');

        if (blank($query)) {
        return response()->json([
            'message' => 'Search term is empty.',
            'users' => [],
            'shops' => [],
            'products' => []
            ]);
        }
        $users = User::where('name', 'LIKE', "%$query%")
                    ->orWhere('email', 'LIKE', "%$query%")
                    ->get();

        $shops = Shop::where('name', 'LIKE', "%$query%")
                    ->get();

        return response()->json([
            'users' => $users,
            'shops' => $shops
            ], 200);
    }

    // owner can ban admin and admin can only ban user
    public function toggleStatus($id)
    {
        /** @var User */
        $targetUser = User::find($id) ?: abort(404, 'User not found');

        $this->authorize('toggleStatus', $targetUser);

        $targetUser->status = $targetUser->isBanned() ? 'active' : 'banned';
        $targetUser->save();

        if ($targetUser->status === 'banned') {
        $targetUser->tokens()->delete(); 
        }

        return response()->json([
        'status' => 'success',
        'message' => "User is now {$targetUser->status}",
        'user_id' => $targetUser->id
    ], 200);
    }



}