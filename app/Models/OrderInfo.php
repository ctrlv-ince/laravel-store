<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderInfo extends Model
{
    use HasFactory;

    protected $primaryKey = 'orderinfo_id';
    public $timestamps = true;
    protected $table = 'orderinfos';

    protected $fillable = [
        'order_id',
        'item_id',
        'quantity',
        'created'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
} 