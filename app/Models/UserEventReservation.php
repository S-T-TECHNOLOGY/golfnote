<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEventReservation extends Model
{
    use HasFactory;
    protected $table = 'user_event_reservations';
    protected $fillable = [
        'user_id',
        'event_id',
        'user_name',
        'phone',
        'email',
        'note',
        'status'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
