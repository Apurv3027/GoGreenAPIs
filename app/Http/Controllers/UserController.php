<?php

namespace App\Http\Controllers;

use App\Models\User;
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

class UserController extends Controller
{
    public function updateAddress(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'street_1' => 'required|string|max:255',
                'street_2' => 'nullable|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => $validator->errors()->first(),
                    ],
                    400,
                );
            }

            // Find the user by ID
            $user = User::find($id);

            // Check if user exists
            if (!$user) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'User not found',
                    ],
                    404,
                );
            }

            // Update user's address
            $user->update([
                'street_1' => $request->input('street_1'),
                'street_2' => $request->input('street_2'),
                'city' => $request->input('city'),
                'state' => $request->input('state'),
            ]);

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Address updated successfully',
                    'data' => $user,
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $th->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all users from the database
        $users = User::where('email', '!=', 'admin@gmail.com')->get();
        $totalUsers = $users->count();

        return response()->json(
            [
                'status' => 'success',
                'totalUsers' => $totalUsers,
                'users' => $users,
            ],
            200,
        );
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Find the user by ID, or return a 404 response if not found
        $user = User::find($id);

        if ($user) {
            return response()->json(
                [
                    'status' => 'success',
                    'user' => $user,
                ],
                200,
            );
        } else {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User not found',
                ],
                404,
            );
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
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $id,
            'mobile_number' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User not found.',
                ],
                404,
            );
        }

        // Update user profile
        $user->fullname = $request->input('fullname', $user->fullname);
        $user->email = $request->input('email', $user->email);
        $user->mobile_number = $request->input('mobile_number', $user->mobile_number);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully.',
            'data' => $user,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
