<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bouquet extends Model
{
    //
    protected $fillable=[
        'name',
        'description',
        'is_available',
        'price',
        'image_url',
        'category_id',
    ];

    public function flowers(): BelongsToMany {
        return $this->belongsToMany(Flower::class, 'bouquet_flower')
            ->using(BouquetFlower::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function orders(): BelongsToMany {
        return $this->belongsToMany(Order::class, 'bouquet_order')
        ->using(BouquetOrder::class)
        ->withPivot(['quantity', 'price'])
            ->withTimestamps();
    }
    public function category():BelongsTo{
        return $this->belongsTo(Category::class);

    }
}
