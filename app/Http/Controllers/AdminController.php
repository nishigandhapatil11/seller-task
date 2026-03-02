<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // =============== CREATE SELLER =================
public function createSeller(Request $request)
{
    DB::beginTransaction();

    try {

        if (auth()->user()->role != 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized Access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'mobile' => 'required',
            'country' => 'required',
            'state' => 'required',
            'skills' => 'required|array',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $seller = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'country' => $request->country,
            'state' => $request->state,
            'skills' => $request->skills,
            'password' => Hash::make($request->password),
            'role' => 'seller'
        ]);

        DB::commit(); // ✅ success

        return response()->json([
            'status' => true,
            'message' => 'Seller Created Successfully',
            'data' => $seller
        ], 201);

    } catch (Exception $e) {

        DB::rollBack(); // ❌ rollback if error

        return response()->json([
            'status' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

    // ================= SELLER LIST =================
    public function sellerList()
    {
        try {

            if (auth()->user()->role != 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized Access'
                ], 403);
            }

            $sellers = User::where('role', 'seller')->paginate(10);

            return response()->json([
                'status' => true,
                'data' => $sellers
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
