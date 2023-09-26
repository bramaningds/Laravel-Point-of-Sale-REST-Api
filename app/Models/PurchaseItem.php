<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PurchaseItem extends Pivot
{
    use HasFactory;

    protected $table = 'purchase_items';

    protected $fillable = [
        'quantity', 'price', 'deleted_at',
    ];

    public $timestamps = false;

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
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
