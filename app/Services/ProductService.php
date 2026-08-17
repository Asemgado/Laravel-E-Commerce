<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Enums\UserRolesEnum;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function getProducts(User $user): Collection
    {
        $query = Product::query()->with('user');

        if ($user->role === UserRolesEnum::SALESMAN) {
            $query->where('user_id', $user->id);
        }

        return $query->get();
    }

    public function createProduct(User $user, array $data): Product
    {
        $data['user_id'] = $user->id;

        return Product::create($data);
    }

    /**
     * Admin & customer: any product by ID.
     * Salesman: only their own product by ID (404 otherwise).
     */
    public function getProductById(User $user, int $productId): Product
    {
        $query = Product::query();

        if ($user->role === UserRolesEnum::SALESMAN) {
            $query->where('user_id', $user->id);
        }

        $product = $query->find($productId);

        if (! $product) {
            throw new ModelNotFoundException("Product with ID {$productId} not found.");
        }

        return $product;
    }

    public function canManageProduct(User $user, Product $product): bool
    {
        if ($user->role === UserRolesEnum::ADMIN) {
            return true;
        }

        if ($user->role === UserRolesEnum::SALESMAN) {
            return $product->user_id === $user->id;
        }

        return false;
    }

    public function updateProduct(User $user, Product $product, array $data): Product
    {
        if (! $this->canManageProduct($user, $product)) {
            throw new AuthorizationException('You are not authorized to perform this action on this product.');
        }

        $product->update($data);

        return $product->fresh();
    }

    public function deleteProduct(User $user, Product $product): void
    {
        if (! $this->canManageProduct($user, $product)) {
            throw new AuthorizationException('You are not authorized to perform this action on this product.');
        }

        $product->delete();
    }
}