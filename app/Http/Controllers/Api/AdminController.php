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
        return response()->json([
            'total_users' => User::count(),
            'total_shops' => Shop::count(),
            'total_products' => Product::count(),
        ]);
    }
}