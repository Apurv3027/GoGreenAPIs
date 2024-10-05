<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Helper\Helper;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function getAllProducts()
    {
        // Fetch all banners from the database
        $products = Product::all();

        return response()->json(
            [
                'status' => 'success',
                'products' => $products,
            ],
            200,
        );
    }

    public function getProductById($id)
    {
        // Find the product by ID, or return a 404 response if not found
        $product = Product::find($id);

        if ($product) {
            return response()->json(
                [
                    'status' => 'success',
                    'product' => $product,
                ],
                200,
            );
        } else {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Product not found',
                ],
                404,
            );
        }
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Store the image in the 'public/products' directory
            $path = $image->store('products', 'public');

            // App URL
            $appurl = 'https://tortoise-new-emu.ngrok-free.app';

            // Generate a full URL to the image
            $url = $appurl . Storage::url($path);

            // Return the full URL so it can be accessed via the API
            return response()->json(['product_image_url' => url($url)], 200);
        }

        return response()->json(['error' => 'Image upload failed'], 400);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric',
            'product_image_url' => 'required|string',
            'product_description' => 'required|string',
            'product_category' => 'required|string',
        ]);

        $category = Category::where('category_name', $request->product_category)->first();

        if ($category) {
            $product = new Product();
            $product->product_name = $request->product_name;
            $product->product_price = $request->product_price;
            $product->product_image_url = $request->product_image_url;
            $product->product_description = $request->product_description;
            $product->product_category = $category->category_name;

            $product->save();

            return response()->json(
                [
                    'message' => 'Product created successfully',
                    'product' => $product,
                ],
                200,
            );
        } else {
            return response()->json(['error' => 'Category does not exist'], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the product by ID
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'product_name' => 'sometimes|string|max:255',
            'product_price' => 'sometimes|string',
            'product_image_url' => 'sometimes|string',
            'product_description' => 'sometimes|string',
            'product_category' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ],
                422,
            ); // Use 422 for validation errors
        }

        // Update fields if provided
        if ($request->has('product_name')) {
            $product->product_name = $request->input('product_name');
        }

        // Update fields if provided
        if ($request->has('product_price')) {
            $product->product_price = $request->input('product_price');
        }

        // Handle file upload if a new image is provided
        if ($request->has('product_image_url')) {
            $product->product_image_url = $request->input('product_image_url');
        }

        // Update fields if provided
        if ($request->has('product_description')) {
            $product->product_description = $request->input('product_description');
        }

        // Check if the category exists in the Categories table
        if ($request->has('product_category')) {
            $categoryName = $request->input('product_category');
            $category = Category::where('category_name', $categoryName)->first();

            if ($category) {
                // If the category exists, update the product's category Name
                $product->product_category = $category->category_name;
            } else {
                // If the category does not exist, return an error response
                return response()->json(['message' => 'Category not found'], 400);
            }
        }

        $product->save();

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the product by ID
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Delete the product
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
