<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\Response as ScrambleResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * List all orders for the user.
     */
    #[Group('Orders')]
    #[ScrambleResponse(200, description: 'List of orders', type: 'array{orders: array{0: array{id: int, user_id: int, total_amount: float, status: string, items: array{0: array{product_id: int, quantity: int, price: float, product_name: string|null, product_description: string|null}}, created_at: string|null, updated_at: string|null}}}')]
    #[ScrambleResponse(401, description: 'Unauthenticated', type: 'array{message: string}')]
    public function index()
    {
        $orders = $this->orderService->getOrders(auth()->user());

        return response()->json(['orders' => OrderResource::collection($orders)], 200);
    }

    /**
     * Place a new order.
     */
    #[Group('Orders')]
    #[ScrambleResponse(201, description: 'Order placed successfully', type: 'array{message: string, order: array{id: int, user_id: int, total_amount: float, status: string, items: array{0: array{product_id: int, quantity: int, price: float, product_name: string|null, product_description: string|null}}, created_at: string|null, updated_at: string|null}}')]
    #[ScrambleResponse(401, description: 'Unauthenticated', type: 'array{message: string}')]
    #[ScrambleResponse(422, description: 'Cannot place order because cart is empty or stock is insufficient', type: 'array{message: string}')]
    public function placeOrder(): JsonResponse
    {
        try {
            $order = $this->orderService->placeOrder(auth()->user());
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Order placed successfully', 'order' => new OrderResource($order)], 201);
    }

    /**
     * Display the specified order.
     */
    #[Group('Orders')]
    #[PathParameter('order', description: 'The order ID', type: 'integer', example: 1)]
    #[ScrambleResponse(200, description: 'Order details', type: 'array{order: array{id: int, user_id: int, total_amount: float, status: string, items: array{0: array{product_id: int, quantity: int, price: float, product_name: string|null, product_description: string|null}}, created_at: string|null, updated_at: string|null}}')]
    #[ScrambleResponse(401, description: 'Unauthenticated', type: 'array{message: string}')]
    #[ScrambleResponse(403, description: 'User is not allowed to access this order', type: 'array{message: string}')]
    #[ScrambleResponse(404, description: 'Order not found', type: 'array{message: string}')]
    public function show(Order $order): JsonResponse
    {
        try {
            $loadedOrder = $this->orderService->getOrder(auth()->user(), $order);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['order' => new OrderResource($loadedOrder)], 200);
    }

    /**
     * Change the status of an order.
     */
    #[Group('Orders')]
    #[PathParameter('order', description: 'The order ID', type: 'integer', example: 1)]
    #[PathParameter('status', description: 'New order status. Allowed values: pending, processing, completed, cancelled', type: 'string', example: 'processing')]
    #[ScrambleResponse(200, description: 'Order status updated successfully', type: 'array{message: string, order: array{id: int, user_id: int, total_amount: float, status: string, items: array{0: array{product_id: int, quantity: int, price: float, product_name: string|null, product_description: string|null}}, created_at: string|null, updated_at: string|null}}')]
    #[ScrambleResponse(401, description: 'Unauthenticated', type: 'array{message: string}')]
    #[ScrambleResponse(403, description: 'Only admin or salesman can change order status', type: 'array{message: string}')]
    #[ScrambleResponse(404, description: 'Order not found', type: 'array{message: string}')]
    #[ScrambleResponse(422, description: 'Provided order status is invalid', type: 'array{message: string}')]
    public function changeStatus(Order $order, string $status): JsonResponse
    {
        if (! in_array($status, array_column(OrderStatus::cases(), 'value'), true)) {
            return response()->json(['message' => 'Invalid order status provided.'], 422);
        }

        try {
            $updated = $this->orderService->changeOrderStatus($order, $status);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Order status updated successfully to '.$status,
            'order' => new OrderResource($updated),
        ], 200);
    }
}
