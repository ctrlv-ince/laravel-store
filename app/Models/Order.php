<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';
    public $timestamps = true;

    protected $fillable = [
        'account_id',
        'date_ordered',
        'total_amount',
        'status'
    ];

    protected $casts = [
        'date_ordered' => 'datetime',
        'total_amount' => 'decimal:2'
    ];

    protected $dates = [
        'date_ordered'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function orderInfos()
    {
        return $this->hasMany(OrderInfo::class, 'order_id');
    }
} 