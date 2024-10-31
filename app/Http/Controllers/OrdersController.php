<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\OrderItem;
use App\Models\Cart;
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

class OrdersController extends Controller
{
    public function getTotalSales()
    {
        $totalSales = Orders::sum('total_amount');
        return response()->json(
            [
                'status' => 'success',
                'total_sales' => $totalSales,
            ],
            200,
        );
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = Orders::with(['user', 'address', 'orderItems'])->get();
        $totalOrders = $orders->count();

        return response()->json(
            [
                'status' => 'success',
                'totalOrders' => $totalOrders,
                'data' => $orders,
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
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'address_id' => 'required|exists:addresses,id',
            'order_id' => 'required|numeric',
            'total_amount' => 'required|numeric',
        ]);

        $cartItems = Cart::where('user_id', $request->user_id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Cart is empty.',
                ],
                400,
            );
        }

        // Create the order
        $order = Orders::create([
            'user_id' => $request->user_id,
            'address_id' => $request->address_id,
            'order_id' => $request->order_id,
            'total_amount' => $request->total_amount,
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ]);
        }

        Cart::where('user_id', $request->user_id)->delete();

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Order created successfully.',
                'data' => $order,
            ],
            201,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User not found.',
                ],
                404,
            );
        }

        $orders = Orders::with(['user', 'address', 'orderItems'])
            ->where('user_id', $userId)
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'No orders found for this user.',
                ],
                404,
            );
        }

        return response()->json(
            [
                'status' => 'success',
                'data' => $orders,
            ],
            200,
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Orders $orders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Orders $orders)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Orders $orders)
    {
        //
    }
}
