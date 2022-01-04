<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Golf extends Model
{
    use HasFactory;
    protected $table = 'golfs';
    protected $fillable = [
        'image',
        'name',
        'price',
        'phone',
        'address',
        'description',
        'time_start',
        'time_close',
        'golf_courses',
        'number_hole',
        'is_open'
    ];
    public function holes()
    {
        return $this->hasMany(HoleImage::class);
    }
}
