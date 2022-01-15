<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCheckIn extends Model
{
    use HasFactory;
    protected $table = 'user_checkin_stores';
    protected $fillable = [
        'user_id',
        'store_id'
    ];
}
