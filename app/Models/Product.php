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
    protected $hidden = ['created_at', 'updated_at'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}