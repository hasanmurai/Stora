<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'photo',
        'description',
        'price',
        'stock',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}