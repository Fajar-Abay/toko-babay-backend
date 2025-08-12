<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function viewCart()
    {
        $cart = auth()->user()->cart()->with('items.product')->first();

        return response()->json($cart);
    }

    public function addToCart(Request $request)
    {
        $user = auth()->user();
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $cart = $user->cart ?? Cart::create(['user_id' => $user->id]);

        $item = $cart->items()->where('product_id', $productId)->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->save();
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return response()->json(['message' => 'Item added to cart']);
    }

    public function checkout()
    {
        $user = auth()->user();
        $cart = $user->cart()->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $total = 0;
        foreach ($cart->items as $item) {
            $total += $item->quantity * $item->product->price;
        }

        $order = $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $total,
            'status' => 'pending',
        ]);


        foreach ($cart->items as $item) {
            $order->orderItems()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        $cart->update(['is_checked_out' => true]);

        return response()->json(['message' => 'Checkout successful']);
    }

    public function removeFromCart(Request $request)
    {
        $user = auth()->user();
        $productId = $request->input('product_id');

        // Ambil keranjang aktif
        $cart = $user->cart;

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        // Cari item di keranjang
        $item = $cart->items()->where('product_id', $productId)->first();

        if (!$item) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        // Hapus item
        $item->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }


}
