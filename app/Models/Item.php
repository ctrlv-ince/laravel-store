<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'item_id';
    public $incrementing = true;
    protected $keyType = 'integer';

    protected $fillable = [
        'item_name',
        'item_description',
        'price',
    ];

    protected $dates = ['deleted_at'];

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