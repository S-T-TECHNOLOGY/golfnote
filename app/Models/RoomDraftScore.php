<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomDraftScore extends Model
{
    use HasFactory;
    protected $table = 'room_draft_scores';
    protected $fillable = [
        'room_id',
        'infor',
        'hole_current'
    ];
}
