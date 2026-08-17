<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;
use App\Services\ProductService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {
    }

    /**
     * Display a listing of the products.
     */
    public function index(): JsonResponse
    {
        $products = $this->productService->getProducts(auth()->user());

        return ProductResource::collection($products)
            ->response()
            ->setStatusCode(200);
    }


    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct(auth()->user(), $request->validated());

        return (new ProductResource($product->load('user')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): JsonResponse
    {
        try {
            $product = $this->productService->getProductById(auth()->user(), $product->id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        return (new ProductResource($product->load('user')))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(StoreProductRequest $request, Product $product): JsonResponse
    {
        try {
            $updated = $this->productService->updateProduct(auth()->user(), $product, $request->validated());
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return (new ProductResource($updated))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified product from storage.
     */
     public function destroy(Product $product): JsonResponse
    {
        try {
            $this->productService->deleteProduct(auth()->user(), $product);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}