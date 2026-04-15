<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


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

    protected static function booted()
    {
        static::saving(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = Str::slug($product->name) . '-' . Str::random(5);
            }
        });

        static::deleted(function ($product) {
            if ($product->photo) {
                Storage::disk('public')->delete($product->photo);
            }
        });
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function category() {
    return $this->belongsTo(Category::class);
}
}