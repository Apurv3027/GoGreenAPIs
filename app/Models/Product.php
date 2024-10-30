<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helper\helper;
use App\Models\Category;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = ['product_name', 'product_price', 'product_image_url', 'product_description', 'product_category', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
