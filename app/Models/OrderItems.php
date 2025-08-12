<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    protected $fillable = [
        "order_id",
        "product_id",
        "quantity",
        "price"
    ];

    // BENAR ✅
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
