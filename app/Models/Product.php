<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'title_slug',
        'price',
        'new_price',
        'categories_id',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id','id');
    }
}
