<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SaleItem extends Pivot
{
    use HasFactory;

    protected $table = 'sale_items';

    protected $fillable = [
        'sale_id', 'product_id', 'quantity', 'price',
    ];

    public $timestamps = true;

    public function getTotalAttribute(): float
    {
        return $this->quantity * $this->price;
    }

}
