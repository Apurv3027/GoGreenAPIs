<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helper\helper;


class Banner extends Model
{
    use HasFactory;

    protected $table = 'banners';

    protected $fillable = [
        'banner_name',
        'banner_description',
        'banner_image_url',
    ];
}
