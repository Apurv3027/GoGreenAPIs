<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Address;
use App\Models\OrderItem;

class Orders extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = ['user_id', 'address_id', 'order_id', 'total_amount', 'payment_type', 'payment_id', 'order_status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
