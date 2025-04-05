<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $primaryKey = 'item_id';
    public $timestamps = true;

    protected $fillable = [
        'quantity'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
} 