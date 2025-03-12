<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Category;
use App\Models\ProductImage;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'produits';

    protected $fillable = ['name', 'slug', 'price', 'stock', 'status', 'category_id'];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
