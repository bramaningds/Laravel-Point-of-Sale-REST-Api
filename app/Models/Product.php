<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'price', 'stock', 'sellable', 'purchasable',
    ];

    protected $casts = [
        'stock' => 'float',
        'price' => 'float',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isStockable(): bool
    {
        return $this->stockable == 'Y';
    }

    public function isNotStockable(): bool
    {
        return !$this->isStockable();
    }

    public function hasSufficientStock($required = 0): bool
    {
        return $this->stock > floatval($required);
    }

    public function hasInsufficientStock($required = 0): bool
    {
        return !$this->hasSufficientStock($required);
    }

    public function isSellable(): bool
    {
        return $this->sellable == 'Y';
    }

    public function isNotSellable(): bool
    {
        return !$this->isSellable();
    }

    public function isPurchasable(): bool
    {
        return $this->purchasable == 'Y';
    }

    public function isNotPurchasable(): bool
    {
        return !$this->isPurchasable();
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        $query->orWhere('name', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%");
    }

    public function scopeOfCategory(Builder $query, string $category_id)
    {
        $query->where('category_id', $category_id);
    }

}
