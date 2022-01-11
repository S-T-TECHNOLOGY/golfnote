<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'user_notifications';
    protected $fillable = [
        'user_id',
        'type',
        'golf_id',
        'event_id',
        'request_friend_id',

    ];

    public function golf()
    {
        return $this->belongsTo(Golf::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
