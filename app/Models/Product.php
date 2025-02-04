<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price'];


    protected static function booted()
    {
        // Clear cache when a product is created, updated, or deleted
        static::saved(function () {
            Cache::forget('top_products');
        });

        static::deleted(function () {
            Cache::forget('top_products');
        });
    }
}
