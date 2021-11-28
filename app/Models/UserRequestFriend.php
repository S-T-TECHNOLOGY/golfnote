<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRequestFriend extends Model
{
    use HasFactory;
    protected $table = 'user_request_friends';
    protected $fillable = [
        'sender_id',
        'received_id',
        'request_content',
        'status'
    ];
}
