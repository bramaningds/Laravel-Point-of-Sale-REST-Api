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
        $query->orWhereRelation('user', fn($query) => $query->where('name', 'like', "%{$keyword}%"))
            ->orWhereRelation('customer', fn($query) => $query->where('name', 'like', "%{$keyword}%"))
            ->orWhereRelation('items', fn($query) => $query->where('name', 'like', "%{$keyword}%"));
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
        $query->whereRelation('product', 'product_id', $product_id);
    }

    public function scopeOfCategory(Builder $query, string $category_id)
    {
        $query->whereRelation('product', 'category_id', $category_id);
    }
}
