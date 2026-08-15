<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Http\Resources\OrderResource;
use App\Enums\OrderStatus;

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
        $orders = $this->orderService->getUserOrders(auth()->user());
        return response()->json(['orders' => OrderResource::collection($orders)]);
    }

    /**
     * Place a new order.
     */
    public function placeOrder()
    {
        try {
            $order = $this->orderService->placeOrder(auth()->user());
            return response()->json(['message' => 'Order placed successfully', 'order' => $order], 201);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
        
    }

    /**
     * Display the specified order.
     */
    public function show(int $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            if ($order->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                throw new \Illuminate\Auth\Access\AuthorizationException('You do not own this order.');
            }

            $loadedOrder = $this->orderService->getOrder($order);
            return response()->json(['order' => new OrderResource($loadedOrder)]);
        
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage(),], 403);
        }
    }

    /**
     * Change the status of an order.
     */
    public function changeStatus(int $orderId, string $status)
    {
        try {

            if (!in_array($status, array_column(OrderStatus::cases(), 'value'))) {
                throw new \InvalidArgumentException('Invalid order status provided.');
            }
            $order = Order::findOrFail($orderId);
            $loadedOrder = $this->orderService->ChangeOrderStatus($order, $status);
            return response()->json(['message' => 'Order status updated successfully to ' . $status, 'order' => new OrderResource($loadedOrder)]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}