<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'customer_id',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'sale_items')
            ->using(SaleItem::class)
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
        $query->select('sales.*')
            ->join('users as scope_search__users', 'scope_search__users.id', 'sales.user_id')
            ->join('customers as scope_search__customers', 'scope_search__customers.id', 'sales.customer_id')
            ->join('sale_items as scope_search__sale_items', 'scope_search__sale_items.sale_id', 'sales.id')
            ->join('products as scope_search__products', 'scope_search__products.id', 'scope_search__sale_items.product_id')
            ->orWhere('scope_search__users.name', 'like', "%{$keyword}%")
            ->orWhere('scope_search__customers.name', 'like', "%{$keyword}%")
            ->orWhere('scope_search__products.name', 'like', "%{$keyword}%");
    }

    public function scopeOfUser(Builder $query, string $user_id)
    {
        $query->where('user_id', $user_id);
    }

    public function scopeOfCustomer(Builder $query, string $customer_id)
    {
        $query->where('customer_id', $customer_id);
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
        $query->select('sales.*')
            ->join('sale_items as scope_product__sale_items', 'scope_product__sale_items.sale_id', '=', 'sales.id')
            ->where('scope_product__sale_items.product_id', $product_id);
    }

    public function scopeOfCategory(Builder $query, string $category_id)
    {
        $query->select('sales.*')
            ->join('sale_items as scope_category__sale_items', 'scope_category__sale_items.sale_id', '=', 'sales.id')
            ->join('products as scope_category__products', 'scope_category__products.id', '=', 'sale_items.product_id')
            ->where('scope_category__products.category_id', $category_id);
    }
}
