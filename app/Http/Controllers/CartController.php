<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Http\Resources\CartsResource;
use App\Models\Product;
use App\Services\CartService;
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\Response as ScrambleResponse;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Display User Cart.
     */
    #[Group('Cart')]
    #[ScrambleResponse(200, description: 'Current cart contents', type: 'array{data: array{id: int, user_id: int, items: array{0: array{id: int, product_id: int, product_name: string|null, quantity: int, price: float, line_total: float}}, totals: array{items_count: int, subtotal: float}, created_at: string|null, updated_at: string|null}}')]
    #[ScrambleResponse(401, description: 'Unauthenticated', type: 'array{message: string}')]
    public function index()
    {
        $cart = $this->cartService->getCart(auth()->user());

        return new CartsResource($cart);
    }

    /**
     * Add item to the cart.
     */
    #[Group('Cart')]
    #[BodyParameter('product_id', description: 'Product ID to add to the cart', type: 'integer', example: 1)]
    #[BodyParameter('quantity', description: 'Quantity to add', type: 'integer', example: 2)]
    #[ScrambleResponse(201, description: 'Product added to cart', type: 'array{message: string, cart: array{id: int, user_id: int, items: array{0: array{id: int, product_id: int, product_name: string|null, quantity: int, price: float, line_total: float}}, totals: array{items_count: int, subtotal: float}, created_at: string|null, updated_at: string|null}}')]
    #[ScrambleResponse(401, description: 'Unauthenticated', type: 'array{message: string}')]
    #[ScrambleResponse(422, description: 'Invalid cart request or insufficient stock', type: 'array{message: string}')]
    public function addToCart(CartRequest $request): JsonResponse
    {
        $data = $request->validated();

        $cart = $this->cartService->addItem(auth()->user(), $data['product_id'], $data['quantity']);

        return response()->json([
            'message' => 'Product added to cart successfully',
            'cart' => new CartsResource($cart),
        ], 201);
    }

    /**
     * Update Product quantity in the cart.
     */
    #[Group('Cart')]
    #[BodyParameter('product_id', description: 'Product ID to update in the cart', type: 'integer', example: 1)]
    #[BodyParameter('quantity', description: 'New quantity value', type: 'integer', example: 3)]
    #[ScrambleResponse(200, description: 'Cart updated successfully', type: 'array{message: string, cart: array{id: int, user_id: int, items: array{0: array{id: int, product_id: int, product_name: string|null, quantity: int, price: float, line_total: float}}, totals: array{items_count: int, subtotal: float}, created_at: string|null, updated_at: string|null}}')]
    #[ScrambleResponse(401, description: 'Unauthenticated', type: 'array{message: string}')]
    #[ScrambleResponse(404, description: 'Cart item not found', type: 'array{message: string}')]
    #[ScrambleResponse(422, description: 'Invalid cart request or insufficient stock', type: 'array{message: string}')]
    public function update(CartRequest $request): JsonResponse
    {
        $data = $request->validated();

        $cart = $this->cartService->updateItem(auth()->user(), $data['product_id'], $data['quantity']);

        return response()->json([
            'message' => 'Cart updated successfully',
            'cart' => new CartsResource($cart),
        ], 200);
    }

    /**
     * Remove item from the cart.
     */
    #[Group('Cart')]
    #[PathParameter('product', description: 'Product ID to remove from the cart', type: 'integer', example: 1)]
    #[ScrambleResponse(200, description: 'Cart item removed successfully', type: 'array{message: string}')]
    #[ScrambleResponse(401, description: 'Unauthenticated', type: 'array{message: string}')]
    public function removeItem(Product $product): JsonResponse
    {
        $this->cartService->removeItem(auth()->user(), $product->id);

        return response()->json(['message' => 'Item removed from cart successfully'], 200);
    }

    /**
     * Clear the cart.
     */
    #[Group('Cart')]
    #[ScrambleResponse(200, description: 'Cart cleared successfully', type: 'array{message: string}')]
    #[ScrambleResponse(401, description: 'Unauthenticated', type: 'array{message: string}')]
    public function clear(): JsonResponse
    {
        $this->cartService->clearCart(auth()->user());

        return response()->json(['message' => 'Cart cleared successfully'], 200);
    }
}
