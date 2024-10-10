<?php

namespace App\Http\Controllers;

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

class CategoryController extends Controller
{
    public function getAllCategories()
    {
        // Fetch all banners from the database
        $categories = Category::all();
        $totalCategories = $categories->count();

        return response()->json(
            [
                'status' => 'success',
                'totalCategories' => $totalCategories,
                'categories' => $categories,
            ],
            200,
        );
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Store the image in the 'public/categories' directory
            $path = $image->store('categories', 'public');

            // App URL
            $appurl = 'https://tortoise-new-emu.ngrok-free.app';

            // Generate a full URL to the image
            $url = $appurl . Storage::url($path);

            // Return the full URL so it can be accessed via the API
            return response()->json(['category_image_url' => url($url)], 200);
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
        $category = new Category();
        $category->category_name = $request->category_name;
        $category->category_image_url = $request->category_image_url;
        $category->category_item_count = 0;

        $category->save();

        return response()->json(['message' => 'Category created successfully'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the category by ID
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'category_name' => 'sometimes|string|max:255',
            'category_image_url' => 'sometimes|string',
            'category_item_count' => 'sometimes|numeric',
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
        if ($request->has('category_name')) {
            $category->category_name = $request->input('category_name');
        }

        // Handle file upload if a new image is provided
        if ($request->has('category_image_url')) {
            $category->category_image_url = $request->input('category_image_url');
        }

        // Handle field if provided
        if ($request->has('category_item_count')) {
            $category->category_item_count = 0;
        }

        $category->save();

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the category by ID
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Delete the category
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
