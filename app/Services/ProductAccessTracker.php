<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class ProductAccessTracker
{
    public function track($productId)
    {
        $key = 'product_accesses:' . now()->format('Y-m-d:H');
        Redis::zincrby($key, 1, $productId);
    }
}
