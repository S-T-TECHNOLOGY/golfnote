<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReservation extends Model
{
    use HasFactory;
    protected $table = 'user_golf_reservations';
    protected $fillable = [
        'user_id',
        'golf_id',
        'user_name',
        'phone',
        'email',
        'date',
        'total_player',
        'status'
    ];

    public function golf()
    {
        return $this->belongsTo(Golf::class);
    }
}
