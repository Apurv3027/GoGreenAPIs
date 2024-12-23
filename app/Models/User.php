<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Helper\helper;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Address;
use App\Models\Product;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['fullname', 'email', 'password', 'mobile_number', 'selected_address_id', 'street_1', 'street_2', 'city', 'state'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function addUser($request)
    {
        $data = new User();
        $data->fullname = $request->fullname;
        $data->email = $request->email;
        $data->password = Hash::make($request->password);
        $data->mobile_number = $request->mobile_number;
        $data->save();

        return $data->toArray();
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function wishlistItems() {
        return $this->belongsToMany(Product::class, 'wishlists');
    }
}
