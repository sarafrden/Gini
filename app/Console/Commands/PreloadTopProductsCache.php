<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class PreloadTopProductsCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:preload-top-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Preload the cache with the top 50 most accessed products in the last 24 hours';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Retrieve the top 50 product IDs from Redis, ordered by descending access count.
        $topProductIds = Redis::zrevrange('product_access', 0, 49);

        if (empty($topProductIds)) {
            Log::info('No product access data available for caching.');
            return self::SUCCESS;
        }

        // Retrieve the product records from the database.
        $products = Product::whereIn('id', $topProductIds)->get();

        // Cache the collection for 24 hours under the key 'top_products'.
        Cache::put('top_products', $products, now()->addHours(24));

        Log::info('Preloaded top 50 products into cache.');

        // Optionally, reset the Redis sorted set to start fresh for the next 24 hours.
        Redis::del('product_access');
        Log::info('Reset product access counts in Redis.');

        return self::SUCCESS;
    }
}
