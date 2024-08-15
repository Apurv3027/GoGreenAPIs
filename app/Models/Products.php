<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helper\helper;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'product_name',
        'product_price',
        'product_image',
        'product_description',
        'product_category',
        'product_tags',
    ];

    public static function addProduct($request) {
        $data = new Products();
        $data->product_name = $request->product_name;
        $data->product_price = $request->product_price;

        /* For Upload Product pic */
        $productpicname = null;
        if(isset($request->product_image) && $request->product_image !=''){
            $productimg   = $request->product_image;
            $productpicname = 'Product-'.time().'.'.$request->product_image->getClientOriginalExtension();
            $productimg->move(Helper::productFileUploadPath(), $productpicname);
        }

        $data->product_image = $productpicname;
        $data->product_description = $request->product_description;
        $data->product_category = $request->product_category;
        $data->product_tags = $request->product_tags;

        $data->save();

        return $data->toArray();
    }
}
