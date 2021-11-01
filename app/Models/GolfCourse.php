<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GolfCourse extends Model
{
    use HasFactory;
    protected $table = 'golf_courses';
    protected $fillable = [
        'image',
        'phone',
        'address',
        'description',
        'time_start',
        'time_close'
    ];
}
