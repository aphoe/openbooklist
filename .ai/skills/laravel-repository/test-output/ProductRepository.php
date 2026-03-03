<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Managers\ModelManager;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

final class ProductRepository
{
    // — Read methods ————————————————————————————————————————

    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::where('slug', $slug)->first();
    }

    public function findBySku(string $sku): ?Product
    {
        return Product::where('sku', $sku)->first();
    }

    public function getAll(): Collection
    {
        return Product::all();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Product::paginate($perPage);
    }

    public function getByCategory(int $categoryId): Collection
    {
        return Product::where('category_id', $categoryId)->get();
    }

    // — Write methods ———————————————————————————————————————

    public function create(
        string $name,
        string $description,
        float $price,
        int $stock,
        string $status,
        array $attributes,
        int $categoryId,
    ): Product {
        $product = new Product();

        $product->name        = $name;
        $product->slug        = (new ModelManager)->generateUniqueSlug($name, Product::class);
        $product->sku         = (new ModelManager)->generateUniqueIdentifier(Product::class);
        $product->description = $description;
        $product->price       = $price;
        $product->stock       = $stock;
        $product->status      = $status;
        $product->attributes  = $attributes;
        $product->is_active   = false;
        $product->category_id = $categoryId;

        $product->save();

        return $product;
    }

    public function update(
        Product $product,
        string $name,
        string $description,
        float $price,
        int $stock,
        string $status,
        array $attributes,
        int $categoryId,
    ): Product {
        $product->name        = $name;
        $product->slug        = (new ModelManager)->generateUniqueSlug($name, Product::class, $product->id);
        $product->description = $description;
        $product->price       = $price;
        $product->stock       = $stock;
        $product->status      = $status;
        $product->attributes  = $attributes;
        $product->category_id = $categoryId;

        $product->save();

        return $product;
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }

    // — Single-field state methods ——————————————————————————

    public function toggleActive(Product $product): void
    {
        $product->is_active = ! $product->is_active;
        $product->save();
    }

    public function setStatus(Product $product, string $status): void
    {
        $product->status = $status;
        $product->save();
    }
}
