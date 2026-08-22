<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\Response as ScrambleResponse;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * Display a listing of the products.
     */
    #[Group('Products')]
    #[ScrambleResponse(200, description: 'List of products', type: 'array{data: array{0: array{id: int, name: string, description: string|null, price: float, stock_quantity: int, added_by: string, created_at: string|null, updated_at: string|null}}}')]
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
    #[Group('Products')]
    #[BodyParameter('name', description: 'Product name', type: 'string', example: 'Wireless Headphones')]
    #[BodyParameter('description', description: 'Product description', type: 'string', example: 'Noise cancelling wireless headphones with 30h battery')]
    #[BodyParameter('price', description: 'Product price', type: 'number', format: 'float', example: 149.99)]
    #[BodyParameter('stock_quantity', description: 'Available stock quantity', type: 'integer', example: 25)]
    #[ScrambleResponse(201, description: 'Product created successfully', type: 'array{data: array{id: int, name: string, description: string|null, price: float, stock_quantity: int, added_by: string, created_at: string|null, updated_at: string|null}}')]
    #[ScrambleResponse(401, description: 'Unauthenticated', type: 'array{message: string}')]
    #[ScrambleResponse(403, description: 'Only admin or salesman can create products', type: 'array{message: string}')]
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
    #[Group('Products')]
    #[PathParameter('product', description: 'The product ID', type: 'integer', example: 1)]
    #[ScrambleResponse(200, description: 'Product details', type: 'array{data: array{id: int, name: string, description: string|null, price: float, stock_quantity: int, added_by: string, created_at: string|null, updated_at: string|null}}')]
    #[ScrambleResponse(401, description: 'Unauthenticated', type: 'array{message: string}')]
    #[ScrambleResponse(404, description: 'Product not found', type: 'array{message: string}')]
    public function show(Product $product): JsonResponse
    {
        $product = $this->productService->getProductById(auth()->user(), $product->id);

        return (new ProductResource($product->load('user')))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Update the specified product in storage.
     */
    #[Group('Products')]
    #[PathParameter('product', description: 'The product ID', type: 'integer', example: 1)]
    #[BodyParameter('name', description: 'Updated product name', type: 'string', example: 'Wireless Headphones Pro')]
    #[BodyParameter('description', description: 'Updated product description', type: 'string', example: 'Updated version with active noise cancellation')]
    #[BodyParameter('price', description: 'Updated price', type: 'number', format: 'float', example: 179.99)]
    #[BodyParameter('stock_quantity', description: 'Updated stock count', type: 'integer', example: 40)]
    #[ScrambleResponse(200, description: 'Product updated successfully', type: 'array{data: array{id: int, name: string, description: string|null, price: float, stock_quantity: int, added_by: string, created_at: string|null, updated_at: string|null}}')]
    #[ScrambleResponse(401, description: 'Unauthenticated', type: 'array{message: string}')]
    #[ScrambleResponse(403, description: 'User is not allowed to update the product', type: 'array{message: string}')]
    #[ScrambleResponse(404, description: 'Product not found', type: 'array{message: string}')]
    public function update(StoreProductRequest $request, Product $product): JsonResponse
    {
        $updated = $this->productService->updateProduct(auth()->user(), $product, $request->validated());

        return (new ProductResource($updated))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified product from storage.
     */
    #[Group('Products')]
    #[PathParameter('product', description: 'The product ID', type: 'integer', example: 1)]
    #[ScrambleResponse(200, description: 'Product deleted successfully', type: 'array{message: string}')]
    #[ScrambleResponse(401, description: 'Unauthenticated', type: 'array{message: string}')]
    #[ScrambleResponse(403, description: 'User is not allowed to delete the product', type: 'array{message: string}')]
    #[ScrambleResponse(404, description: 'Product not found', type: 'array{message: string}')]
    public function destroy(Product $product): JsonResponse
    {
        $this->productService->deleteProduct(auth()->user(), $product);

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
