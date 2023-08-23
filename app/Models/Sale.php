<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'customer_id'
    ];

    protected $with = [
        // 'user', 'customer', 'items'
    ];

    public function customer() : BelongsTo {
        return $this->belongsTo(Customer::class);
    }

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function items() : BelongsToMany {
        return $this->belongsToMany(Product::class, 'sale_items')
                    ->using(SaleItem::class)
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    public function getTotalAttribute() : Float {
        $items = $this->items->toArray() ?: [];

        $subtotal = array_reduce($items, function($carry, $item) {
            return $carry + ($item['pivot']['quantity'] * $item['pivot']['price']);
        }, 0);

        $total = $subtotal;

        return $total;
    }

    public function scopeSearch(Builder $query, string $keyword) {
        $query->whereIn('id', Sale::query()->selectRaw('DISTINCT(sales.id) as id')
              ->join('users', 'users.id', '=', 'user_id')
              ->join('customers', 'customers.id', '=', 'customer_id')
              ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
              ->join('products', 'products.id', '=', 'sale_items.product_id')
              ->whereRaw('FALSE')
              ->orWhere('users.name', 'like', "%{$keyword}%")
              ->orWhere('customers.name', 'like', "%{$keyword}%")
              ->orWhere('products.name', 'like', "%{$keyword}%")
              ->pluck('id'));
    }

    public function scopeOfUser(Builder $query, string $user_id) {
        $query->where('user_id', $user_id);
    }

    public function scopeOfCustomer(Builder $query, string $customer_id) {
        $query->where('customer_id', $customer_id);
    }

    public function scopeOfDate(Builder $query, string $date_start, string $date_end) {
        $query->where(function($query) use ($date_start, $date_end) {
            $query->whereRaw("DATE(created_at) >= '{$date_start}'");
            $query->whereRaw("DATE(created_at) <= '{$date_end}'");
        });
    }

    public function scopeOfProduct(Builder $query, string $product_id) {
        $sale_ids = Sale::query()->selectRaw('sales.id as id')
                                 ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
                                 ->where('sale_items.product_id', $product_id)
                                 ->pluck('id');

        $query->whereIn('id', $sale_ids);
    }
}
