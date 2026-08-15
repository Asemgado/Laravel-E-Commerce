<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Http\Resources\CartsResource;
use App\Http\Requests\CartRequest;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ){}
    /**
     * Display User Cart.
     */
    public function index()
    {
        $cart = $this->cartService->getCart(auth()->user());

        return new CartsResource($cart);
    }

    /**
     * Add item to the cart.
     */
    public function addToCart(CartRequest $request)
    {
        $productId = $request->validated()['product_id'];
        $quantity = $request->validated()['quantity'];

        try {
            $cart = $this->cartService->addItem(auth()->user(), $productId, $quantity);
            return response()->json(['message' => 'Product added to cart successfully', 'cart' => new CartsResource($cart)], 201);
            
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Update Product quantity in the cart.
     */
    public function update(CartRequest $request)
    {
        $productId = $request->validate()['product_id'];
        $quantity = $request->validate()['quantity'];
        try {
            $cart = $this->cartService->updateCart(auth()->user(), $productId, $quantity);
            return response()->json(['message' => 'Cart updated successfully', 'cart' => new CartsResource($cart)], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Remove item from the cart.
     */
    public function removeItem(int $productId)
    {
        try {
            $this->cartService->removeItem(auth()->user(), $productId);
            return response()->json(['message' => 'Item removed from cart successfully'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
    /**
     * Clear the cart.
     */
    public function clear()
    {
        try {
            $this->cartService->clearCart(auth()->user());
            return response()->json(['message' => 'Cart cleared successfully'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
