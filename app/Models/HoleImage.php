<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoleImage extends Model
{
    use HasFactory;
    protected $table = 'hole_images';
    protected $fillable = [
        'golf_id',
        'image',
        'course',
        'number_hole',
        'standard'
    ];
}
