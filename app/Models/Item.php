<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Item extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $primaryKey = 'item_id';
    public $timestamps = true;

    protected $fillable = [
        'item_name',
        'price',
        'item_description'
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'item_id' => $this->item_id,
            'item_name' => $this->item_name,
            'item_description' => $this->item_description,
            'price' => $this->price,
            'groups' => $this->groups->pluck('group_name')->join(' ')
        ];
    }

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