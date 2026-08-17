<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Http\Resources\OrderResource;
use App\Enums\OrderStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    )
    {
    }
    /**
     * List all orders for the user.
     */
    public function index()
    {
        $orders = $this->orderService->getOrders(auth()->user());
        return response()->json(['orders' => OrderResource::collection($orders)], 200);
    }

    /**
     * Place a new order.
     */
    public function placeOrder(): JsonResponse
    {
        try {
            $order = $this->orderService->placeOrder(auth()->user());
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Order placed successfully','order' => new OrderResource($order),], 201);
    }

    /**
     * Display the specified order.
     */
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
            'message' => 'Order status updated successfully to ' . $status,
            'order' => new OrderResource($updated),
        ], 200);
    }
}