<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Http\Resources\CartsResource;
use App\Http\Requests\CartRequest;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

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
    public function addToCart(CartRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $cart = $this->cartService->addItem(auth()->user(), $data['product_id'], $data['quantity']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Product added to cart successfully',
            'cart' => new CartsResource($cart),
        ], 201);
    }

    /**
     * Update Product quantity in the cart.
     */
    public function update(CartRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $cart = $this->cartService->updateItem(auth()->user(), $data['product_id'], $data['quantity']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Item not found in cart.'], 404);
        }

        return response()->json([
            'message' => 'Cart updated successfully',
            'cart' => new CartsResource($cart),
        ], 200);
    }

    /**
     * Remove item from the cart.
     */
     public function removeItem(int $productId): JsonResponse
    {
        $this->cartService->removeItem(auth()->user(), $productId);

        return response()->json(['message' => 'Item removed from cart successfully'], 200);
    }
    /**
     * Clear the cart.
     */
     public function clear(): JsonResponse
    {
        $this->cartService->clearCart(auth()->user());

        return response()->json(['message' => 'Cart cleared successfully'], 200);
    }
}
