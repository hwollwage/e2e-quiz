<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = [
        'name',
        'description', 
        'point_cost', 
        'category',
        'image', 
        'is_featured', 
        'is_active', 
        'stock',
    ];
}
