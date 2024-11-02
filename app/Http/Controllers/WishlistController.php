<?php

namespace App\Http\Controllers;
use App\Models\Wishlist;
use App\Models\User;
use App\Models\Product;
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

class WishlistController extends Controller
{
    public function addToWishlist(Request $request, $user_id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = User::findOrFail($user_id);
        $user->wishlistItems()->syncWithoutDetaching([$request->product_id]);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Product added to wishlist successfully',
            ],
            200,
        );
    }

    public function removeFromWishlist(Request $request, $user_id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = User::findOrFail($user_id);
        $user->wishlistItems()->detach($request->product_id);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Product removed from wishlist',
            ],
            200,
        );
    }

    public function viewWishlist($user_id)
    {
        $user = User::findOrFail($user_id);
        $wishlist = $user->wishlistItems()->get();

        return response()->json(
            [
                'status' => 'success',
                'wishlistItems' => $wishlist,
            ],
            200,
        );
    }
}
