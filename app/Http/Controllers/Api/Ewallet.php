<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Ewallet extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
            ]);

            // If validation fails, return errors
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()
                ], 400);
            }

            // Create the user with a hashed password
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Return a success response with the user data
            return response()->json([
                'status' => true,
                'message' => 'Registered Successfully'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // If validation fails, return errors
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()
                ], 400);
            }

            // Check credentials
            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'error' => 'Incorrect Credentials'
                ], 401);  // Changed status to 401 (Unauthorized)
            }

            // Find user and generate token
            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'data' => [
                    'token' => $user->createToken("Login Token")->plainTextToken
                ]
            ], 200);  // Changed status to 200 (OK)
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function profile()
    {
        $userData = auth()->user();
        return response()->json([
            'status' => true,
            'message' => 'Profile Information',
            'data' => $userData
        ], 200);
    }

    public function logout(Request $request)
    {
        // In case of multiple tokens, you might want to revoke all of them.
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'User Logged Out'
        ], 200);
    }
}
