<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $primaryKey = 'account_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'username',
        'password',
        'role',
        'profile_img',
        'account_status'
    ];

    protected $hidden = [
        'password'
    ];

    /**
     * Check if the account has admin role
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'account_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'account_id');
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'account_id');
    }
} 