<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Brand;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;

class ProductController extends Controller
{
    // ================= ADD PRODUCT =================
    public function addProduct(Request $request)
    {
        try {

            if (auth()->user()->role != 'seller') {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized Access'
                ], 403);
            }

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

            $product = Product::create([
                'seller_id' => auth()->id(),
                'product_name' => $request->product_name,
                'product_description' => $request->product_description
            ]);

            foreach ($request->brands as $brand) {
                Brand::create([
                    'product_id' => $product->id,
                    'brand_name' => $brand['brand_name'],
                    'detail' => $brand['detail'],
                    'image' => $brand['image'],
                    'price' => $brand['price']
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Product Created Successfully'
            ], 201);

        } catch (Exception $e) {
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

            $product = Product::with('brands')
                ->where('seller_id', auth()->id())
                ->findOrFail($id);

            $total = $product->brands->sum('price');

            $pdf = PDF::loadView('pdf.product', compact('product', 'total'));

            return $pdf->download('product.pdf');

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}