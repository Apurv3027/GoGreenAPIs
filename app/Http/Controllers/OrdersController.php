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
            'payment_type' => 'required|in:Online Payment,Cash on Delivery',
            'payment_id' => 'required',
            'order_status' => 'nullable|in:Processing,Delivered,Canceled,Returned',
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
            'payment_type' => $request->payment_type,
            'payment_id' => $request->payment_id,
            'order_status' => $request->order_status ?? 'Processing',
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ]);
        }

        Cart::where('user_id', $request->user_id)->delete();

        // Remove selected address from user's table after order is placed
        $user = User::find($request->user_id);
        $user->selected_address_id = null;
        $user->save();

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

        // Count orders by status
        $orderStatusCounts = $orders->groupBy('order_status')->map(function ($group) {
            return $group->count();
        });

        return response()->json(
            [
                'status' => 'success',
                'data' => [
                    'orders' => $orders,
                    'order_status_counts' => $orderStatusCounts,
                ],
            ],
            200,
        );
    }

    public function showDeliveredOrders(Request $request, $userId)
    {
        // Find the user by ID
        $user = User::find($userId);

        // Check if the user exists
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
            ->where('order_status', 'Delivered')
            ->get();

        // Check if there are no orders with 'Delivered' status
        if ($orders->isEmpty()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'No delivered orders found for this user.',
                ],
                404,
            );
        }

        // Count orders by status (if needed, you can also count only delivered orders here)
        $orderStatusCounts = $orders->groupBy('order_status')->map(function ($group) {
            return $group->count();
        });

        // Return the response
        return response()->json(
            [
                'status' => 'success',
                'data' => [
                    'orders' => $orders,
                    'order_status_counts' => $orderStatusCounts,
                ],
            ],
            200,
        );
    }

    public function showProcessingOrders(Request $request, $userId)
    {
        // Find the user by ID
        $user = User::find($userId);

        // Check if the user exists
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
            ->where('order_status', 'Processing')
            ->get();

        // Check if there are no orders with 'Processing' status
        if ($orders->isEmpty()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'No processing orders found for this user.',
                ],
                404,
            );
        }

        // Count orders by status (if needed, you can also count only delivered orders here)
        $orderStatusCounts = $orders->groupBy('order_status')->map(function ($group) {
            return $group->count();
        });

        // Return the response
        return response()->json(
            [
                'status' => 'success',
                'data' => [
                    'orders' => $orders,
                    'order_status_counts' => $orderStatusCounts,
                ],
            ],
            200,
        );
    }

    public function showReturnedOrders(Request $request, $userId)
    {
        // Find the user by ID
        $user = User::find($userId);

        // Check if the user exists
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
            ->where('order_status', 'Returned')
            ->get();

        // Check if there are no orders with 'Returned' status
        if ($orders->isEmpty()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'No returned orders found for this user.',
                ],
                404,
            );
        }

        // Count orders by status (if needed, you can also count only delivered orders here)
        $orderStatusCounts = $orders->groupBy('order_status')->map(function ($group) {
            return $group->count();
        });

        // Return the response
        return response()->json(
            [
                'status' => 'success',
                'data' => [
                    'orders' => $orders,
                    'order_status_counts' => $orderStatusCounts,
                ],
            ],
            200,
        );
    }

    public function showCanceledOrders(Request $request, $userId)
    {
        // Find the user by ID
        $user = User::find($userId);

        // Check if the user exists
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
            ->where('order_status', 'Canceled')
            ->get();

        // Check if there are no orders with 'Canceled' status
        if ($orders->isEmpty()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'No canceled orders found for this user.',
                ],
                404,
            );
        }

        // Count orders by status (if needed, you can also count only delivered orders here)
        $orderStatusCounts = $orders->groupBy('order_status')->map(function ($group) {
            return $group->count();
        });

        // Return the response
        return response()->json(
            [
                'status' => 'success',
                'data' => [
                    'orders' => $orders,
                    'order_status_counts' => $orderStatusCounts,
                ],
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
    public function update(Request $request, $orderId)
    {
        $request->validate([
            'order_status' => 'required|in:Processing,Delivered,Canceled,Returned',
        ]);

        // Find the order by ID
        $order = Orders::where('order_id', $orderId)->first();

        // Check if the order exists
        if (!$order) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Order not found.',
                ],
                404,
            );
        }

        // Update the order status
        $order->order_status = $request->order_status;
        $order->save();

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Order status updated successfully.',
                'data' => $order,
            ],
            200,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Orders $orders)
    {
        //
    }
}
