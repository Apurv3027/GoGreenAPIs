<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use Illuminate\Support\Facades\Validator;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Helper\Helper;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            // Display all products
            $products = Products::all();

            if ($products->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No products found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $products,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_name'          => 'required|unique:products,product_name',
                'product_price'         => 'required|numeric',
                'product_image'         => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                'product_description'   => 'required',
                'product_category'      => 'required',
                'product_tags'          => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ], 302);
            }

            // Insert Product Data
            $productData = Products::addProduct($request);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Product added successfully.',
                'data' => $productData,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 302);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $name)
    {
        try {
            // Find the product by name
            $product = Products::where('product_name', $name)->first();

            if (!$product) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product not found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $product,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
