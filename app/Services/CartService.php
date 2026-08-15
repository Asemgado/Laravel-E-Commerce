<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getCart(User $user): Cart
    {
        return $user->cart()->with('items.product')->firstOrCreate([
            'user_id' => $user->id,
        ]);
    }

    public function addItem(User $user, int $productId, int $quantity): Cart
    {
        $product = Product::findOrFail($productId);

        if ($quantity > $product->stock_quantity) {
            throw new \InvalidArgumentException('Not enough stock available.');
        }

        $cart = $this->getCart($user);

        $item = $cart->items()->where('product_id', $productId)->first();

        if ($item) {
            $newQuantity = $item->quantity + $quantity;

            if ($newQuantity > $product->stock_quantity) {
                throw new \InvalidArgumentException('Not enough stock available for the updated quantity.');
            }

            $item->update([
                'quantity' => $newQuantity,
                'price' => $product->price,
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
        }

        return $cart->fresh()->load('items.product');
    }

    public function updateItem(User $user, int $productId, int $quantity): Cart
    {
        $product = Product::findOrFail($productId);

        if ($quantity > $product->stock_quantity) {
            throw new \InvalidArgumentException('Not enough stock available.');
        }

        $cart = $this->getCart($user);

        $item = $cart->items()->where('product_id', $productId)->firstOrFail();

        $item->update([
            'quantity' => $quantity,
            'price' => $product->price,
        ]);

        return $cart->fresh()->load('items.product');
    }

    public function removeItem(User $user, int $productId): void
    {
        $cart = $this->getCart($user);

        $cart->items()
            ->where('product_id', $productId)
            ->delete();
    }

    public function clearCart(User $user): void
    {
        $cart = $this->getCart($user);

        $cart->items()->delete();
    }

    public function getItems(User $user)
    {
        return $this->getCart($user)->items()->with('product')->get();
    }
}