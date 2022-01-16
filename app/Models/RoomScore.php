<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomScore extends Model
{
    use HasFactory;
    protected $table = 'room_scores';
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'infor',
        'score',
        'room_id',
        'created_at',
        'updated_at'
    ];
}
