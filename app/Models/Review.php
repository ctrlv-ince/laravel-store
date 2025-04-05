<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $primaryKey = 'review_id';
    public $timestamps = true;

    protected $fillable = [
        'account_id',
        'item_id',
        'comment',
        'rating',
        'create_at',
        'update_at'
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