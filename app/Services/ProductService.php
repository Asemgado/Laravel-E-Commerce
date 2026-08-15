<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Enums\UserRolesEnum;

class ProductService
{
    public function getProducts(User $user)
    {
        if ($user->role === UserRolesEnum::SALESMAN) {
            return Product::where('user_id', $user->id)->with('user')->get();
        }

        return Product::with('user')->get();
    }

    public function createProduct(User $user, array $data): Product
    {
        $data['user_id'] = $user->id;

        return Product::create($data);
    }

    public function getProductById(User $user, int $productId): Product
    {
        $product = $user->role === UserRolesEnum::SALESMAN
            ? Product::where('user_id', $user->id)->find($productId)
            : Product::find($productId);

        if (! $product) {
            throw new \InvalidArgumentException("Product with ID {$productId} not found.");
        }

        return $product;
    }

    public function canManageProduct(User $user, Product $product): bool
    {
        if ($user->role === UserRolesEnum::ADMIN) {
            return true;
        }

        else if ($user->role === UserRolesEnum::SALESMAN) {
            return $product->user_id === $user->id;
        }

        return false;
    }


    
    public function updateProduct(User $user, Product $product, array $data): Product
    {
        if (! $this->CanManageProduct($user, $product)) {
            throw new \InvalidArgumentException('You are not authorized to perform this action on this product.');
        }
        $product->update($data);

        return $product->fresh();
    }

    public function deleteProduct(User $user, Product $product): void
    {
        if (! $this->CanManageProduct($user, $product)) {
            throw new \InvalidArgumentException('You are not authorized to perform this action on this product.');
        }
        $product->delete();
    }
}