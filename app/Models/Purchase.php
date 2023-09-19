<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'supplier_id',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'purchase_items')
            ->using(PurchaseItem::class)
            ->withTimestamps()
            ->withPivot('quantity', 'price')
            ->wherePivotNull('deleted_at');
    }

    public function getTotalAttribute(): Float
    {
        return array_reduce($this->items?->toArray() ?? [], function ($total, $item) {
            return ($total ?? 0) + ($item['pivot']['quantity'] * $item['pivot']['price']);
        });
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        $query->select('purchases.*')
            ->join('users as scope_search__users', 'scope_search__users.id', 'purchases.user_id')
            ->join('suppliers as scope_search__suppliers', 'scope_search__suppliers.id', 'purchases.supplier_id')
            ->join('purchase_items as scope_search__purchase_items', 'scope_search__purchase_items.purchase_id', 'purchases.id')
            ->join('products as scope_search__products', 'scope_search__products.id', 'scope_search__purchase_items.product_id')
            ->orWhere('scope_search__users.name', 'like', "%{$keyword}%")
            ->orWhere('scope_search__suppliers.name', 'like', "%{$keyword}%")
            ->orWhere('scope_search__products.name', 'like', "%{$keyword}%");
    }

    public function scopeOfUser(Builder $query, string $user_id)
    {
        $query->where('user_id', $user_id);
    }

    public function scopeOfSupplier(Builder $query, string $supplier_id)
    {
        $query->where('supplier_id', $supplier_id);
    }

    public function scopeOfDate(Builder $query, string $date_start, string $date_end)
    {
        $query->where(function ($query) use ($date_start, $date_end) {
            $query->whereRaw("DATE(created_at) >= '{$date_start}'");
            $query->whereRaw("DATE(created_at) <= '{$date_end}'");
        });
    }

    public function scopeOfProduct(Builder $query, string $product_id)
    {
        $query->select('purchases.*')
            ->join('purchase_items as scope_product__purchase_items', 'scope_product__purchase_items.purchase_id', '=', 'purchases.id')
            ->where('scope_product__purchase_items.product_id', $product_id);
    }

    public function scopeOfCategory(Builder $query, string $category_id)
    {
        $query->select('purchases.*')
            ->join('purchase_items as scope_category__purchase_items', 'scope_category__purchase_items.purchase_id', '=', 'purchases.id')
            ->join('products as scope_category__products', 'scope_category__products.id', '=', 'purchase_items.product_id')
            ->where('scope_category__products.category_id', $category_id);
    }
}
