<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $primaryKey = 'item_id';
    public $timestamps = true;

    protected $fillable = [
        'item_name',
        'price',
        'item_description',
        'date_added'
    ];

    public function images()
    {
        return $this->hasMany(ItemImage::class, 'item_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'item_groups', 'item_id', 'group_id');
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'item_id');
    }

    public function orderInfos()
    {
        return $this->hasMany(OrderInfo::class, 'item_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'item_id');
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'item_id');
    }
} 