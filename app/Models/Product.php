<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'product_name',
        'product_price',
        'product_stock',
        'product_category',
        'product_image',
    ];

    protected $casts = [
        'product_price' => 'decimal:2',
        'product_stock' => 'integer',
        'admin_id' => 'integer',
    ];
}
