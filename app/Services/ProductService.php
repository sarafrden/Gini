<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ProductService
{
    /**
     * Retrieve a product by ID using caching.
     * Logs cache hits/misses and increments an access count in Redis.
     *
     * @param  int  $id
     * @return Product|null
     */
    public function getProduct(int $id): ?Product
    {
        $cacheKey = $this->cacheKey($id);

        if (Cache::has($cacheKey)) {
            Log::info("Cache hit for product {$id}");
            /** @var Product $product */
            $product = Cache::get($cacheKey);
        } else {
            Log::info("Cache miss for product {$id}");
            $product = Product::find($id);

            if ($product !== null) {
                // Cache the product for 60 minutes. Adjust the duration as needed.
                Cache::put($cacheKey, $product, now()->addMinutes(60));
            }
        }

        // Increment the product's access count in Redis.
        if ($product !== null) {
            // Use a Redis sorted set called 'product_access' where the score is the access count.
            Redis::zincrby('product_access', 1, (string)$id);
        }

        return $product;
    }

    /**
     * Generate a cache key for a product.
     */
    protected function cacheKey(int $id): string
    {
        return "product:{$id}";
    }
}
