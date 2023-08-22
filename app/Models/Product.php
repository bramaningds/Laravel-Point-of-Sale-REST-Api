<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'price', 'stock', 'sellable', 'purchasable'
    ];

    protected $casts = [
        'stock' => 'float',
        'price' => 'float',
    ];

    public function scopeSearch(Builder $query, string $keyword) {
        $query->orWhere('name', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
    }

}
