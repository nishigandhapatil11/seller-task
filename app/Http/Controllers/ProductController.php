<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Brand;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Illuminate\Support\Facades\DB;
class ProductController extends Controller
{
    // ================= ADD PRODUCT =================
public function addProduct(Request $request)
{
    DB::beginTransaction();

    try {

        // if (auth()->user()->role != 'seller') {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Unauthorized Access'
        //     ], 403);
        // }

        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'product_description' => 'required',
            'brands' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Create Product
        $product = Product::create([
            'seller_id' => auth()->id(),
            'product_name' => $request->product_name,
            'product_description' => $request->product_description
        ]);

        // Create Brands
        foreach ($request->brands as $brand) {

            Brand::create([
                'product_id' => $product->id,
                'brand_name' => $brand['brand_name'],
                'detail' => $brand['detail'],
                'image' => $brand['image'],
                'price' => $brand['price']
            ]);
        }

        DB::commit(); // ✅ everything successful

        return response()->json([
            'status' => true,
            'message' => 'Product Created Successfully'
        ], 201);

    } catch (Exception $e) {

        DB::rollBack(); // ❌ rollback product + brands

        return response()->json([
            'status' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

    // ================= PRODUCT LIST =================
    public function productList()
    {
        try {

            $products = Product::with('brands')
                ->where('seller_id', auth()->id())
                ->paginate(10);

            return response()->json([
                'status' => true,
                'data' => $products
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ================= DELETE PRODUCT =================
    public function deleteProduct($id)
    {
        try {

            $product = Product::where('id', $id)
                ->where('seller_id', auth()->id())
                ->first();

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product Not Found or Unauthorized'
                ], 404);
            }

            $product->delete();

            return response()->json([
                'status' => true,
                'message' => 'Product Deleted Successfully'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ================= PRODUCT PDF =================
public function productPDF($id)
{
    try {

        // ================= STEP 1: Check Authentication =================
        if (!auth()->check()) {
            return response()->json([
                'status' => false,
                'step' => 'Auth Check Failed',
                'message' => 'User not logged in'
            ], 401);
        }

        // ================= STEP 2: Show Logged-in User =================
        $user = auth()->user();

        // Uncomment this line if you want to see full user data
        // dd($user);

        // ================= STEP 3: Check Product Exists =================
        $productCheck = \App\Product::find($id);

        if (!$productCheck) {
            return response()->json([
                'status' => false,
                'step' => 'Product Not Found in DB',
                'product_id' => $id
            ], 404);
        }

        // ================= STEP 4: Check Seller Match =================
        if ($productCheck->seller_id != $user->id) {
            return response()->json([
                'status' => false,
                'step' => 'Seller ID Mismatch',
                'logged_in_user_id' => $user->id,
                'product_seller_id' => $productCheck->seller_id
            ], 403);
        }

        // ================= STEP 5: Load Product With Brands =================
        $product = \App\Product::with('brands')
            ->where('seller_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$product) {
            return response()->json([
                'status' => false,
                'step' => 'Final Query Failed'
            ], 404);
        }

        // ================= STEP 6: Check Brands =================
        if ($product->brands->isEmpty()) {
            return response()->json([
                'status' => false,
                'step' => 'No Brands Found For This Product'
            ]);
        }

        // ================= STEP 7: Calculate Total =================
        $total = $product->brands->sum('price');

        // ================= STEP 8: Generate PDF =================
        $pdf = \PDF::loadView('pdf.product', compact('product', 'total'));

        return $pdf->download('product.pdf');

    } catch (\Exception $e) {

        return response()->json([
            'status' => false,
            'step' => 'Exception Caught',
            'error_message' => $e->getMessage(),
            'line' => $e->getLine()
        ], 500);
    }
}
}
