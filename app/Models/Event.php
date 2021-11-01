<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'image',
        'start_date',
        'end_date',
        'quantity',
        'quantity_remain',
        'join_fee',
        'host',
        'organizational_unit',
        'caddie_fee',
        'green_fee'
    ];
}
