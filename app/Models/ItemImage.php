<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemImage extends Model
{
    use HasFactory;

    protected $primaryKey = 'image_id';
    public $timestamps = true;

    protected $fillable = [
        'item_id',
        'image_path',
        'is_primary',
        'uploaded_at'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
} 