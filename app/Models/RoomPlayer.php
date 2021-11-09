<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomPlayer extends Model
{
    use HasFactory;
    protected $table = 'room_players';
    protected $fillable = [
        'user_id',
        'name',
        'phone'
    ];
}
