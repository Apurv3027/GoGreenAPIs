<?php

namespace App\Http\Controllers;

use App\Models\Address;
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

class AddressController extends Controller
{
    public function getSelectedAddress($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User not found',
                ],
                404,
            );
        }

        $address = Address::find($user->selected_address_id);

        if (!$address) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'No address selected',
                ],
                404,
            );
        }

        return response()->json([
            'success' => true,
            'user' => [
                'fullname' => $user->fullname,
                'mobile_number' => $user->mobile_number,
            ],
            'address' => $address,
        ]);
    }

    public function selectAddress(Request $request, $userId)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);

        $user = User::find($userId);

        if (!$user) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User not found',
                ],
                404,
            );
        }

        // Check if the address belongs to the user
        $address = Address::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json(['error' => 'Invalid address selection'], 403);
        }

        // Update the selected address in the user record
        $user->selected_address_id = $request->address_id;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Address selected successfully',
            'selected_address_id' => $user->selected_address_id,
        ]);
    }

    public function deselectAddress(Request $request, $userId)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);

        $user = User::find($userId);

        if (!$user) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User not found',
                ],
                404,
            );
        }

        // Check if the address belongs to the user
        $address = Address::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json(['error' => 'Invalid address selection'], 403);
        }

        // Deselect the address (set to null)
        $user->selected_address_id = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Address deselected successfully',
            'selected_address_id' => $user->selected_address_id,
        ]);
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
    public function store(Request $request, $userId)
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

            $user = User::find($userId);

            if (!$user) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'User not found',
                    ],
                    404,
                );
            }

            // Create a new address record for the user
            $address = $user->addresses()->create([
                'street_1' => $request->input('street_1'),
                'street_2' => $request->input('street_2'),
                'city' => $request->input('city'),
                'state' => $request->input('state'),
            ]);

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Address added successfully',
                    'data' => $address,
                    'user' => $user,
                ],
                201,
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
     * Display the specified resource.
     */
    public function show($userId)
    {
        try {
            $user = User::with('addresses')->find($userId);

            if (!$user) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'User not found',
                    ],
                    404,
                );
            }

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'User addresses retrieved successfully',
                    'data' => [
                        'id' => $user->id,
                        'fullname' => $user->fullname,
                        'email' => $user->email,
                        'mobile_number' => $user->mobile_number,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                        'addresses' => $user->addresses,
                    ],
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
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Address $address)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        //
    }
}
