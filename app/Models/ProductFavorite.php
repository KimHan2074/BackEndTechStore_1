<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFavorite extends Model
{
    use HasFactory;

    protected $table = 'product_favorite';

    protected $fillable = [
        'user_id',
        'product_id',
        'color',
    ];

    /**
     * Quan hệ với model User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ với model Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
