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
        'phone',
        'address',
        'description',
        'time_start',
        'time_close',
        'golf_courses'
    ];
}
