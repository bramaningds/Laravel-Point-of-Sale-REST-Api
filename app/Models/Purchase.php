<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'supplier_id'
    ];

    protected $with = [
        'user', 'supplier', 'items'
    ];

    public function getTotalAttribute() : Float {
        $items = $this->items->toArray() ?: [];

        $subtotal = array_reduce($items, function($carry, $item) {
            return $carry + ($item['pivot']['quantity'] * $item['pivot']['price']);
        }, 0);

        $total = $subtotal;

        return $total;
    }

    public function supplier() : BelongsTo {
        return $this->belongsTo(Supplier::class);
    }

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function items() : BelongsToMany {
        return $this->belongsToMany(Product::class, 'purchase_items')
                    ->using(PurchaseItem::class)
                    ->withPivot('quantity', 'price');
    }

    public function scopeSearch(Builder $query, string $keyword) {
        $query->whereIn('id', Purchase::query()->selectRaw('DISTINCT(purchases.id) as id')
              ->join('users', 'users.id', '=', 'user_id')
              ->join('suppliers', 'suppliers.id', '=', 'supplier_id')
              ->join('purchase_items', 'purchases.id', '=', 'purchase_items.purchase_id')
              ->join('products', 'products.id', '=', 'purchase_items.product_id')
              ->whereRaw('FALSE')
              ->orWhere('users.name', 'like', "%{$keyword}%")
              ->orWhere('suppliers.name', 'like', "%{$keyword}%")
              ->orWhere('products.name', 'like', "%{$keyword}%")
              ->pluck('id'));
    }

    public function scopeOfUser(Builder $query, string $user_id) {
        $query->where('user_id', $user_id);
    }

    public function scopeOfSupplier(Builder $query, string $supplier_id) {
        $query->where('supplier_id', $supplier_id);
    }

    public function scopeOfDate(Builder $query, string $date_start, string $date_end) {
        $query->where(function($query) use ($date_start, $date_end) {
            $query->whereRaw("DATE(created_at) >= '{$date_start}'");
            $query->whereRaw("DATE(created_at) <= '{$date_end}'");
        });
    }

    public function scopeOfProduct(Builder $query, string $product_id) {
        $purchase_ids = Purchase::query()->selectRaw('purchases.id as id')
                                 ->join('purchase_items', 'purchases.id', '=', 'purchase_items.purchase_id')
                                 ->where('purchase_items.product_id', $product_id)
                                 ->pluck('id');

        $query->whereIn('id', $purchase_ids);
    }
}
