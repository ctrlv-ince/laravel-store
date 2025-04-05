<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $primaryKey = 'group_id';
    public $timestamps = true;

    protected $fillable = [
        'group_name'
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_groups', 'group_id', 'item_id');
    }
} 