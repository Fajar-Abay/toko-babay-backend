<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

// app/Http/Controllers/OrderController.php
class OrderController extends Controller
{
    // Lihat daftar pesanan user
    public function listOrders()
    {
        $orders = auth()->user()
            ->orders()
            ->with(['orderItems.product'])
            ->latest()
            ->get();

        return response()->json($orders);
    }


    // Update status pesanan (admin atau sistem)
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $status = $request->input('status');
        $allowed = ['pending', 'paid', 'canceled', 'done'];

        if (!in_array($status, $allowed)) {
            return response()->json(['message' => 'Invalid status'], 400);
        }

        $order->status = $status;
        $order->save();

        return response()->json(['message' => 'Order status updated']);
    }
}
