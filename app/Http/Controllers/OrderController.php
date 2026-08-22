<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\ChangeOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Dedoc\Scramble\Attributes\BodyParameter;
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
        $order = $this->orderService->placeOrder(auth()->user());

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
        $loadedOrder = $this->orderService->getOrder(auth()->user(), $order);


        return response()->json(['order' => new OrderResource($loadedOrder)], 200);
    }

    /**
     * Change the status of an order.
     */
    #[Group('Orders')]
    #[BodyParameter('order_id', description: 'The order ID', type: 'integer', example: 1)]
    #[BodyParameter('status', description: 'New order status. Allowed values: pending, processing, completed, cancelled', type: 'string', example: 'processing')]
    #[ScrambleResponse(200, description: 'Order status updated successfully', type: 'array{message: string, order: array{id: int, user_id: int, total_amount: float, status: string, items: array{0: array{product_id: int, quantity: int, price: float, product_name: string|null, product_description: string|null}}, created_at: string|null, updated_at: string|null}}')]
    #[ScrambleResponse(401, description: 'Unauthenticated', type: 'array{message: string}')]
    #[ScrambleResponse(403, description: 'Only admin or salesman can change order status', type: 'array{message: string}')]
    #[ScrambleResponse(404, description: 'Order not found', type: 'array{message: string}')]
    #[ScrambleResponse(422, description: 'Provided order status is invalid', type: 'array{message: string}')]
    public function changeStatus(ChangeOrderStatusRequest $request): JsonResponse
    {
        $data = $request->validated();
        $updated = $this->orderService->changeOrderStatus($data['order_id'], $data['status']);
        
        return response()->json([
            'message' => 'Order status updated successfully to '.$data['status'],
            'order' => new OrderResource($updated),
        ], 200);
    }
}
