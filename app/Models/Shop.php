<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Shop extends Model
{
    protected $fillable = ['name', 'slug', 'photo', 'description'];
    protected $hidden = ['created_at', 'updated_at'];
    protected static function booted()
    {
        static::saving(function ($shop) {
            // Only regenerate the slug if the name has changed (or if it's new)
            if ($shop->isDirty('name')) {
                $shop->slug = Str::slug($shop->name);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
