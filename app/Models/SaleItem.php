<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SaleItem extends Pivot
{
    use HasFactory;

    protected $table = 'sale_items';

    protected $fillable = [
        'quantity', 'price',
    ];

    public $timestamps = false;

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalAttribute(): float
    {
        return $this->quantity * $this->price;
    }

}
