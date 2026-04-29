<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BouquetFlower extends Pivot
{
    //
    protected $table = 'bouquet_flowers';
    public $incrementing=true;
    protected $fillable=[
        'flower_id',
        'bouquet_id',
        'quantity',
    ];
    public function flower():BelongsTo{
        return $this->belongsTo(Flower::class);
    }
    public function bouquet():BelongsTo{
        return $this->belongsTo(Bouquet::class);
    }
}
