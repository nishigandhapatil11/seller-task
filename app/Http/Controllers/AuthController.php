<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;

class AuthController extends Controller
{
    // ================= ADMIN LOGIN =================
    
public function adminLogin(Request $request)
{
    $user = User::where('email', $request->email)
                ->where('role', 'admin')
                ->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Admin not found'
        ], 404);
    }

    if (!Hash::check($request->password, $user->password)) {
        return response()->json([
            'status' => false,
            'message' => 'Password incorrect'
        ], 401);
    }

    $token = $user->createToken('admin-token')->plainTextToken;

    return response()->json([
        'status' => true,
        'message' => 'Admin Login Successful',
        'token' => $token,
        'data' => $user
    ], 200);
}
    // ================= SELLER LOGIN =================
    public function sellerLogin(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::where('email', $request->email)
                        ->where('role', 'seller')
                        ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Seller Credentials'
                ], 401);
            }

            $token = $user->createToken('SellerToken')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Seller Login Successful',
                'token' => $token,
                'role' => $user->role
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
