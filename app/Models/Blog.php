<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'content',
        'image_url',
        'link_url',
        'status',
        'author_id',
        'publish_date',
    ];

    public function author()
{
    return $this->belongsTo(Author::class, 'author_id');
}

    public function category()
{
    return $this->belongsTo(Category::class, 'category_id');
}

}
