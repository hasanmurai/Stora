<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    // 1. user registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed', 
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('Personal Access Token')->accessToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    // 2. user login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->isBanned()) {
                Auth::logout(); 
                return response()->json([
                    'message' => 'Your account has been deactivated. Please contact support.'
                ], 403);
            }

            $token = $user->createToken('Personal Access Token')->accessToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token
            ], 200);
        }

        return response()->json(['message' => 'Invalid email or password.'], 401);
    }

    // 3. user logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->each(function (Token $token) {
        $token->revoke();
        $token->refreshToken?->revoke();
        }); 
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    // 4. change password
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

    // 5. delete account
    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        if(!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->shops()->delete(); 
        $user->delete();

        return response()->json(['message' => 'Account deleted successfully'], 200);
    }

    // 6. edit profile
    public function editProfile(Request $request)
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

    // 7. get user profile
    public function getProfile(Request $request)
    {
        return response()->json(['user' => $request->user()], 200);
    }

}