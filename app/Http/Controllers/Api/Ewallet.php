<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
                    'error' => 'Validation Error'
                ], 422);
            }

            // Create the user with a hashed password
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Return a success response with the user data and token
            return response()->json([
                'status' => true,
                'data' => [
                    'user' => $user,
                    'token' => $user->createToken('Ewallet Token')->plainTextToken
                ]
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage()
            ]);
        }
    }
}
