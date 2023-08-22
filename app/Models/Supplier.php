<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'last_purchased_at'
    ];

    public function purchases() : HasMany {
        return $this->hasMany(Purchase::class);
    }

    public function last_order() {
        return $this->hasOne(Purchase::class)->ofMany('created_at', 'max');
    }

}
