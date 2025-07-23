<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'promotion_type',
        'old_price',
        'description',
        'category_id',
        'stock',
        'status',
        'start_date',
        'end_date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

 public function images()
{
    return $this->hasMany(ProductImage::class, 'product_id', 'id'); // ✅ thêm rõ ràng
}

    public function firstImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id')->oldest();
    }

    public function colors()
    {
        return $this->hasMany(ProductColor::class, 'product_id');
    }
}

