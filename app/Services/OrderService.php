<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Enums\OrderStatus;

class OrderService
{
    public function placeOrder(User $user): Order
    {
        $cart = $user->cart()->with('items.product')->first();

        if (! $cart || $cart->items->isEmpty()) {
            throw new \InvalidArgumentException('Your cart is empty.');
        }

        return DB::transaction(function () use ($user, $cart) {
            $total = 0;

            foreach ($cart->items as $item) {
                $product = $item->product;

                if (! $product) {
                    throw new \InvalidArgumentException('One of the products no longer exists.');
                }

                if ($item->quantity > $product->stock_quantity) {
                    throw new \InvalidArgumentException("Not enough stock for product: {$product->name}");
                }

                $total += $item->price * $item->quantity;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $total,
                'status' => OrderStatus::PENDING->value
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);

                $product = $item->product;

                $product->decrement('stock_quantity', $item->quantity);
            }

            $cart->items()->delete();

            return $order->fresh()->load('items.product');
        });
    }

    public function getUserOrders(User $user)
    {
        return $user->orders()->with('items.product')->latest()->get();
    }

    public function getOrder(Order $order): Order
    {
        return $order->load('items.product');
    }
    public function ChangeOrderStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);
        return $order->fresh()->load('items.product');
    }
}