<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user-> role->value === 'salesman') {
            // If the user is a salesman, return only the products they added
            return ProductResource::collection(Product::where('user_id', $user->id)->get());
        }
        // If the user is an admin or customer, return all products
        return ProductResource::collection(Product::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id(); // Assign the authenticated user's ID to the product

        $product = Product::create($validated);

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $user = auth()->user();

        // Salesmen can only view their own products
        if ($user->role->value === 'salesman' && $product->user_id !== $user->id) {
            return response()->json(['message' => 'you are not authorized to view this product'], 403);
        }

        return new ProductResource($product->load('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, Product $product)
    {
        $user = auth()->user();
        
        // Only the owner or admin can update
        if ($user->role->value === 'salesman' && $product->user_id !== $user->id) {
            return response()->json(['message' => 'you are not authorized to update this product'], 403);
        }
        
        $product->update($request->validated());

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $user = auth()->user();
        
        // Only the owner or admin can delete
        if ($user->role->value === 'salesman' && $product->user_id !== $user->id) {
            return response()->json(['message' => 'you are not authorized to delete this product'], 403);
        }
        
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 204);
    }
}
