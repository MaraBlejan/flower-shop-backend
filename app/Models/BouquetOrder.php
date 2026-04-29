<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BouquetOrder extends Pivot
{
    //
    protected $table = 'bouquet_order';
    public $incrementing = true;
    protected $fillable = [
        'order_id',
        'bouquet_id',
        'quantity',
        'price',
    ];
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }
    public function order():BelongsTo{
        return $this->belongsTo(Order::class);
    }
    public function bouquet(): BelongsTo {
        return $this->belongsTo(Bouquet::class);
    }
}
