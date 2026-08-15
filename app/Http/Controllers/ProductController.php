<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;
use App\Services\ProductService;
use App\Exceptions\UnauthorizedProductActionException;
use Illuminate\Http\JsonResponse;
use Throwable;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {
    }

    /**
     * Display a listing of the products.
     */
    public function index()
    {
        try {
            $products = $this->productService->getProducts(auth()->user());
            return ProductResource::collection($products);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->createProduct(auth()->user(), $request->validated());

            return (new ProductResource($product->load('user')))
                ->response()
                ->setStatusCode(201);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        try {
            $product = $this->productService->getProductById(auth()->user(), $product->id);

            return new ProductResource($product->load('user'));
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Update the specified product in storage.
     */
    public function update(StoreProductRequest $request, Product $product)
    {
        try {
            $updated = $this->productService->updateProduct(auth()->user(), $product, $request->validated());

            return new ProductResource($updated);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        try {
            $this->productService->deleteProduct(auth()->user(), $product);

            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}