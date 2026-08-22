<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use InvalidArgumentException;

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
            throw new InvalidArgumentException('Not enough stock available.');
        }

        $cart = $this->getCart($user);

        $item = $cart->items()->where('product_id', $productId)->first();

        $newQuantity = ($item?->quantity ?? 0) + $quantity;

         if ($newQuantity > $product->stock_quantity) {
            throw new InvalidArgumentException('Not enough stock available for the updated quantity.');
        }

        $cart->items()->updateOrCreate(
            [
                'product_id' => $productId,
            ],
            [
                'quantity' => $newQuantity,
                'price' => $product->price,
            ]
        );

        return $cart->fresh()->load('items.product');
    }

    public function updateItem(User $user, int $productId, int $quantity): Cart
    {
        $product = Product::findOrFail($productId);

        if ($quantity > $product->stock_quantity) {
            throw new InvalidArgumentException('Not enough stock available.');
        }

        $cart = $this->getCart($user);

        $item = $cart->items()->where('product_id', $productId)->firstOrFail();

        $item->update([
            'quantity' => $quantity,
            'price' => $product->price,
        ]);

        return $cart->fresh()->load('items.product');
    }

    public function removeItem(User $user, CartItem $item): void
    {
        $cart = $this->getCart($user);

        $cart->items()
            ->where('id', $item->id)
            ->delete();
    }

    public function clearCart(User $user): void
    {
        $cart = $this->getCart($user);

        $cart->items()->delete();
    }

}