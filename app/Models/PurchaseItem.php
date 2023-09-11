<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PurchaseItem extends Pivot
{
    use HasFactory;

    protected $table = 'purchase_items';

    protected $fillable = [
        'product_id', 'quantity', 'price',
    ];

    public $timestamps = false;

    public function getTotalAttribute(): float
    {
        return $this->quantity * $this->price;
    }

}
