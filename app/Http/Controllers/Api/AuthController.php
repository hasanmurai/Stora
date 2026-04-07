<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Token;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed', 
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('StoreToken')->accessToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('StoreToken')->accessToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {

        $request->user()->tokens()->each(function (Token $token) {
        $token->revoke();
        $token->refreshToken?->revoke();
        }); 
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function user(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    public function changePassword(Request $request)
    {

        $user = $request->user();
        
        $request->validate([
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 401);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully'], 200);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        $user->shops()->delete(); 
        $user->delete();

        return response()->json(['message' => 'Account deleted successfully'], 200);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:users,name,' . $user->id,
            'email' => 'sometimes|required|string|email|unique:users,email,' . $user->id,
        ]);

        $user->update($data);

        return response()->json(['message' => 'Profile updated successfully',
         'user' => $user->fresh()], 200);
    }


}