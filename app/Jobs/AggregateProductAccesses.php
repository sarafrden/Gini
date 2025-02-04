<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use App\Models\Product;

class AggregateProductAccesses implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $keys = [];
        for ($i = 0; $i < 24; $i++) {
            $keys[] = 'product_accesses:' . now()->subHours($i)->format('Y-m-d:H');
        }

        $productAccesses = [];
        foreach ($keys as $key) {
            $data = Redis::zrange($key, 0, -1, 'WITHSCORES');
            foreach ($data as $productId => $score) {
                if (!isset($productAccesses[$productId])) {
                    $productAccesses[$productId] = 0;
                }
                $productAccesses[$productId] += $score;
            }
        }

        // Update the database
        foreach ($productAccesses as $productId => $accessCount) {
            Product::where('id', $productId)->increment('access_count', $accessCount);
        }

        // Clear the Redis keys for the oldest hour
        Redis::del($keys[23]);
    }
}
