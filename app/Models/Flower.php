<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flower extends Model
{
    //
    protected $fillable = [
        'name',
        'image_url',
        'color',
        'quantity',

    ];

    public function bouquets(): BelongsToMany
    {
        return $this->belongsToMany(Bouquet::class,'bouquet_flower')
            ->using(BouquetFlower::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

}
