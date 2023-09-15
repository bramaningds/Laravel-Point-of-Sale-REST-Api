<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleItem extends Pivot
{
    use HasFactory, SoftDeletes;

    protected $table = 'sale_items';

    protected $fillable = [
        'sale_id', 'product_id', 'quantity', 'price', 'deleted_at',
    ];

    public $timestamps = true;

    public function getTotalAttribute(): float
    {
        return $this->quantity * $this->price;
    }

}
