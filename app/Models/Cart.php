<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $primaryKey = 'cart_id';
    public $timestamps = true;

    protected $fillable = [
        'account_id',
        'item_id',
        'quantity',
        'date_placed'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
} 