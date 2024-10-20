<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'street_1',
        'street_2',
        'city',
        'state',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
